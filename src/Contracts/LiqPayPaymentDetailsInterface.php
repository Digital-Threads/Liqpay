<?php

namespace DigitalThreads\LiqPay\Contracts;

/**
 * @see https://www.liqpay.ua/documentation/en/api/callback
 */
interface LiqPayPaymentDetailsInterface
{
    /**
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function get(string $key, $default = null);
}
