<?php

namespace DigitalThreads\LiqPay\Tests;

use Orchestra\Testbench\TestCase;
use DigitalThreads\LiqPay\LiqPayServiceProvider;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class AbstractTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            LiqPayServiceProvider::class,
        ];
    }
}
