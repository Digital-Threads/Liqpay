<?php

namespace DigitalThreads\LiqPay;

use LiqPay;
use Illuminate\Support\Arr;
use DigitalThreads\LiqPay\Contracts\LiqPayClientInterface;
use DigitalThreads\LiqPay\Dto\StdClassLiqPayPaymentDetails;
use DigitalThreads\LiqPay\Dto\LiqPayCheckoutFormPrerequisites;
use DigitalThreads\LiqPay\Contracts\LiqPayConfigurationsInterface;
use DigitalThreads\LiqPay\Contracts\LiqPayPaymentDetailsInterface;
use DigitalThreads\LiqPay\Exceptions\InvalidCallbackRequestException;
use DigitalThreads\LiqPay\Contracts\LiqPayCheckoutFormPrerequisitesInterface;

final class LiqPaySdkClient implements LiqPayClientInterface
{
    /**
     * @var LiqPay
     */
    private $sdk;

    /**
     * @var LiqPayConfigurationsInterface
     */
    private $config;

    /**
     * @param  LiqPay                        $sdk
     * @param  LiqPayConfigurationsInterface $config
     * @return void
     */
    public function __construct(LiqPay $sdk, LiqPayConfigurationsInterface $config)
    {
        $this->sdk = $sdk;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutFormPrerequisites(array $params): LiqPayCheckoutFormPrerequisitesInterface
    {
        $source = array_merge($params, [
            'version' => self::API_VERSION,
            'public_key' => $this->config->getPublicKey(),
            'currency' => Arr::get($params, 'currency', $this->config->getDefaultCurrency()),
        ]);

        $encoded = base64_encode(json_encode($source));

        return new LiqPayCheckoutFormPrerequisites(self::CHECKOUT_URL, $encoded, $this->sdk->cnb_signature($source));
    }

    /**
     * {@inheritdoc}
     */
    public function validateCallback($request): LiqPayPaymentDetailsInterface
    {
        $signature = $request->input('signature');
        $data = $request->input('data');

        if (!is_string($signature) || !is_string($data)) {
            throw InvalidCallbackRequestException::request();
        }

        if ($signature !== $this->sdk->str_to_sign($this->config->getPrivateKey() . $data . $this->config->getPrivateKey())) {
            throw InvalidCallbackRequestException::signature();
        }

        return new StdClassLiqPayPaymentDetails(json_decode(base64_decode($data)));
    }
}
