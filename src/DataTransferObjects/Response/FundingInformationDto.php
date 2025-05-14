<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use Tomise\Barion\Traits\Arrayable;

class FundingInformationDto
{
    use Arrayable;

    public function __construct(
        public readonly array $bankCard,
        public readonly ?string $authorizationCode = null,
        public readonly ?string $processResult = null
    )
    {
    }
}
