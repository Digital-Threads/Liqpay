<?php

namespace DigitalThreads\LiqPay;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use DigitalThreads\LiqPay\Contracts\LiqPayClientInterface;
use DigitalThreads\LiqPay\Contracts\LiqPayPaymentDetailsInterface;
use DigitalThreads\LiqPay\Contracts\LiqPayCheckoutFormPrerequisitesInterface;

/**
 * @method static LiqPayCheckoutFormPrerequisitesInterface getCheckoutFormPrerequisites(array $params)
 * @method static LiqPayPaymentDetailsInterface validateCallback(Request $request)
 */
final class LiqPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LiqPayClientInterface::class;
    }
}
