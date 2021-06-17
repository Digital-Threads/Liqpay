<?php

namespace DigitalThreads\LiqPay;

use LiqPay;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use DigitalThreads\LiqPay\Dto\LiqPayConfigurations;
use Illuminate\Contracts\Support\DeferrableProvider;
use DigitalThreads\LiqPay\Contracts\LiqPayClientInterface;
use DigitalThreads\LiqPay\Contracts\LiqPayConfigurationsInterface;

final class LiqPayServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        LiqPayClientInterface::class => LiqPaySdkClient::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->bind(LiqPay::class, static function (Application $app) {
            $config = $app->make('config');

            return new LiqPay(
                $config->get('liqpay.public_key'),
                $config->get('liqpay.private_key')
            );
        });

        $this->app->bind(LiqPayClientInterface::class, LiqPaySdkClient::class);

        $this->app->bind(LiqPayConfigurationsInterface::class, function (Application $app) {
            $config = $app->make('config');

            return new LiqPayConfigurations(
                $config->get('liqpay.public_key'),
                $config->get('liqpay.private_key'),
                $config->get('liqpay.default_currency')
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(): void
    {
        $this->publishes([dirname(__DIR__, 1) . '/config/liqpay.php' => config_path('liqpay.php')], 'config');
        $this->mergeConfigFrom(dirname(__DIR__, 1) . '/config/liqpay.php', 'liqpay');
    }

    /**
     * {@inheritdoc}
     */
    public function provides(): array
    {
        return [
            LiqPay::class,
            LiqPayConfigurationsInterface::class,
            LiqPayClientInterface::class,
        ];
    }
}
