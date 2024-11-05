<?php

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

class BarionParamsBuilder
{
    /**
     * @var Collection<Model>
     */
    private Collection $items;
    private Model $order;
    private array $configData;
    private array $transactions = [];

    public function __construct()
    {
        $this->loadDataFromConfig();
    }

	/**
	 * @param Model $order
	 * @return $this
	 */
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

    public function build(): BarionPaymentDto
    {
        $barionData = new BarionPaymentDto($this->configData['posKey']);
        $barionData->setLocale($this->getLocale());
        $barionData->setCurrency($this->getCurrency());

        $this->setConfigData($barionData);

        $this->setDataFromModel($barionData);

        $this->setTransactions($barionData);

        return $barionData;
    }

    public function buildManual(): BarionPaymentDto
    {
        $barionData = new BarionPaymentDto($this->configData['posKey']);
		$barionData->setLocale($this->getLocale());
        $barionData->setCurrency($this->getCurrency());

        $this->setConfigData($barionData);

        return $barionData;
    }

    private function setConfigData(BarionPaymentDto $barionData): void
    {
        $barionData->setPaymentType($this->configData['paymentType']);
        $barionData->setPaymentWindow($this->configData['paymentWindow']);
        $barionData->setGuestCheckout($this->configData['guestCheckout']);
        $barionData->setFundingSources($this->configData['fundingSources']);

        if(Arr::get($this->configData, 'redirectUrl')) {
            $barionData->setRedirectUrl($this->configData['redirectUrl']);
        }

        if(Arr::get($this->configData, 'callbackUrl')) {
            $barionData->setCallbackUrl($this->configData['callbackUrl']);
        }
    }

	/**
	 * @return void
	 */
    private function loadDataFromConfig(): void
    {
        $this->configData = config("barion-gateway");
    }

    private function setDataFromModel(BarionPaymentDto $barionData): void
    {
        $baseDataKeys = ['payment_request_id', 'payer_hint', 'order_number', 'phone_number'];

        foreach($this->order->barion_casts as $key => $value) {
            if(in_array($key, $baseDataKeys)) {
                $setter = "set".ucfirst(Str::camel($key));
                $barionData->$setter($this->order->$value);
            }
        }
    }

    private function setTransactions(BarionPaymentDto $barionData): void
    {
        $totalField = Arr::get($this->order->barion_casts, 'total');

        $transaction = (new PaymentTransactionDto())
            ->setPostTransactionId($barionData->getPaymentRequestId())
            ->setPayee($this->getPayee())
            ->setTotal($this->roundPrice($this->order->$totalField, $barionData->getCurrency()))
            ->setItems($this->setTransactionItems());

        $this->transactions[] = $transaction->toArray();

        $barionData->setTransactions($this->transactions);
    }

    private function roundPrice(mixed $price, string $currency): int|float
    {
        if(Currency::Huf->value === $currency) {
            return round($price);
        }

        return $price;
    }

	/**
	 * @return Collection
	 */
    private function setTransactionItems(): Collection
    {
        $items = Collection::make();
		foreach($this->items as $item) {
            $barionItem = new TransactionItemDto();
            foreach($item->barion_casts as $key => $value) {
                $setter = "set".ucfirst(Str::camel($key));
                $barionItem->$setter($item->$value);
            }

            $items->push($barionItem);
        }

		return $items;
    }

    private function getPayee(): string
    {
        if(in_array('payee', $this->order->barion_casts)) {
            $payeeField = $this->order->barion_casts['payee'];

            return $this->order->$payeeField;
        }

        return Arr::get($this->configData, 'payee');
    }

	/**
	 * @return Locale
	 */
    private function getLocale(): Locale
    {

        $locale = Arr::get($this->configData, 'locale');

        if(!$locale) {
            throw new BarionPaymentException('Locale is not settings!');
        }


		return Locale::tryFrom($locale);
    }

	/**
	 * @return Currency
	 */
    private function getCurrency(): Currency
    {
        $currency = Arr::get($this->configData, 'currency');

        if(!$currency) {
            throw new BarionPaymentException('Currency is not settings!');
        }

        return Currency::tryFrom($currency);
    }
}
