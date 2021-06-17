# Laravel Liqpay Client

[![Code Coverage](https://img.shields.io/github/v/release/Digital-Threads/Liqpay)](https://github.com/Digital-Threads/Liqpay/releases) [![License](https://img.shields.io/github/license/digital-threads/liqpay)](https://github.com/Digital-Threads/Liqpay/blob/master/LICENSE) [![Code Coverage](https://codecov.io/gh/Digital-Threads/Liqpay/branch/master/graph/badge.svg)](https://codecov.io/gh/Digital-Threads/Liqpay)

## Installation

Run `composer require digital-threads/liqpay`

## Configurations

[LiqPay](https://www.liqpay.ua) client requires following configurations to be set in your environment:

| Key                       | Description                                                                         |
| ------------------------- | ----------------------------------------------------------------------------------- |
| `LIQPAY_PUBLIC_KEY`       | [LiqPay Public Key](https://www.liqpay.ua/ru/registration)                          |
| `LIQPAY_PRIVATE_KEY`      | [LiqPay Private Key](https://www.liqpay.ua/ru/registration)                         |
| `LIQPAY_DEFAULT_CURRENCY` | Default order currency that will be used if none will be specified for each request |

Alternatively you can publish package configurations and specify your own boundaries:

`php artisan vendor:publish --provider='DigitalThreads\LiqPay\LiqPayServiceProvider' --tag='config'`

## Usage

After package configurations were specified you can use `DigitalThreads\LiqPay\LiqPay` facade for your payment operations.

### Checkout

In order to render LiqPay form you may want to securly recieve [Checkout encoded form parameters](https://www.liqpay.ua/documentation/api/aquiring/checkout/doc) from your backend API like following:

#### PaymentController.php

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use DigitalThreads\LiqPay\LiqPay;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function checkout($orderId)
    {
        $order = Order::findOrFail($orderId);

        $prerequisites = LiqPay::getCheckoutFormPrerequisites([
            'amount' => $order->amount,
            'description' => $order->description,
            'order_id' => $order->id,
            'result_url' => route('web.checkout'),
            'server_url' => route('api.liqpay_callback'), // The url that wil be used for order webhook notification
            'currency' => $order->currency, // Optional. If not set - default currency will be used.
        ]);

        return new JsonResponse([
            'action' => $prerequisites->getAction(),
            'data' => $prerequisites->getData(),
            'signature' => $prerequisites->getSignature(),
        ]);
    }
}
```

#### api.php

```php
<?php

use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\Api\PaymentController;

Route::get('{order}/checkout', [PaymentController::class, 'checkout']);
```

Then you can render the form with your favorite front-end framework like VueJS:

```html
<template>
  <form method="POST" action="{{ form.action }}" accept-charset="utf-8">
    <input type="hidden" name="data" value="{{ form.data }}" />
    <input type="hidden" name="signature" value="{{ form.signature }}" />
    <input
      type="image"
      src="//static.liqpay.ua/buttons/p1en.radius.png"
      name="btn_text"
    />
  </form>
</template>

<script>
  export default {
    data() {
      return {
        orderId: 1,
        form: {
          action: null,
          data: null,
          signature: null,
        },
      };
    },
    async mounted() {
      const response = await fetch(`{your-api-url}/${this.orderId}/checkout`);
      this.form = response.json();
    },
  };
</script>
```

### Callback Validation

During payment processing your API will recieve a [Callback](https://www.liqpay.ua/documentation/api/callback) post request with url that was specified as `server_url` in the `PaymentController`. You will need to register callback handler route in order to update order status according to the data in the request.

#### PaymentController.php

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use DigitalThreads\LiqPay\LiqPay;
use App\Http\Controllers\Controller;
use DigitalThreads\LiqPay\Exceptions\InvalidCallbackRequestException;

class PaymentController extends Controller
{
    public function checkout($orderId)
    {
        $order = Order::findOrFail($orderId);

        $prerequisites = LiqPay::getCheckoutFormPrerequisites([
            'amount' => $order->amount,
            'description' => $order->description,
            'order_id' => $order->id,
            'result_url' => route('web.checkout'),
            'server_url' => route('api.liqpay_callback'), // The url that wil be used for order webhook notification
            'currency' => $order->currency, // Optional. If not set - default currency will be used.
        ]);

        return new JsonResponse([
            'action' => $prerequisites->getAction(),
            'data' => $prerequisites->getData(),
            'signature' => $prerequisites->getSignature(),
        ]);
    }

    public function callback(Request $request)
    {
        try {
            $payload = LiqPay::validateCallback($request);
            $order = Order::findOrFail($payload->get('order_id'));

            $order->update(['status' => $payload->get('status')]);
        } catch (InvalidCallbackRequestException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
```

#### api.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

Route::get('{order}/checkout', [PaymentController::class, 'checkout']);
Route::post('callback', [PaymentController::class, 'callback'])->name('liqpay_callback');
```

`LiqPay::validateCallback` method will take care of the request validation and signature checks and will return an instance of `LiqPayPaymentDetailsInterface`, use it to extract order details data for your needs.

## Credits

- [Stas Vartanyan](https://github.com/vaawebdev)
- [Digital Threads](https://github.com/Digital-Threads)
