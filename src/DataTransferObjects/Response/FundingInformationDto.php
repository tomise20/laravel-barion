<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use Tomise\Barion\Traits\Arrayable;

/**
 * How the payment was funded, e.g. the (masked) bank card.
 */
class FundingInformationDto
{
    use Arrayable;

    public function __construct(
        public ?array $bankCard = null,
        public ?string $authorizationCode = null,
        public ?string $processResult = null,
    ) {}
}
