<?php

namespace DigitalThreads\LiqPay\Contracts;

use LiqPay;
use Illuminate\Http\Request;
use DigitalThreads\LiqPay\Exceptions\InvalidCallbackRequestException;

interface LiqPayClientInterface
{
    const API_VERSION = 3;
    const API_URL = 'https://www.liqpay.ua/api/';
    const CHECKOUT_URL = 'https://www.liqpay.ua/api/3/checkout';

    const CURRENCY_EUR = LiqPay::CURRENCY_EUR;
    const CURRENCY_USD = LiqPay::CURRENCY_USD;
    const CURRENCY_UAH = LiqPay::CURRENCY_UAH;
    const CURRENCY_RUB = LiqPay::CURRENCY_RUB;

    const LANUGAGE_EN = 'en';
    const LANUGAGE_RU = 'ru';

    const ACTION_PAY = 'pay';
    const ACTION_HOLD = 'hold';
    const ACTION_SPLIT = 'paysplit';
    const ACTION_SUBSCRIBE = 'subscribe';
    const ACTION_PAYDONATE = 'paydonate';
    const ACTION_AUTH = 'auth';
    const ACTION_REGULAR = 'regular';

    const RECIPIENT_3DS = 5;
    const RECIPIENT_WITHOUT_3DS_SUPPORT = 6;
    const RECIPIENT_WITHOUT_3DS_SECURE = 7;

    const PAYTYPE_CARD = 'card';
    const PAYTYPE_LIQPAY = 'liqpay';
    const PAYTYPE_PRIVAT24 = 'privat24';
    const PAYTYPE_MASTERPASS = 'masterpass';
    const PAYTYPE_MOMENT_PART = 'moment_part';
    const PAYTYPE_CASH = 'cash';
    const PAYTYPE_INVOICE = 'invoice';
    const PAYTYPE_QR = 'qr';

    const PAYMENT_STATUS_ERROR = 'error';
    const PAYMENT_STATUS_FAILURE = 'failure';
    const PAYMENT_STATUS_REVERSED = 'reversed';
    const PAYMENT_STATUS_SUBSCRIBED = 'subscribed';
    const PAYMENT_STATUS_SUCCESS = 'success';
    const PAYMENT_STATUS_UNSUBSCRIBED = 'unsubscribed';
    const PAYMENT_STATUS_3DS_VERIFY = '3ds_verify';
    const PAYMENT_STATUS_CAPTCHA_VERIFY = 'captcha_verify';
    const PAYMENT_STATUS_CVV_VERIFY = 'cvv_verify';
    const PAYMENT_STATUS_IVR_VERIFY = 'ivr_verify';
    const PAYMENT_STATUS_OTP_VERIFY = 'otp_verify';
    const PAYMENT_STATUS_PHONE_VERIFY = 'phone_verify';
    const PAYMENT_STATUS_RECEIVER_VERIFY = 'receiver_verify';
    const PAYMENT_STATUS_SENDER_VERIFY = 'sender_verify';
    const PAYMENT_STATUS_WAIT_QR = 'wait_qr';
    const PAYMENT_STATUS_WAIT_SENDER = 'wait_sender';
    const PAYMENT_STATUS_CASH_WAIT = 'cash_wait';
    const PAYMENT_STATUS_HOLD_WAIT = 'hold_wait';
    const PAYMENT_STATUS_INVOICE_WAIT = 'invoice_wait';
    const PAYMENT_STATUS_PREPARED = 'prepared';
    const PAYMENT_STATUS_PROCESSING = 'processing';
    const PAYMENT_STATUS_WAIT_ACCEPT = 'wait_accept';
    const PAYMENT_STATUS_WAIT_CARD = 'wait_card';
    const PAYMENT_STATUS_WAIT_COMPENSATION = 'wait_compensation';
    const PAYMENT_STATUS_WAIT_LC = 'wait_lc';
    const PAYMENT_STATUS_WAIT_RESERVE = 'wait_reserve';
    const PAYMENT_STATUS_WAIT_SECURE = 'wait_secure';

    /**
     * @psalm-var array<string, string | int | bool> $params
     * @param  array                                    $params
     * @return LiqPayCheckoutFormPrerequisitesInterface
     */
    public function getCheckoutFormPrerequisites(array $params): LiqPayCheckoutFormPrerequisitesInterface;

    /**
     * @param  Request                         $request
     * @throws InvalidCallbackRequestException
     * @return LiqPayPaymentDetailsInterface
     */
    public function validateCallback($request): LiqPayPaymentDetailsInterface;
}
