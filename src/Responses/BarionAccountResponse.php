<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Illuminate\Support\Collection;
use Tomise\Barion\DataTransferObjects\AccountDto;
use Tomise\Barion\Utils\TransformHelper;

class BarionAccountResponse
{
    /**
     * @var Collection<AccountDto> $accounts
     */
    public Collection $accounts;

    public static function createFromArray(array $rawResponse): BarionAccountResponse
    {
        $instance = new self();

        return TransformHelper::transformArray($instance, $rawResponse);
    }
}