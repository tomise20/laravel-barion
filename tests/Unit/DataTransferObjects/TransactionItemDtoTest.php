<?php

declare(strict_types=1);

namespace Tomise\Barion\Tests\Unit\DataTransferObjects;

use BadMethodCallException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tomise\Barion\DataTransferObjects\TransactionItemDto;
use Tomise\Barion\Tests\Unit\BaseUnitTest;

class TransactionItemDtoTest extends BaseUnitTest
{
    #[DataProvider('itemTotalProvider')]
    public function test_toArray_sendsItemTotal(?float $itemTotal, int|float $expectedItemTotal): void
    {
        // Arrange
        $item = (new TransactionItemDto)->setName('Massage')->setDescription('30 minutes')->setQuantity(2)->setUnitPrice(4500.5);

        if ($itemTotal !== null) {
            $item->setItemTotal($itemTotal);
        }

        // Act
        $data = $item->toArray();

        // Assert
        $this->assertSame($expectedItemTotal, $data['ItemTotal']);
    }

    public static function itemTotalProvider(): array
    {
        return [
            'quantity × unit price when missing' => [null, 9001],
            'the given item total' => [8000.0, 8000],
            'decimals kept' => [8000.25, 8000.25],
        ];
    }

    public function test_toArray_usesBarionFieldNamesAndSkipsNulls(): void
    {
        // Arrange
        $item = (new TransactionItemDto)->setName('Massage')->setDescription('30 minutes')->setUnitPrice(4500)->setSku('MB-30');

        // Act
        $data = $item->toArray();

        // Assert
        $this->assertSame('MB-30', $data['SKU']);
        $this->assertArrayNotHasKey('Sku', $data);
        $this->assertArrayNotHasKey('ImageUrl', $data);
        $this->assertSame(['Name', 'Description', 'Quantity', 'Unit', 'UnitPrice', 'ItemTotal', 'SKU'], array_keys($data));
    }

    public function test_setter_throwsExceptionForUnknownProperty(): void
    {
        // Assert
        $this->expectException(BadMethodCallException::class);

        // Act
        (new TransactionItemDto)->setColor('red');
    }
}
