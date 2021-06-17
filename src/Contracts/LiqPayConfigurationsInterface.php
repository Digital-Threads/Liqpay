<?php

namespace DigitalThreads\LiqPay\Contracts;

interface LiqPayConfigurationsInterface
{
    /**
     * @return string 
     */
    public function getPublicKey(): string;

    /**
     * @return string 
     */
    public function getPrivateKey(): string;

    /**
     * @return string 
     */
    public function getDefaultCurrency(): string;
}
