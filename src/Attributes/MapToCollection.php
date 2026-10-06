<?php

declare(strict_types=1);

namespace Tomise\Barion\Attributes;

use Attribute;
use Illuminate\Support\Collection;

/**
 * Maps a list of raw response objects to a collection of the given DTO.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class MapToCollection
{
    public function __construct(public string $itemType) {}

    public function process(mixed $rawValue): Collection
    {
        return Collection::make(is_array($rawValue) ? $rawValue : [])
            ->map(fn (mixed $item): mixed => is_array($item) ? $this->itemType::fromArray($item) : $item)
            ->values();
    }
}
