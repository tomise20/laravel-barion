<?php

namespace Tomise\Barion\Exceptions;

use Exception;

class BarionPaymentException extends Exception
{
    private $responseData = [];

    public function setResponseData(array $responseData): BarionPaymentException
    {
        $this->responseData = $responseData;

        return $this;
    }

    public function getResponseData(): array
    {
        return $this->responseData;
    }
}