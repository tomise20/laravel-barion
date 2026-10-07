# A simple Laravel package for Barion Smart Gateway

Tomise/Laravel-Barion provides an easy way to use the Barion API in Laravel applications (10–13): Smart Gateway
payments (immediate, reservation, delayed capture, recurring), payment state, refund, and Barion Wallet operations.

## Installation

1. Install the package using composer:

```bash
composer require tomise/laravel-barion
```

From a local checkout, add a path repository to the application's `composer.json` first:

```json
"repositories": [
    { "type": "path", "url": "../libs/barion" }
]
```

2. The service provider is registered automatically (package discovery). Without discovery, add
   `Tomise\Barion\Providers\BarionServiceProvider::class` to the providers.

3. Publish the config file (optional):

```bash
php artisan vendor:publish --provider="Tomise\Barion\Providers\BarionServiceProvider" --tag="config"
```

## Configuration

Details in `config/barion-gateway.php`.

```dotenv
# required
BARION_POS_KEY=<your POS key>            # BARION_POST_KEY (the old name) is still read
BARION_PAYEE=<the e-mail of your Barion wallet>
BARION_REDIRECT_URL=<where the customer returns after paying>
BARION_CALLBACK_URL=<where Barion reports the changes of a payment>

# optional
BARION_ENVIRONMENT=test                  # "prod" sends to the live API, anything else to the sandbox
BARION_PAYMENT_TYPE=Immediate            # Immediate | Reservation | DelayedCapture
BARION_PAYMENT_WINDOW=00:30:00           # how long the customer can pay
BARION_RESERVATION_PERIOD=7.00:00:00     # Reservation: time to finish the payment (max. 1 year)
BARION_DELAYED_CAPTURE_PERIOD=7.00:00:00 # DelayedCapture: time to capture (max. 7 days, 21 for Hungarian shops)
BARION_GUEST_CHECKOUT=true
BARION_TIMEOUT=30                        # seconds
BARION_API_URL=                          # override of the API address, e.g. a local fake server for tests
BARION_GATEWAY_URL=                      # override of the payment page address (".../Pay")

# only for wallet operations
BARION_API_KEY=<your wallet API key>
```

## Smart Gateway

Every request method throws:

- `Tomise\Barion\Exceptions\BarionPaymentException` when Barion rejects the request. The message is Barion's
  (`Title: Description`), `getErrors()` has every error, `getErrorCode()` the first error code, `getCode()` the HTTP status.
- `Tomise\Barion\Exceptions\BarionConnectionException` when Barion cannot be reached.

### Start a payment

```php
use Tomise\Barion\DataTransferObjects\PaymentTransactionDto;
use Tomise\Barion\DataTransferObjects\TransactionItemDto;
use Tomise\Barion\Support\BarionGateway;

$payment = BarionGateway::createPaymentGateway()->startPaymentManual();

$payment->getPaymentData()
    ->setPaymentRequestId('ORDER-1001')      // your unique id (a UUID is generated when missing)
    ->setOrderNumber('ORDER-1001')
    ->setPayerHint('customer@example.com')
    ->setPayerPhoneNumber('+36 30 123 4567') // sent as 36301234567
    ->addTransaction(
        (new PaymentTransactionDto)
            ->setPostTransactionId('ORDER-1001')
            ->setPayee(config('barion-gateway.payee'))
            ->setTotal(16500)
            ->setItems([
                (new TransactionItemDto)
                    ->setName('Sports massage')
                    ->setDescription('60 minutes')
                    ->setQuantity(1)
                    ->setUnit('pcs')
                    ->setUnitPrice(16500),   // ItemTotal defaults to quantity × unit price
            ])
    );

$response = $payment->sendSinglePayment();

// Store these with the order: the callback and the refund need them.
$response->getPaymentId();
$response->getTransactionId();

return redirect($response->getGatewayUrl());
```

`startPaymentManual()` fills the configured defaults (payment type, periods, payment window, guest checkout, funding
sources, redirect and callback URL, locale, currency). Amounts are rounded to the currency's decimals (HUF: 0).

#### Usage with models

The models need a `$barion_casts` property: the keys are given, the values are the model's attributes.

