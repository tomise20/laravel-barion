<?php

declare(strict_types=1);

namespace Tomise\Barion\Exceptions;

use RuntimeException;

class BarionConnectionException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}