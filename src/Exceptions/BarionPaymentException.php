<?php

declare(strict_types=1);

namespace Tomise\Barion\Exceptions;

use Exception;

/**
 * A request Barion rejected. The message is Barion's first error; all errors and the raw response are attached.
 */
class BarionPaymentException extends Exception
{
    private array $responseData = [];

    /**
     * @var list<array{ErrorCode?: string, Title?: string, Description?: string}>
     */
    private array $errors = [];

    public function setResponseData(array $responseData): BarionPaymentException
    {
        $this->responseData = $responseData;

        return $this;
    }

    public function getResponseData(): array
    {
        return $this->responseData;
    }

    public function setErrors(array $errors): BarionPaymentException
    {
        $this->errors = array_values($errors);

        return $this;
    }

    /**
     * @return list<array{ErrorCode?: string, Title?: string, Description?: string}>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Barion's error code of the first error, e.g. "InvalidPosKey".
     */
    public function getErrorCode(): ?string
    {
        return $this->errors[0]['ErrorCode'] ?? null;
    }
}
