<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use BackedEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Tomise\Barion\Enums\PaymentType;
use Tomise\Barion\Enums\RecurrenceType;

/**
 * The request of Payment/Start (v2). The POSKey is sent in the x-pos-key header, not in the body.
 */
class BarionPaymentDto implements Arrayable
{
    private string $paymentType = 'Immediate';
    private ?string $reservationPeriod = null;
    private ?string $delayedCapturePeriod = null;
    private string $paymentWindow = '00:30:00';
    private bool $guestCheckOut = true;
    private array $fundingSources = ['All'];
    private ?string $paymentRequestId = null;
    private ?string $payerHint = null;
    private ?string $cardHolderNameHint = null;
    private bool $initiateRecurrence = false;
    private ?string $recurrenceId = null;
    private ?string $recurrenceType = null;
    private ?string $traceId = null;
    private ?AddressDto $shippingAddress = null;
    private ?AddressDto $billingAddress = null;
    private ?string $redirectUrl = null;
    private ?string $callbackUrl = null;
    private array $transactions = [];
    private ?string $orderNumber = null;
    private Locale $locale = Locale::Hu;
    private Currency $currency = Currency::Huf;
    private ?string $payerPhoneNumber = null;
    private ?string $payerWorkPhoneNumber = null;
    private ?string $payerHomePhoneNumber = null;
    private ?PurchaseInformation $purchaseInformation = null;

    /**
     * Not part of Payment/Start: kept for the follow-up requests of the same payment.
     */
    private ?string $paymentId = null;

    public function __construct(private readonly string $posKey) {}

