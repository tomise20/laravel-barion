<?php

namespace Tomise\Barion\Exceptions;

use Exception;
use RuntimeException;

class BarionLocaleException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}