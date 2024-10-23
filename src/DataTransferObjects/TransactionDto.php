<?php

namespace Tomise\Barion\DataTransferObjects;

use Carbon\Carbon;

class TransactionDto
{
    private ?int $userId;
    private int $reservationId;
    private string $paymentMethod;
    private string $paymentId;
    private string $transactionId;
    private string $ownStatus;
    private string $gatewayStatus;
    private string $currency;
    private Carbon $completedAt;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $id): TransactionDto
    {
        $this->userId = $id;

        return $this;
    }

    public function getReservationId(): int
    {
        return $this->reservationId;
    }

    public function setReservationId(int $id): TransactionDto
    {
        $this->reservationId = $id;

        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $method): TransactionDto
    {
        $this->paymentMethod = $method;

        return $this;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $id): TransactionDto
    {
        $this->paymentId = $id;

        return $this;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $id): TransactionDto
    {
        $this->transactionId = $id;

        return $this;
    }

    public function getOwnStatus(): string
    {
        return $this->ownStatus;
    }

    public function setOwnStatus(string $status): TransactionDto
    {
        $this->ownStatus = $status;

        return $this;
    }

    public function getGatewayStatus(): string
    {
        return $this->gatewayStatus;
    }

    public function setGatewayStatus(string $status): TransactionDto
    {
        $this->gatewayStatus = $status;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): TransactionDto
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCompletedAt(): Carbon
    {
        return $this->completedAt;
    }

    public function setCompletedAt(string $time): TransactionDto
    {
        $this->completedAt = Carbon::parse($time);

        return $this;
    }
}