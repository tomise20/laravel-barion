<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

class UserHistoryParticipantDto
{
    public ?string $userAccountId = null;
    public ?string $type = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $loginName = null;
    public ?string $organizationName = null;
    public ?string $shopId = null;
    public ?string $shopName = null;

    public static function createFromArray(array $data): UserHistoryParticipantDto
    {
        return new UserHistoryParticipantDto(
            userAccountId: $data['UserAccountId'],
            type: $data['Type'],
            firstName: $data['FirstName'],
            lastName: $data['LastName'],
            loginName: $data['LoginName'],
            organizationName: $data['OrganizationName'],
            shopId: $data['ShopId'],
            shopName: $data['ShopName']
        );
    }
}