    public function getPosKey(): string
    {
        return $this->posKey;
    }

    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string|PaymentType $paymentType): BarionPaymentDto
    {
        $this->paymentType = $paymentType instanceof PaymentType ? $paymentType->value : $paymentType;

        return $this;
    }

    public function getReservationPeriod(): ?string
    {
        return $this->reservationPeriod;
    }

    /**
     * How long a Reservation payment can be finished, "d.hh:mm:ss" (e.g. "7.00:00:00"); required for Reservation.
     */
    public function setReservationPeriod(?string $reservationPeriod): BarionPaymentDto
    {
        $this->reservationPeriod = $reservationPeriod;

        return $this;
    }

    public function getDelayedCapturePeriod(): ?string
    {
        return $this->delayedCapturePeriod;
    }

    /**
     * How long a DelayedCapture payment can be captured, "d.hh:mm:ss" (at most 7 days); required for DelayedCapture.
     */
    public function setDelayedCapturePeriod(?string $delayedCapturePeriod): BarionPaymentDto
    {
        $this->delayedCapturePeriod = $delayedCapturePeriod;

        return $this;
    }

    public function getPaymentWindow(): string
    {
        return $this->paymentWindow;
    }

    public function setPaymentWindow(string $paymentWindow): BarionPaymentDto
    {
        $this->paymentWindow = $paymentWindow;

        return $this;
    }

    public function getGuestCheckout(): bool
    {
        return $this->guestCheckOut;
    }

    public function isGuestCheckOut(): bool
    {
        return $this->guestCheckOut;
    }

    public function setGuestCheckout(bool $guestCheckOut): BarionPaymentDto
    {
        $this->guestCheckOut = $guestCheckOut;

        return $this;
    }

    public function getFundingSources(): array
    {
        return $this->fundingSources;
    }

    public function setFundingSources(array $fundingSources): BarionPaymentDto
    {
        $this->fundingSources = $fundingSources;

        return $this;
    }

    public function getPaymentRequestId(): ?string
    {
        return $this->paymentRequestId;
    }

    public function setPaymentRequestId(string $paymentRequestId): BarionPaymentDto
    {
        $this->paymentRequestId = $paymentRequestId;

        return $this;
    }

    public function getPayerHint(): ?string
    {
        return $this->payerHint;
    }

    public function setPayerHint(?string $payerHint): BarionPaymentDto
    {
        $this->payerHint = $payerHint;

        return $this;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(string $url): BarionPaymentDto
    {
        $this->redirectUrl = $url;

        return $this;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(string $url): BarionPaymentDto
    {
        $this->callbackUrl = $url;

        return $this;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @param  array<int, PaymentTransactionDto|array>|Collection<int, PaymentTransactionDto|array>  $transactions
     */
    public function setTransactions(array|Collection $transactions): BarionPaymentDto
    {
        $this->transactions = Collection::make($transactions)->values()->all();

        return $this;
    }

    public function addTransaction(PaymentTransactionDto|array $transaction): BarionPaymentDto
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): BarionPaymentDto
    {
        $this->orderNumber = $orderNumber;

        if (config('barion-gateway.sync_payment_request_id')) {
            $this->setPaymentRequestId($orderNumber);
        }

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale->value;
    }

    public function setLocale(Locale $locale): BarionPaymentDto
    {
        $this->locale = $locale;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency->value;
    }

    public function getCurrencyEnum(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): BarionPaymentDto
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @deprecated Barion has no PhoneNumber field, use setPayerPhoneNumber().
     */
    public function getPhoneNumber(): ?string
    {
        return $this->payerPhoneNumber;
    }

    /**
     * @deprecated Barion has no PhoneNumber field, use setPayerPhoneNumber().
     */
    public function setPhoneNumber(?string $phoneNumber): BarionPaymentDto
    {
        return $this->setPayerPhoneNumber($phoneNumber);
    }

    public function getBillingAddress(): ?AddressDto
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?AddressDto $billingAddress): BarionPaymentDto
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): BarionPaymentDto
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function getCardHolderNameHint(): ?string
    {
        return $this->cardHolderNameHint;
    }

    public function setCardHolderNameHint(?string $cardHolderNameHint): BarionPaymentDto
    {
        $this->cardHolderNameHint = $cardHolderNameHint;

        return $this;
    }

    public function getInitiateRecurrence(): bool
    {
        return $this->initiateRecurrence;
    }

    /**
     * True on the first payment of a recurring (token) payment, paid by the customer on the Barion page.
     */
    public function setInitiateRecurrence(bool $initiateRecurrence): BarionPaymentDto
    {
        $this->initiateRecurrence = $initiateRecurrence;

        return $this;
    }

    public function getRecurrenceId(): ?string
    {
        return $this->recurrenceId;
    }

    /**
     * Your identifier of the saved card (token); the later payments with the same id are charged without the customer.
     */
    public function setRecurrenceId(?string $recurrenceId): BarionPaymentDto
    {
        $this->recurrenceId = $recurrenceId;

        return $this;
    }

    public function getRecurrenceType(): ?string
    {
        return $this->recurrenceType;
    }

    public function setRecurrenceType(string|RecurrenceType|null $recurrenceType): BarionPaymentDto
    {
        $this->recurrenceType = $recurrenceType instanceof RecurrenceType ? $recurrenceType->value : $recurrenceType;

        return $this;
    }

    public function getTraceId(): ?string
    {
        return $this->traceId;
    }

    /**
     * The TraceId of the first payment of a recurring payment, required for the merchant initiated ones.
     */
    public function setTraceId(?string $traceId): BarionPaymentDto
    {
        $this->traceId = $traceId;

        return $this;
    }

    public function getShippingAddress(): ?AddressDto
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?AddressDto $shippingAddress): BarionPaymentDto
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getPayerPhoneNumber(): ?string
    {
        return $this->payerPhoneNumber;
    }

    /**
     * Barion expects digits only with the country code, e.g. "36301234567"; other characters are removed.
     */
    public function setPayerPhoneNumber(?string $payerPhoneNumber): BarionPaymentDto
    {
        $this->payerPhoneNumber = self::normalizePhone($payerPhoneNumber);

        return $this;
    }

    public function getPayerWorkPhoneNumber(): ?string
    {
        return $this->payerWorkPhoneNumber;
    }

    public function setPayerWorkPhoneNumber(?string $payerWorkPhoneNumber): BarionPaymentDto
    {
        $this->payerWorkPhoneNumber = self::normalizePhone($payerWorkPhoneNumber);

        return $this;
    }

    public function getPayerHomePhoneNumber(): ?string
    {
        return $this->payerHomePhoneNumber;
    }

    public function setPayerHomePhoneNumber(?string $payerHomePhoneNumber): BarionPaymentDto
    {
        $this->payerHomePhoneNumber = self::normalizePhone($payerHomePhoneNumber);

        return $this;
    }

    public function getPurchaseInformation(): ?PurchaseInformation
    {
        return $this->purchaseInformation;
    }

    public function setPurchaseInformation(?PurchaseInformation $purchaseInformation): BarionPaymentDto
    {
        $this->purchaseInformation = $purchaseInformation;

        return $this;
    }

    /**
     * The JSON body of Payment/Start, in Barion's field names.
     */
    public function toArray(): array
    {
        $data = [
            'PaymentType' => $this->paymentType,
            'ReservationPeriod' => $this->paymentType === PaymentType::Reservation->value ? $this->reservationPeriod : null,
            'DelayedCapturePeriod' => $this->paymentType === PaymentType::DelayedCapture->value ? $this->delayedCapturePeriod : null,
            'PaymentWindow' => $this->paymentWindow,
            'GuestCheckOut' => $this->guestCheckOut,
            'FundingSources' => $this->fundingSources,
            'PaymentRequestId' => $this->paymentRequestId,
            'PayerHint' => $this->payerHint,
            'CardHolderNameHint' => $this->cardHolderNameHint,
            'InitiateRecurrence' => $this->initiateRecurrence,
            'RecurrenceId' => $this->recurrenceId,
            'RecurrenceType' => $this->recurrenceType,
            'TraceId' => $this->traceId,
            'RedirectUrl' => $this->redirectUrl,
            'CallbackUrl' => $this->callbackUrl,
            'Transactions' => array_map(fn (mixed $transaction): mixed => self::serialize($transaction), $this->transactions),
            'OrderNumber' => $this->orderNumber,
            'ShippingAddress' => self::serialize($this->shippingAddress),
            'BillingAddress' => self::serialize($this->billingAddress),
            'Locale' => $this->locale->value,
            'Currency' => $this->currency->value,
            'PayerPhoneNumber' => $this->payerPhoneNumber,
            'PayerWorkPhoneNumber' => $this->payerWorkPhoneNumber,
            'PayerHomePhoneNumber' => $this->payerHomePhoneNumber,
            'PurchaseInformation' => self::serialize($this->purchaseInformation),
        ];

        return array_filter($data, fn (mixed $value): bool => $value !== null);
    }

    private static function serialize(mixed $value): mixed
    {
        return match (true) {
            $value instanceof BackedEnum => $value->value,
            is_object($value) && method_exists($value, 'toArray') => $value->toArray(),
            default => $value,
        };
    }

    private static function normalizePhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        return $digits === '' ? null : $digits;
    }
}
