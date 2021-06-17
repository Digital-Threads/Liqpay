<?php

namespace DigitalThreads\LiqPay\Dto;

use stdClass;
use DigitalThreads\LiqPay\Contracts\LiqPayPaymentDetailsInterface;

final class StdClassLiqPayPaymentDetails implements LiqPayPaymentDetailsInterface
{
    /**
     * @var stdClass
     */
    private $source;

    /**
     * @param  stdClass $source
     * @return void
     */
    public function __construct(stdClass $source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        return $this->source->{$key} ?? $default;
    }
}
