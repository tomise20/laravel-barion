<?php

declare(strict_types=1);

namespace Tomise\Barion\Enums;

enum BarionStatus: string {
    case Prepared = 'Prepared';
    case Started = 'Started';
    case InProgress = 'InProgress';
    case Waiting = 'Waiting';
    case Reserved = 'Reserved';
    case Authorized = 'Authorized';
    case Canceled = 'Canceled';
    case Succeeded = 'Succeeded';
    case Failed = 'Failed';

	public function getStatusName(BarionStatus $status): string
	{
		return match (true) {
			$status === BarionStatus::Prepared => 'Folyamatban',
			$status === BarionStatus::Started => 'Elkezdődott',
			$status === BarionStatus::InProgress => 'Folymatban',
			$status === BarionStatus::Waiting => 'Várakozás',
			$status === BarionStatus::Canceled => 'Visszavonva',
			$status === BarionStatus::Succeeded => 'Sikeres fizetés',
			$status === BarionStatus::Failed => 'Sikertelen fizetés',
			default => 'Ismertlen státusz',
		};
	}

    public function isSuccess(): bool
    {
        return $this === BarionStatus::Succeeded;
    }

    public function isFailed(): bool
    {
        return $this === BarionStatus::Failed;
    }

    public function isCanceled(): bool
    {
        return $this === BarionStatus::Canceled;
    }

    public function isInProgress(): bool
    {
        return $this === BarionStatus::InProgress;
    }
}
