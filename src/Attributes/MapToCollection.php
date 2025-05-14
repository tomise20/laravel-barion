<?php
namespace Tomise\Barion\Attributes;

use Attribute;
use Illuminate\Support\Collection;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MapToCollection
{
    public function __construct(public string $itemType) {}

    public function process(array $rawValue): Collection
    {
        return new Collection(
            array_map(
                fn($item) => $this->itemType::fromArray($item),
                $rawValue
            )
        );
    }
}