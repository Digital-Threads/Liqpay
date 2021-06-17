<?php

namespace DigitalThreads\LiqPay\Dto;

use DigitalThreads\LiqPay\Contracts\LiqPayConfigurationsInterface;

final class LiqPayConfigurations implements LiqPayConfigurationsInterface
{
    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $defaultCurrency;

    /**
     * @param  string $publicKey
     * @param  string $privateKey
     * @param  string $defaultCurrency
     * @return void
     */
    public function __construct(
        string $publicKey,
        string $privateKey,
        string $defaultCurrency
    ) {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrency(): string
    {
        return $this->defaultCurrency;
    }
}
