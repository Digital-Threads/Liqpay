  
<?php

use DigitalThreads\LiqPay\Contracts\LiqPayClientInterface;

return [
    /**
     * LiqPay credential keys.
     *
     * @see https://www.liqpay.ua/documentation/en/start
     */
    'public_key' => env('LIQPAY_PUBLIC_KEY'),

    'private_key' => env('LIQPAY_PRIVATE_KEY'),

    /**
     * LiqPay default currency
     *
     * @see https://www.liqpay.ua/documentation/en/api/aquiring/checkout/doc
     */
    'default_currency' => env('LIQPAY_DEFAULT_CURRENCY', LiqPayClientInterface::CURRENCY_USD),

    /**
     * LiqPay language.
     */
    'lang' => env('LIQPAY_LANUGAGE', LiqPayClientInterface::LANUGAGE_EN),
];
