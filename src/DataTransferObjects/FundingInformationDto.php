<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

class FundingInformationDto
{
    public function __construct(
        public readonly array $bankCard,
        public readonly ?string $authorizationCode = null,
        public readonly ?string $processResult = null
    )
    {
    }

    public static function createFromArray(array $data): FundingInformationDto
    {
        return new FundingInformationDto(
            bankCard: $data['BankCard'],
            authorizationCode: $data['AuthorizationCode'],
            processResult: $data['ProcessResult']
        );
    }
}