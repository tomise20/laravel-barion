<?php

declare(strict_types=1);

namespace Tomise\Barion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\DataTransferObjects\BarionPaymentDto;
use Tomise\Barion\Services\PaymentClient;

class AdapterTest extends TestCase
{
    /**
     * @test
     */
    public function testIt_throws_exception_if_invalid_credentials(): void
    {
        $this->expectException(RuntimeException::class);

        new PaymentClient(new BarionPaymentDto(''), new BarionAdapter());
    }
}