<?php

namespace DigitalThreads\LiqPay\Dto;

use DigitalThreads\LiqPay\Contracts\LiqPayCheckoutFormPrerequisitesInterface;

final class LiqPayCheckoutFormPrerequisites implements LiqPayCheckoutFormPrerequisitesInterface
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $signature;

    /**
     * @param  string $action
     * @param  string $data
     * @param  string $signature
     * @return void
     */
    public function __construct(
        string $action,
        string $data,
        string $signature
    ) {
        $this->action = $action;
        $this->data = $data;
        $this->signature = $signature;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getSignature(): string
    {
        return $this->signature;
    }
}
