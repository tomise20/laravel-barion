<?php

declare(strict_types=1);

namespace Tomise\Barion\Attributes;

use Attribute;
use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

/**
 * Maps a Barion date (ISO 8601, e.g. "2024-05-14T12:34:56.789Z" or "2024-05-14T12:34:56") to a date object.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class MapToDateTime
{
    /**
     * @param  string|null  $format  an exact format; by default any ISO 8601 date is accepted
     */
    public function __construct(public ?string $format = null) {}

    public function process(mixed $rawValue): ?DateTimeInterface
    {
        if (! is_string($rawValue) || $rawValue === '') {
            return null;
        }

        if ($this->format !== null) {
            return DateTimeImmutable::createFromFormat($this->format, $rawValue) ?: null;
        }

        try {
            return new DateTimeImmutable($rawValue);
        } catch (Throwable) {
            return null;
        }
    }
}
