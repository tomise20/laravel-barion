<?php

declare(strict_types=1);

namespace Tomise\Barion\Enums;

enum TransactionStatus: string {
    case Prepared = 'Prepared';
    case Started = 'Started';
    case Succeeded = 'Succeeded';
    case Timeout = 'Timeout';
    case ShopIsDeleted = 'ShopIsDeleted';
    case ShopIsClosed = 'ShopIsClosed';
    case Rejected = 'Rejected';
    case RejectedByShop = 'RejectedByShop';
    case Storno = 'Storno';
    case Reserved = 'Reserved';
    case Deleted = 'Deleted';
    case Expired = 'Expired';
    case Authorized = 'Authorized';
    case InvalidPaymentRecord = 'InvalidPaymentRecord';
    case PaymentTimeOut = 'PaymentTimeOut';
    case InvalidPaymentStatus = 'InvalidPaymentStatus';
    case PaymentSenderOrRecipientIsInvalid = 'PaymentSenderOrRecipientIsInvalid';
    case Unknown = 'Unknown';
}