```php
class Order extends Model
{
    public $barion_casts = [
        'total' => 'total_price',            // required
        'payment_request_id' => 'reference', // a UUID is generated when missing
        'payer_hint' => 'email',
        'order_number' => 'reference',
        'phone_number' => 'phone',
        // 'card_holder_name_hint' => 'name',
        // 'payee' => 'payee_email',         // defaults to BARION_PAYEE
    ];
}

class OrderItem extends Model
{
    public $barion_casts = [
        'name' => 'name',
        'description' => 'description',
        'quantity' => 'quantity',
        'unit' => 'unit',
        'unit_price' => 'unit_price',
        'item_total' => 'item_total',
    ];
}
```

```php
$payment = BarionGateway::createPaymentGateway()->startPayment($order, $order->items);
// Extra data before sending, e.g. $payment->getPaymentData()->setBillingAddress($address);
$response = $payment->sendSinglePayment();
```

Locale and currency come from the config; to change them: `$payment->getPaymentData()->setCurrency(Currency::Eur)`.
**Changing the currency does not convert the amounts.**

### Callback: the state of a payment

Barion only posts the `paymentId` to the callback URL; query the state (v4 PaymentState):

```php
$state = BarionGateway::createPaymentGateway()->startPaymentManual()->sendPaymentState($request->input('paymentId'));

$state->isSucceeded();              // Status === Succeeded
$state->status;                     // BarionStatus enum
$state->paymentRequestId;           // your id
$state->transaction('ORDER-1001');  // the customer's transaction of a POSTransactionId (no fees, no reversed ones)
$state->completedAt;                // DateTimeImmutable
```

Answer the callback with HTTP 200, Barion retries otherwise.

### Refund

Full or partial, per transaction (the `TransactionId` from the start or the state response):

```php
use Tomise\Barion\DataTransferObjects\TransactionToRefundDto;

$response = BarionGateway::createPaymentGateway()->startPaymentManual()->sendRefund(
    $paymentId,
    [new TransactionToRefundDto($transactionId, 'ORDER-1001', 5000, 'Partial refund')],
    idempotencyKey: 'refund-ORDER-1001-1', // a retried request with the same key is refunded only once (sent as a GUID)
);

$response->refundedTransactions; // Collection of RefundedTransactionDto
```

### Reservation: charge later, finish with the final amount

The amount is charged at once and held until the payment is finished (`BARION_RESERVATION_PERIOD`, max. 1 year). A
lower final total refunds the rest, 0 refunds everything. Transactions not finished in time are refunded to the
customer (status `Expired`, or `PartiallySucceeded` when some were finished).

```php
use Tomise\Barion\DataTransferObjects\TransactionToFinishDto;
use Tomise\Barion\Enums\PaymentType;

$payment = BarionGateway::createPaymentGateway()->startPaymentManual();
$payment->getPaymentData()->setPaymentType(PaymentType::Reservation)->setReservationPeriod('3.00:00:00') /* ... */;
$response = $payment->sendSinglePayment();

// later
BarionGateway::createPaymentGateway()->startPaymentManual()
    ->sendFinishReservation($paymentId, [new TransactionToFinishDto($transactionId, 12000)]);
```

### Delayed capture: authorize now, capture later

The amount is only blocked on the card (`BARION_DELAYED_CAPTURE_PERIOD`, max. 7 days, 21 days for Hungarian shops)
and charged on capture. An uncaptured authorization is released when the period expires (status `Expired`). Card
payments only; the card issuer can release the block earlier, then the capture fails.

```php
$payment->getPaymentData()->setPaymentType(PaymentType::DelayedCapture) /* ... */;

// charge (at most the authorized amount)
$client->sendCapture($paymentId, [new TransactionToFinishDto($transactionId, 16500, 'Treatment done')]);

// or release without charging
$client->sendCancelAuthorization($paymentId);
```

### Token payments: save the card, charge it later

The first payment is made by the customer on the Barion page and saves the card under your `RecurrenceId` (3DS
authentication happens here). Later payments with the same `RecurrenceId` are charged without the customer: the
response is already `Succeeded`, or Barion's error (e.g. `CardExpired`) is thrown. A premium feature: it has to be
requested from Barion. Barion does not schedule the charges: call the API when a charge is due (e.g. from a scheduled
command).

