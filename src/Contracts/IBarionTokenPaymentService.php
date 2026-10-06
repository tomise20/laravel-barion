<?php

declare(strict_types=1);

namespace Tomise\Barion\Contracts;

/**
 * Placeholder of a dedicated token payment API. Recurring (token) payments already work through Payment/Start:
 * see BarionPaymentDto::setInitiateRecurrence(), setRecurrenceId(), setRecurrenceType() and setTraceId().
 */
interface IBarionTokenPaymentService {}
