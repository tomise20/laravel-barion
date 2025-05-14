<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use Tomise\Barion\Traits\Arrayable;

class BalanceDto
{

    use Arrayable;

    public float $available;
    public float $blocked;
}