| `RecurrenceType` | When | Amount |
|---|---|---|
| `MerchantInitiatedPayment` | irregular charges without the customer (e.g. on a date, a no-show fee) | any; the first payment can be 0 (card registration only) |
| `RecurringPayment` | regular charges without the customer (subscription) | at most the first amount; needs `PurchaseInformation` RecurringFrequency and RecurringExpiry |
| `OneClickPayment` | the customer pays again on your site | any; 3DS on every payment |

The customer must clearly consent to the later charges: the Barion page does not show that the card is saved.

```php
use Tomise\Barion\Enums\RecurrenceType;

// 1. Registration, by the customer (here without charging anything)
$payment->getPaymentData()
    ->setInitiateRecurrence(true)
    ->setRecurrenceId('guest-42-'.Str::uuid())  // unique per registration; store it
    ->setRecurrenceType(RecurrenceType::MerchantInitiatedPayment);

// 2. In the callback: store the TraceId (and the card for display)
$state = $client->sendPaymentState($paymentId);
$state->traceId;                       // required for the later charges, store it unchanged
$state->fundingInformation?->bankCard; // masked PAN, card type, expiry

// 3. Later, without the customer
$charge->getPaymentData()
    ->setRecurrenceId($recurrenceId)
    ->setRecurrenceType(RecurrenceType::MerchantInitiatedPayment) // the same type as at registration
    ->setTraceId($traceId);
$response = $charge->sendSinglePayment(idempotencyKey: 'charge-booking-1001');
$response->getStatus();           // BarionStatus::Succeeded
$response->getRecurrenceResult(); // Successful, Failed, NotFound, ThreeDSAuthenticationRequired (TraceId missing)
```

When a charge fails, Barion suggests checking `FundingInformation->ProcessResult` in the payment state: do not retry
`LostOrStolenCard`, `FraudulentTransaction`, `CardNotSupported` or `ThreeDsNotEnabled`. Retry the others later
(at least a day apart, at most 5 times), then ask the customer to register a card again.

### Follow-up requests

The follow-up methods take the `PaymentId` as a parameter, or use the one set with `setPaymentId()`:

```php
$client = BarionGateway::createPaymentGateway()->startPaymentManual()->setPaymentId($paymentId);
$client->sendCancelAuthorization();
```

| Method | Endpoint |
|---|---|
| `sendSinglePayment(?idempotencyKey)` | `POST v2/Payment/Start` |
| `sendPaymentState(paymentId)` | `GET v4/Payment/{paymentId}/PaymentState` |
| `sendRefund(?paymentId, transactions, ?idempotencyKey)` | `POST v2/Payment/Refund` |
| `sendFinishReservation(?paymentId, transactions)` | `POST v2/Payment/FinishReservation` |
| `sendCapture(?paymentId, transactions)` | `POST v2/Payment/Capture` |
| `sendCancelAuthorization(?paymentId)` | `POST v2/Payment/CancelAuthorization` |
| `sendCompletePayment(?paymentId)` | `POST v2/Payment/Complete` |

**Available Locales:** [https://docs.barion.com/Localisation](https://docs.barion.com/Localisation)

**Available Currencies:** [https://docs.barion.com/Supported_currencies](https://docs.barion.com/Supported_currencies)

### Testing your application

`BarionGateway` resolves the service from the container, so the HTTP client can be replaced:

```php
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tomise\Barion\Adapters\BarionAdapter;

$mock = new MockHandler([new Response(200, [], json_encode(['PaymentId' => 'test-payment', 'Status' => 'Prepared', 'Transactions' => []]))]);
$this->app->instance(BarionAdapter::class, new BarionAdapter(new Client(['handler' => HandlerStack::create($mock), 'http_errors' => false])));
```

## Barion Wallet

[Barion Wallet documentation](docs/wallet.md)

## Examples

- [Payment example with models](examples/payment-width-models/)
- [Manual payment, callback and refund example](examples/manual-payment/)

## License

Laravel-Barion is open source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
