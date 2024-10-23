<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

class UserHistoryParticipantDto
{
    public string $userAccountId;
    public string $type;
    public string $fistName;
    public string $lastName;
    public string $loginName;
    public string $organizationName;
    public string $shopId;
    public string $shopName;
}