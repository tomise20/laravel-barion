# A simple Laravel package for Barion Smart Gateway

### **Connect your webshop to the Barion Smart Gateway in just 5 minutes.**

### **Development currently in progress!**

Tomise/Laravel-Barion is provides an easy way to use the Barion API with Laravel applications.Support Barion Smart Gateway operation and Barion Wallet operations.

## Installation

1. Install the package using composer:

`composer require tomise/laravel-barion`

2. Register the service provider in the `app.php` config file

```php
Tomise\Barion\Providers\BarionServiceProvider::class,
```

3. Publish config file (optional)

```php
php artisan vendor:publish --provider="Tomise\Barion\Providers\BarionServiceProvider" --tag="config"
```

## Configuration

Package has a pre-configuration, but you set some data your .env file:
Configuration details in config/barion-gateway.php

```php
#required:
BARION_POST_KEY=<your pos key>
BARION_REDIRECT_URL=<your default redirect url>
BARION_CALLBACK_URL=<your default callback url>

#optional:
# only need for wallet operations
BARION_API_KEY=<your wallet api key>
BARION_PAYEE=<payee email>

#others:
BARION_ENVIRONMENT=<default test>
BARION_GUEST_CHECKOUT=<default true>
BARION_PAYMENT_TYPE=<default Immediate>
BARION_PAYMENT_WINDOW=<default 00:30:00>
```

If your `BARION_ENVIRONMENT` variable value is "test" every request send to sandbox server.

### Documentation for Smart Gateway

## There are 2 ways to use the BarionGateway (model options only available for payment start operation).

### **Usage with models**

**The $barion_casts property should be a key-value pair based array, the keys are given and the values are the fields of the model you want to pass to the Barion.**

If you want to use Barion with your models only needs some simple step:

1. Add `public $barion_casts = []` you Order or Cart or any class that extends from Model.

   1. Required keys: payment_request_id, payer_hint, payer_hint, order_number, phone_number, total

Example

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    ...other properties

    public $barion_casts = [
		'payment_request_id' => 'id',
		'payer_hint' => 'email',
		'order_number' => 'reservation_number',
		'phone_number' => 'phone_number',
		'total' => 'total_price',
	];

    ...other methods
}
```

2. Add `public $barion_casts = []` you OrderItem or CartItem or any class that also extends from Model.
   1. Required keys: name, description, quantity, unit, unit_price, item_total

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    ...other properties
    public $barion_casts = [
		"name" => "name",
		"description" => "description",
		"quantity" => "quantity",
		"unit" => "unit",
		"unit_price" => "unit_price",
		"item_total" => "item_total",
	];

    ...other methods
}
```

If you want to use this version, but you are missing one or more properties of a model, just attach it before passing it to the startPayment method or you can create a custom model directly for Barion e.g.: BarionOrderModel

#### Start single payment

```php
<?php

use Tomise\Barion\Support\BarionGateway;

...

// Get payment gateway.
$gateway = BarionGateway::createPaymentGateway();

// Build barion payment data you models
$preparedPayment = $gateway->startPayment($order, $items);

// Send data to Barion
$response = $preparedPayment->sendSinglePayment();

// Redirect user to the Barion Gateway
return redirect($response->getGatewayUrl());
```

**startPayment** method only assign minimal data to [Payment/Start](https://docs.barion.com/Payment-Start-v2) endpoint. But if you want to add some extra data just set before you call the **sendSinglePayment**.

Example

```php
// Get payment gateway.
$gateway = BarionGateway::createPaymentGateway();

// Build barion payment data from your models
$preparedPayment = $gateway->startPayment($order, $items);
$paymentDto = $preparedPayment->getPaymentDto();
// Add billing address before send the request.
$paymentDto->setBillingAddress();
// Send data to Barion
$response = $preparedPayment->sendSinglePayment();
```

#### Different Locale and Currency

If you want to use the "model option" but are missing the locale or the currency from your "order" model, you can simply overwrite the default values.

**Important:** Changing the currency does not automatically convert the values, please note this!

```php
...
$preparedPayment = $gateway->startPayment($order, $items);
$paymentDto = $preparedPayment->getPaymentDto();
// Modify the default currency and locale
$paymentDto->setCurrency(Currency::Huf);
$paymentDto->setLocale(Locale::Hu);
$response = $preparedPayment->sendSinglePayment();
```

### **Simple manual usage**

#### Payment Start

In this case you will get an empty "PreparedPaymentService" object where you can set everything. **The POSKey is automatically attached to the object.**

```php
<?php

use Tomise\Barion\Support\BarionGateway;
use Tomise\Barion\DataTransferObjects\Currency;
use Tomise\Barion\DataTransferObjects\Locale;

...
// Get payment gateway.
$gateway = BarionGateway::createPaymentGateway();

$preparedPayment = $gateway->startPaymentManual();

$paymentDto = $preparedPayment->getPaymentDto();
$paymentDto->setTransactions($transactions);
$paymentDto->setCurrency(Currency::Huf);
$paymentDto->setLocale(Locale::Hu);
...
$response = $preparedPayment->sendSinglePayment();
```

#### Get Payment Status

```php
// Get payment gateway.
$gateway = BarionGateway::createPaymentGateway();

$preparedPayment = $gateway->startPaymentManual();
$response = $preparedPayment->sendPaymentState($paymentId);
```

#### Cancel Authorization

```php
// Get payment gateway.
$gateway = BarionGateway::createPaymentGateway();

$preparedPayment = $gateway->startPaymentManual();
// Set required parameter
$preparedPayment->setPaymentId($paymentId);
$response = $preparedPayment->sendCancelAuthorization($paymentId);
```

## Information

**Available gateway endpoints:**

- sendSinglePayment() -> Payment/Start
- sendPaymentState() -> Payment/:paymentId/paymentstate
- sendCompletePayment() -> Payment/Complete
- sendFinishReservation() -> Payment/FinishReservation
- sendCapture() -> Payment/Capture
- sendCancelAuthorization() -> Payment/CancelAuthorization
- sendRefund() -> Payment/Refund

**Available Locales:** [https://docs.barion.com/Localisation](https://docs.barion.com/Localisation)

**Available Currencies:**[https://docs.barion.com/Supported_currencies](https://docs.barion.com/Supported_currencies)

### Documentation for Barion Wallet

[Barion Wallet documentation](docs/wallet.md)

### Examples

- [Payment example with model](examples/payment-width-models/)
- [Manual payment example](examples/manual-payment/)

## License

Laravel-Barion is open source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
