<?php

namespace DigitalThreads\LiqPay\Tests;

use DigitalThreads\LiqPay\LiqPaySdkClient;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertInstanceOf;
use DigitalThreads\LiqPay\LiqPay as LiqPayFacade;
use DigitalThreads\LiqPay\Contracts\LiqPayClientInterface;
use DigitalThreads\LiqPay\Contracts\LiqPayConfigurationsInterface;

final class LiqPayServiceProviderTest extends AbstractTestCase
{
    public function testBindLiqPayConfigurationsInterfaceWithLiqPayConfigurations(): void
    {
        /** @var LiqPayConfigurationsInterface */
        $config = $this->app->make(LiqPayConfigurationsInterface::class);

        assertSame(config('liqpay.public_key'), $config->getPublicKey());
        assertSame(config('liqpay.private_key'), $config->getPrivateKey());
        assertSame(config('liqpay.default_currency'), $config->getDefaultCurrency());
    }

    public function testBindLiqPayClientInterfaceToLiqPaySdkClient(): void
    {
        assertInstanceOf(LiqPaySdkClient::class, $this->app->make(LiqPayClientInterface::class));
    }

    public function testBindLiqFacadeAccessorToLiqPayClientInterface(): void
    {
        assertInstanceOf(LiqPayClientInterface::class, LiqPayFacade::getFacadeRoot());
    }
}
