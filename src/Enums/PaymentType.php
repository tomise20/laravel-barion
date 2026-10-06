<?php

declare(strict_types=1);

namespace Tomise\Barion\Enums;

/**
 * How the money is taken:
 * - Immediate: charged at once.
 * - Reservation: charged at once but kept reserved until you finish it (FinishReservation), at most for the
 *   ReservationPeriod; an unfinished reservation is refunded automatically.
 * - DelayedCapture: the card is only authorized; you capture the final amount later (Capture) within the
 *   DelayedCapturePeriod (at most 7 days, 21 days for Hungarian shops), or release it (CancelAuthorization);
 *   an uncaptured authorization is released automatically. Bank cards only.
 */
enum PaymentType: string
{
    case Immediate = 'Immediate';
    case Reservation = 'Reservation';
    case DelayedCapture = 'DelayedCapture';
}
