<?php

namespace DigitalThreads\LiqPay\Tests;

use LiqPay;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Http\Request;
use DigitalThreads\LiqPay\LiqPaySdkClient;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertEquals;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\ExpectationFailedException;
use DigitalThreads\LiqPay\Dto\LiqPayConfigurations;
use DigitalThreads\LiqPay\Contracts\LiqPayClientInterface;
use DigitalThreads\LiqPay\Contracts\LiqPayConfigurationsInterface;
use DigitalThreads\LiqPay\Exceptions\InvalidCallbackRequestException;
use DigitalThreads\LiqPay\Contracts\LiqPayCheckoutFormPrerequisitesInterface;

final class LiqPaySdkClientTest extends AbstractTestCase
{
    use WithFaker;

    /**
     * @var MockInterface
     */
    private $sdk;

    /**
     * @var LiqPayConfigurationsInterface
     */
    private $config;

    /**
     * @var LiqPaySdkClient
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->client = new LiqPaySdkClient(
            $this->sdk = Mockery::mock(LiqPay::class),
            $this->config = new LiqPayConfigurations(
                $this->faker->sha1,
                $this->faker->sha1,
                LiqPayClientInterface::CURRENCY_USD
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->sdk = null;
        $this->config = null;
        $this->client = null;
    }

    /**
     * @param  array  $source
     * @param  string $signature
     * @return void
     */
    private function assertSourceWasSigned(array $source, string $signature): void
    {
        $this->sdk
            ->shouldReceive('cnb_signature')
            ->withArgs([$source])
            ->once()
            ->andReturn($signature);
    }

    /**
     * @param  array  $source
     * @param  string $signature
     * @return void
     */
    private function assertStringWasSigned(string $source, string $signature): void
    {
        $this->sdk
            ->shouldReceive('str_to_sign')
            ->withArgs([$source])
            ->once()
            ->andReturn($signature);
    }

    /**
     * @param  LiqPayCheckoutFormPrerequisitesInterface $result
     * @param  array                                    $source
     * @param  string                                   $signature
     * @throws ExpectationFailedException
     * @return void
     */
    private function assertValidResult(LiqPayCheckoutFormPrerequisitesInterface $result, array $source, string $signature): void
    {
        assertSame(LiqPayClientInterface::CHECKOUT_URL, $result->getAction());
        assertSame($signature, $result->getSignature());
        assertEquals($source, json_decode(base64_decode($result->getData()), true));
    }

    /**
     * @return array
     */
    private function getDefaultSource(): array
    {
        return [
            'version' => LiqPayClientInterface::API_VERSION,
            'public_key' => $this->config->getPublicKey(),
            'currency' => $this->config->getDefaultCurrency(),
        ];
    }

    /**
     * @param  array   $body
     * @return Request
     */
    private function makeRequest(array $body = []): Request
    {
        $request = new Request();
        $request->merge($body);

        return $request;
    }

    public function testGetCheckoutFormPrerequisitesWithEmptyParams(): void
    {
        $this->assertSourceWasSigned($default = $this->getDefaultSource(), $signature = $this->faker->sha256);
        $this->assertValidResult($this->client->getCheckoutFormPrerequisites([]), $default, $signature);
    }

    public function testGetCheckoutFormPrerequisitesOverrideDefaultCurrency(): void
    {
        $params = [
            'currency' => LiqPayClientInterface::CURRENCY_RUB,
        ];

        $this->assertSourceWasSigned($merged = array_merge($this->getDefaultSource(), $params), $signature = $this->faker->sha256);
        $this->assertValidResult($this->client->getCheckoutFormPrerequisites($params), $merged, $signature);
    }

    public function testGetCheckoutFormPrerequisitesDoNotOverrideVersionAndPublicKey(): void
    {
        $params = [
            'version' => $this->faker->numberBetween(10, 50),
            'public_key' => $this->faker->slug,
        ];

        $this->assertSourceWasSigned($default = $this->getDefaultSource(), $signature = $this->faker->sha256);
        $this->assertValidResult($this->client->getCheckoutFormPrerequisites($params), $default, $signature);
    }

    public function testGetCheckoutFormPrerequisitesMergeWithRandomKeysAndValues(): void
    {
        $params = collect(array_fill(0, 10, 0))
            ->mapWithKeys(function () {
                return [$this->faker->sha256 => $this->faker->realText];
            })
            ->toArray();

        $this->assertSourceWasSigned($merged = array_merge($this->getDefaultSource(), $params), $signature = $this->faker->sha256);
        $this->assertValidResult($this->client->getCheckoutFormPrerequisites($params), $merged, $signature);
    }

    public function testValidateCallbackThrowInvalidCallbackRequestExceptionIfDataIsNull(): void
    {
        $request = $this->makeRequest();

        $this->expectException(InvalidCallbackRequestException::class);

        $this->client->validateCallback($request);
    }

    public function testValidateCallbackThrowInvalidCallbackRequestExceptionIfSignatureIsNull(): void
    {
        $request = $this->makeRequest(['data' => $this->faker->sha256]);

        $this->expectException(InvalidCallbackRequestException::class);

        $this->client->validateCallback($request);
    }

    public function testValidateCallbackSignDataWithPrivateKeysAndThrowInvalidCallbackRequestExceptionIfMissmatch(): void
    {
        $request = $this->makeRequest([
            'data' => $this->faker->sha256,
            'signature' => $this->faker->sha1,
        ]);

        $this->assertStringWasSigned(
            $this->config->getPrivateKey() . $request->data . $this->config->getPrivateKey(),
            $this->faker->slug
        );

        $this->expectException(InvalidCallbackRequestException::class);

        $this->client->validateCallback($request);
    }

    public function testValidateCallbackSignDataWithPrivateKeysAndReturnDecodedDataIfSignatureMatches(): void
    {
        $request = $this->makeRequest([
            'data' => base64_encode(json_encode($data = ['order_id' => $this->faker->uuid])),
            'signature' => $this->faker->sha1,
        ]);

        $this->assertStringWasSigned(
            $this->config->getPrivateKey() . $request->data . $this->config->getPrivateKey(),
            $request->signature
        );

        $result = $this->client->validateCallback($request);

        assertSame($result->get('order_id'), $data['order_id']);
    }
}
