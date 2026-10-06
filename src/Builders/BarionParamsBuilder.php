<?php

declare(strict_types=1);

namespace Tomise\Barion\Builders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tomise\Barion\DataTransferObjects\BarionPaymentDto;
use Tomise\Barion\DataTransferObjects\Currency;
use Tomise\Barion\DataTransferObjects\Locale;
use Tomise\Barion\DataTransferObjects\PaymentTransactionDto;
use Tomise\Barion\DataTransferObjects\TransactionItemDto;
use Tomise\Barion\Exceptions\BarionPaymentException;

/**
 * Builds the Payment/Start request from the config, and optionally from an order model and its items
 * (with $barion_casts: Barion field => model attribute).
 */
class BarionParamsBuilder
{
    /**
     * Order fields that go to the request itself; "total" and "payee" go to the transaction.
     */
    private const ORDER_FIELDS = ['payment_request_id', 'payer_hint', 'order_number', 'phone_number', 'payer_phone_number', 'card_holder_name_hint'];

    /**
     * @var Collection<int, Model>
     */
    private Collection $items;

    private Model $order;

    private array $configData;

    public function __construct()
    {
        $this->configData = (array) config('barion-gateway');
        $this->items = new Collection;
    }

    public function setOrder(Model $order): BarionParamsBuilder
    {
        $this->order = $order;

        return $this;
    }

    public function setItems(Collection $items): BarionParamsBuilder
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @throws BarionPaymentException
     */
    public function build(): BarionPaymentDto
    {
        $barionData = $this->buildManual();

        $this->setDataFromOrder($barionData);
        $this->setTransactions($barionData);

        return $barionData;
    }

    /**
     * @throws BarionPaymentException
     */
    public function buildManual(): BarionPaymentDto
    {
        $barionData = new BarionPaymentDto((string) Arr::get($this->configData, 'posKey', ''));
        $barionData->setLocale($this->getLocale());
        $barionData->setCurrency($this->getCurrency());
        // Barion needs a unique request id; it can be overwritten (or comes from the casts of the order).
        $barionData->setPaymentRequestId((string) Str::uuid());

        $this->setConfigData($barionData);

        return $barionData;
    }

    private function setConfigData(BarionPaymentDto $barionData): void
    {
        $barionData->setPaymentType((string) Arr::get($this->configData, 'paymentType', 'Immediate'));
        $barionData->setPaymentWindow((string) Arr::get($this->configData, 'paymentWindow', '00:30:00'));
        $barionData->setGuestCheckout(filter_var(Arr::get($this->configData, 'guestCheckout', true), FILTER_VALIDATE_BOOL));
        $barionData->setFundingSources((array) Arr::get($this->configData, 'fundingSources', ['All']));
        $barionData->setReservationPeriod(Arr::get($this->configData, 'reservationPeriod'));
        $barionData->setDelayedCapturePeriod(Arr::get($this->configData, 'delayedCapturePeriod'));

        if (Arr::get($this->configData, 'redirectUrl')) {
            $barionData->setRedirectUrl($this->configData['redirectUrl']);
        }

        if (Arr::get($this->configData, 'callbackUrl')) {
            $barionData->setCallbackUrl($this->configData['callbackUrl']);
        }
    }

    private function setDataFromOrder(BarionPaymentDto $barionData): void
    {
        foreach ($this->order->barion_casts as $key => $attribute) {
            $value = $this->order->{$attribute};

            if (! in_array($key, self::ORDER_FIELDS, true) || blank($value)) {
                continue;
            }

            $setter = 'set'.ucfirst(Str::camel($key));
            $barionData->{$setter}((string) $value);
        }
    }

    private function setTransactions(BarionPaymentDto $barionData): void
    {
        $currency = $barionData->getCurrencyEnum();
        $totalField = Arr::get($this->order->barion_casts, 'total');

        $transaction = (new PaymentTransactionDto)
            ->setPostTransactionId($barionData->getPaymentRequestId())
            ->setPayee($this->getPayee())
            ->setTotal($this->roundPrice($this->order->{$totalField}, $currency))
            ->setItems($this->transactionItems($currency));

        $barionData->setTransactions([$transaction]);
    }

    private function roundPrice(mixed $price, Currency $currency): float
    {
        return round((float) $price, $currency->decimals());
    }

    /**
     * @return Collection<int, TransactionItemDto>
     */
    private function transactionItems(Currency $currency): Collection
    {
        return $this->items->map(function (Model $item) use ($currency): TransactionItemDto {
            $barionItem = new TransactionItemDto;

            foreach ($item->barion_casts as $key => $attribute) {
                $value = $item->{$attribute};

                if (in_array($key, ['unit_price', 'item_total'], true)) {
                    $value = $this->roundPrice($value, $currency);
                }

                $barionItem->{'set'.ucfirst(Str::camel($key))}($value);
            }

            return $barionItem;
        })->values();
    }

    /**
     * @throws BarionPaymentException
     */
    private function getPayee(): string
    {
        $payeeField = Arr::get($this->order->barion_casts, 'payee');
        $payee = $payeeField ? $this->order->{$payeeField} : Arr::get($this->configData, 'payee');

        if (blank($payee)) {
            throw new BarionPaymentException('The payee (the e-mail of your Barion wallet) is not set: BARION_PAYEE.');
        }

        return (string) $payee;
    }

    /**
     * @throws BarionPaymentException
     */
    private function getLocale(): Locale
    {
        $locale = Arr::get($this->configData, 'locale');

        return Locale::tryFrom((string) $locale) ?? throw new BarionPaymentException("Invalid Barion locale: {$locale}");
    }

    /**
     * @throws BarionPaymentException
     */
    private function getCurrency(): Currency
    {
        $currency = Arr::get($this->configData, 'currency');

        return Currency::tryFrom((string) $currency) ?? throw new BarionPaymentException("Invalid Barion currency: {$currency}");
    }
}
