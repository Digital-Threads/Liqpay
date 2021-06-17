<?php

namespace DigitalThreads\LiqPay\Contracts;

interface LiqPayCheckoutFormPrerequisitesInterface
{
    /**
     * @return string 
     */
    public function getAction(): string;

    /**
     * @return string 
     */
    public function getData(): string;

    /**
     * @return string 
     */
    public function getSignature(): string;
}
