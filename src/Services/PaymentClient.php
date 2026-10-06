<?php

declare(strict_types=1);

namespace Tomise\Barion\Services;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\DataTransferObjects\BarionPaymentDto;
use Tomise\Barion\DataTransferObjects\TransactionToFinishDto;
use Tomise\Barion\DataTransferObjects\TransactionToRefundDto;
use Tomise\Barion\Enums\BarionGatewayEndpoint;
use Tomise\Barion\Enums\PaymentType;
use Tomise\Barion\Exceptions\BarionConnectionException;
use Tomise\Barion\Exceptions\BarionPaymentException;
use Tomise\Barion\Responses\BarionCommonPaymentResponse;
use Tomise\Barion\Responses\BarionPaymentCompleteResponse;
use Tomise\Barion\Responses\BarionPaymentResponse;
use Tomise\Barion\Responses\BarionPaymentStatusResponse;
use Tomise\Barion\Responses\BarionRefoundResponse;

/**
 * The Smart Gateway operations of one POS: start a payment, query it, and the follow-ups
 * (refund, finish a reservation, capture or cancel a delayed capture).
 *
 * Every method throws BarionPaymentException when Barion rejects the request (getErrors() has Barion's errors)
 * and BarionConnectionException when Barion cannot be reached.
 */
class PaymentClient
{
    public function __construct(
        private BarionPaymentDto $paymentDto,
        private readonly BarionAdapter $adapter
    ) {
        $this->checkCredentials();
    }

    /**
     * Payment/Start: Immediate, Reservation or DelayedCapture, according to the payment data. Redirect the customer
     * to getGatewayUrl() of the response.
     *
     * @throws BarionPaymentException
     * @throws BarionConnectionException
     */
    public function sendSinglePayment(?string $idempotencyKey = null): BarionPaymentResponse
    {
        $this->checkPaymentData();

        return new BarionPaymentResponse(
            $this->adapter->send(BarionGatewayEndpoint::PaymentStart, $this->posKey(), $this->paymentDto->toArray(), idempotencyKey: $idempotencyKey)
        );
    }

    /**
     * The state of a payment (v4 PaymentState). Use it in the callback: Barion only says that something changed.
     *
     * @throws BarionPaymentException
     * @throws BarionConnectionException
     */
    public function sendPaymentState(string $paymentId): BarionPaymentStatusResponse
    {
        return BarionPaymentStatusResponse::createFromArray(
            $this->adapter->send(BarionGatewayEndpoint::PaymentState, $this->posKey(), pathParameters: ['paymentId' => $paymentId])
        );
    }

    /**
     * Payment/Complete: completes a payment started with 3D Secure authentication in your own UI.
     *
     * @throws BarionPaymentException
     * @throws BarionConnectionException
     */
    public function sendCompletePayment(?string $paymentId = null): BarionPaymentCompleteResponse
    {
        return BarionPaymentCompleteResponse::createFromArray(
            $this->adapter->send(BarionGatewayEndpoint::Complete, $this->posKey(), ['PaymentId' => $this->paymentId($paymentId)])
        );
    }

    /**
     * Finish a Reservation payment with the final amounts. A total lower than the reserved one refunds the rest,
     * 0 refunds the whole transaction.
     *
     * @param  array<int, TransactionToFinishDto>|Collection<int, TransactionToFinishDto>  $transactions
     *
     * @throws BarionPaymentException
     * @throws BarionConnectionException
     */
    public function sendFinishReservation(?string $paymentId = null, array|Collection $transactions = []): BarionCommonPaymentResponse
    {
        return BarionCommonPaymentResponse::createFromArray($this->adapter->send(BarionGatewayEndpoint::FinishReservation, $this->posKey(), [
            'PaymentId' => $this->paymentId($paymentId),
            'Transactions' => $this->serializeTransactions($transactions, TransactionToFinishDto::class),
        ]));
    }

    /**
     * Capture an authorized DelayedCapture payment with the final amounts (at most the authorized ones).
     *
     * @param  array<int, TransactionToFinishDto>|Collection<int, TransactionToFinishDto>  $transactions
     *
     * @throws BarionPaymentException
     * @throws BarionConnectionException
     */
    public function sendCapture(?string $paymentId = null, array|Collection $transactions = []): BarionCommonPaymentResponse
    {
        return BarionCommonPaymentResponse::createFromArray($this->adapter->send(BarionGatewayEndpoint::Capture, $this->posKey(), [
            'PaymentId' => $this->paymentId($paymentId),
            'Transactions' => $this->serializeTransactions($transactions, TransactionToFinishDto::class),
        ]));
    }

