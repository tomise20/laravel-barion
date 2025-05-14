<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use Tomise\Barion\Traits\Arrayable;

class UserHistoryParticipantDto
{

    use Arrayable;

    public ?string $userAccountId = null;
    public ?string $type = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $loginName = null;
    public ?string $organizationName = null;
    public ?string $shopId = null;
    public ?string $shopName = null;
}
