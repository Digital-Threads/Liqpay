<?php

namespace DigitalThreads\LiqPay\Tests\Dto;

use function PHPUnit\Framework\assertSame;
use Illuminate\Foundation\Testing\WithFaker;
use DigitalThreads\LiqPay\Tests\AbstractTestCase;
use DigitalThreads\LiqPay\Dto\LiqPayCheckoutFormPrerequisites;

final class LiqPayCheckoutFormPrerequisitesTest extends AbstractTestCase
{
    use WithFaker;

    public function testConstructor(): void
    {
        $dto = new LiqPayCheckoutFormPrerequisites(
            $action = $this->faker->url,
            $data = $this->faker->sha1,
            $signature = $this->faker->sha1,
        );

        assertSame($action, $dto->getAction());
        assertSame($data, $dto->getData());
        assertSame($signature, $dto->getSignature());
    }
}