    /**
     * Release an authorized DelayedCapture payment without charging the customer.
     *
     * @throws BarionPaymentException
     * @throws BarionConnectionException
     */
    public function sendCancelAuthorization(?string $paymentId = null): BarionCommonPaymentResponse
    {
        return BarionCommonPaymentResponse::createFromArray(
            $this->adapter->send(BarionGatewayEndpoint::CancelAuthorization, $this->posKey(), ['PaymentId' => $this->paymentId($paymentId)])
        );
    }

    /**
     * Refund transactions of a completed payment, fully or partially.
     *
     * @param  array<int, TransactionToRefundDto>|Collection<int, TransactionToRefundDto>  $transactionsToRefund
     *
     * @throws BarionPaymentException
     * @throws BarionConnectionException
     */
    public function sendRefund(?string $paymentId = null, array|Collection $transactionsToRefund = [], ?string $idempotencyKey = null): BarionRefoundResponse
    {
        return BarionRefoundResponse::createFromArray($this->adapter->send(BarionGatewayEndpoint::Refound, $this->posKey(), [
            'PaymentId' => $this->paymentId($paymentId),
            'TransactionsToRefund' => $this->serializeTransactions($transactionsToRefund, TransactionToRefundDto::class),
        ], idempotencyKey: $idempotencyKey));
    }

    public function getPaymentData(): BarionPaymentDto
    {
        return $this->paymentDto;
    }

    /**
     * @deprecated use getPaymentData()
     */
    public function getPaymentDto(): BarionPaymentDto
    {
        return $this->paymentDto;
    }

    public function setPaymentData(BarionPaymentDto $paymentData): PaymentClient
    {
        $this->paymentDto = $paymentData;
        $this->checkCredentials();

        return $this;
    }

    /**
     * The payment the follow-up requests (refund, finish, capture, cancel) refer to by default.
     */
    public function setPaymentId(string $paymentId): PaymentClient
    {
        $this->paymentDto->setPaymentId($paymentId);

        return $this;
    }

    private function posKey(): string
    {
        return $this->paymentDto->getPosKey();
    }

    private function paymentId(?string $paymentId): string
    {
        $paymentId ??= $this->paymentDto->getPaymentId();

        if (blank($paymentId)) {
            throw new InvalidArgumentException('The Barion PaymentId is required for this request.');
        }

        return $paymentId;
    }

    /**
     * @param  array<int, object|array>|Collection<int, object|array>  $transactions
     * @param  class-string  $type
     * @return list<array<string, mixed>>
     */
    private function serializeTransactions(array|Collection $transactions, string $type): array
    {
        $transactions = Collection::make($transactions);

        if ($transactions->isEmpty()) {
            throw new InvalidArgumentException('At least one transaction is required for this request.');
        }

        return $transactions
            ->map(fn (object|array $transaction): array => match (true) {
                is_array($transaction) => $transaction,
                $transaction instanceof $type => $transaction->toArray(),
                default => throw new InvalidArgumentException('Expected '.$type.', got '.$transaction::class.'.'),
            })
            ->values()
            ->all();
    }

    private function checkCredentials(): void
    {
        if (trim($this->paymentDto->getPosKey()) === '') {
            throw new RuntimeException('Invalid configuration provided. Please provide a valid POSKey (BARION_POS_KEY)!');
        }
    }

    /**
     * @throws BarionPaymentException
     */
    private function checkPaymentData(): void
    {
        $type = PaymentType::tryFrom($this->paymentDto->getPaymentType());

        if ($type === null) {
            throw new BarionPaymentException('Invalid payment type: '.$this->paymentDto->getPaymentType().'.');
        }

        if ($type === PaymentType::Reservation && blank($this->paymentDto->getReservationPeriod())) {
            throw new BarionPaymentException('A Reservation payment needs a ReservationPeriod (BARION_RESERVATION_PERIOD).');
        }

        if ($type === PaymentType::DelayedCapture && blank($this->paymentDto->getDelayedCapturePeriod())) {
            throw new BarionPaymentException('A DelayedCapture payment needs a DelayedCapturePeriod (BARION_DELAYED_CAPTURE_PERIOD).');
        }

        if ($this->paymentDto->getTransactions() === []) {
            throw new BarionPaymentException('A payment needs at least one transaction.');
        }

        if ($this->paymentDto->getPaymentRequestId() === null) {
            throw new BarionPaymentException('A payment needs a PaymentRequestId.');
        }
    }
}
