<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class BarionWalletDto implements Arrayable
{
    private string $apiKey;

    private string $lastVisibleItemId = null;
    private mixed $lastRequestTime = null;
    private ?int $limit = null;

    private ?int $year = null;
    private ?int $month = null;
    private ?int $day = null;
    private ?Currency $currency = null;

    private ?float $amount = null;
    private ?string $comment = null;
    private ?BankAccountDto $bankAccount = null;
    private ?BankDto $bank = null;
    private ?RecipientDto $recipient = null;

    public function __construct()
    {
        $this->apiKey = config('barion-gateway.apiKey');
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): BarionWalletDto
    {
        $this->year = $year;
        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): BarionWalletDto
    {
        $this->month = $month;
        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): BarionWalletDto
    {
        $this->day = $day;
        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): BarionWalletDto
    {
        $this->currency = $currency;
        return $this;
    }

    public function getLastVisibleItemId(): ?string
    {
        return $this->lastVisibleItemId;
    }

    public function setLastVisibleItemId(?string $lastVisibleItemId): BarionWalletDto
    {
        $this->lastVisibleItemId = $lastVisibleItemId;
        return $this;
    }

    public function getLastRequestTime(): mixed
    {
        return $this->lastRequestTime;
    }

    public function setLastRequestTime(mixed $lastRequestTime): BarionWalletDto
    {
        $this->lastRequestTime = $lastRequestTime;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): BarionWalletDto
    {
        $this->limit = $limit;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): BarionWalletDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): BarionWalletDto
    {
        $this->comment = $comment;
        return $this;
    }

    public function getBankAccount(): ?BankAccountDto
    {
        return $this->bankAccount;
    }

    public function setBankAccount(?BankAccountDto $bankAccount): BarionWalletDto
    {
        $this->bankAccount = $bankAccount;
        return $this;
    }

    public function getBank(): ?BankDto
    {
        return $this->bank;
    }

    public function setBank(?BankDto $bank): BarionWalletDto
    {
        $this->bank = $bank;
        return $this;
    }

    public function getRecipient(): ?RecipientDto
    {
        return $this->recipient;
    }

    public function setRecipient(?RecipientDto $recipient): BarionWalletDto
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this as $key => $value) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $result[$key] = $value->toArray();
            } elseif (is_object($value) && method_exists($value, 'getValue')) {
                $result[$key] = $value->getValue();
            } else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
}
