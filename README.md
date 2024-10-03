

![CMI-Payment-Gateway](https://raw.githubusercontent.com/baidou5/CMI-Payment-Gateway/main/cmi.jpg)

# CMI Payment Gateway for Laravel
[![Latest Version](https://img.shields.io/github/v/release/baidou5/CMI-Payment-Gateway)](https://github.com/baidou5/CMI-Payment-Gateway/releases)
[![License](https://img.shields.io/github/license/baidou5/CMI-Payment-Gateway)](https://github.com/baidou5/CMI-Payment-Gateway/blob/main/LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-%3E%3D7.0-orange.svg)](https://laravel.com/)
[![Total Downloads](https://img.shields.io/packagist/dt/baidouabdellah/cmi-payment-gateway)](https://packagist.org/packages/baidouabdellah/cmi-payment-gateway)
[![Contributors](https://img.shields.io/github/contributors/baidou5/CMI-Payment-Gateway.svg)](https://github.com/baidou5/CMI-Payment-Gateway/graphs/contributors)
[![Issues](https://img.shields.io/github/issues/baidou5/CMI-Payment-Gateway.svg)](https://github.com/baidou5/CMI-Payment-Gateway/issues)
[![Pull Requests](https://img.shields.io/github/issues-pr/baidou5/CMI-Payment-Gateway.svg)](https://github.com/baidou5/CMI-Payment-Gateway/pulls)
[![GitHub stars](https://img.shields.io/github/stars/baidou5/CMI-Payment-Gateway.svg?style=social)](https://github.com/baidou5/CMI-Payment-Gateway/stargazers)


**CMI-Payment-Gateway** is a simple and secure package for integrating CMI (Centre Monétique Interbancaire) Payment Gateway with Laravel applications. It allows you to process payments in a seamless way by interacting with the CMI API.

## Features

- Seamless integration with Laravel
- Easy to configure and use
- Secure payment processing via CMI
- Supports multiple SHOPs (SAAS) transactions
- Customizable payment callback URL

## Requirements

- PHP 7.4 or higher
- Laravel 7.x or higher
- CMI Merchant account

## Installation

To install the package, use Composer:

```bash
composer require baidouabdellah/cmi-payment-gateway
```

### Publish Configuration

Once installed, publish the configuration file using the following command:

```bash
php artisan vendor:publish --tag="cmi-config"
```

This will create a `cmi.php` file in your `config` directory where you can configure your CMI credentials.

### Configuration

In the `config/cmi.php` file, add your CMI Merchant credentials:

```php
return [
    'merchant_id' => env('CMI_MERCHANT_ID'),
    'client_id' => env('CMI_CLIENT_ID'),
    'store_key' => env('CMI_STORE_KEY'),
    'api_key' => env('CMI_API_KEY'),
    'secret_key' => env('CMI_SECRET_KEY'),
    'sandbox' => env('CMI_SANDBOX', true),
    'base_uri' => env('CMI_BASE_URI', 'https://testpayment.cmi.co.ma/fim/est3Dgate'),
    'ok_url' => env('CMI_OK_URL', 'your_ok_url'),
    'fail_url' => env('CMI_FAIL_URL', 'your_fail_url'),
    'shop_url' => env('CMI_SHOP_URL', 'your_shop_url'),
    'callback_url' => env('CMI_CALLBACK_URL', 'your_callback_url'),
];
```

You can also add these environment variables to your `.env` file:

```env
CMI_MERCHANT_ID=your_merchant_id
CMI_CLIENT_ID=your_client_id
CMI_STORE_KEY=your_store_key
CMI_API_KEY=your_api_key
CMI_SECRET_KEY=your_secret_key
CMI_SANDBOX=true
CMI_BASE_URI=https://testpayment.cmi.co.ma/fim/est3Dgate
CMI_OK_URL=https://yourwebsite.com/payment/success
CMI_FAIL_URL=https://yourwebsite.com/payment/fail
CMI_SHOP_URL=https://yourwebsite.com/payment/cancel
CMI_CALLBACK_URL=https://yourwebsite.com/payment/callback
```

## Usage

To process a payment, you can use the provided `CMIPayment` facade:

1. First, inject the `CMIPayment` service into your controller or use the facade.

2. Example usage in a controller:

```php
use BaidouAbdellah\CMIPaymentGateway\CMIPayment;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        $payment = new CMIPayment();
        $response = $payment->process([
            'amount' => 100.00,  // The amount to charge
            'order_id' => 'ORDER12345',  // Your unique order ID
            'customer_name' => 'Abdellah baidou', // Customer details
            'customer_email' => 'baidou.abd@gmail.com', // Customer Email
        ]);

        return redirect($response->getPaymentUrl()); // Redirect user to CMI payment page
    }
}
```

### Handling Payment Callbacks

In your `web.php` routes file, you should add a route to handle the CMI callback:

```php
Route::post('/cmi/callback', [PaymentController::class, 'handleCallback'])->name('cmi.callback');
```

In your `PaymentController`, handle the callback:

```php
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function handleCallback(Request $request)
    {
        // Handle the response from CMI here
        $paymentStatus = $request->input('STATUS');

        if ($paymentStatus === 'APPROVED') {
            // Payment was successful
            return view('payment.success');
        } else {
            // Payment failed
            return view('payment.failed');
        }
    }
}
```

## Testing

You can test the integration using CMI's sandbox credentials. Ensure you configure the sandbox environment in the `cmi.php` configuration file or use different `.env` variables for testing.

### Basic Test Card Numbers

Credit card information cannot be used in test mode. Instead, use any of the following test card numbers, along with a valid future expiration date and any random CVC number, to simulate a successful payment transaction.

| Card Type            | Card Number         | CVC (Random) | Expiration Date (Future)          |
|----------------------|---------------------|--------------|-----------------------------------|
| Visa                 | 4242 4242 4242 4242 | 3 digits     | Any future date                  |
| Visa (Debit)         | 4000 0566 5566 5556 | 3 digits     | Any future date                  |
| Mastercard           | 5555 5555 5555 4444 | 3 digits     | Any future date                  |
| Mastercard (Series 2)| 2223 0031 2200 3222 | 3 digits     | Any future date                  |
| Mastercard (Debit)   | 5200 8282 8282 8210 | 3 digits     | Any future date                  |
| Mastercard (Prepaid) | 5105 1051 0510 5100 | 3 digits     | Any future date                  |
| American Express     | 3782 822463 10005   | 4 digits     | Any future date                  |
| American Express     | 3714 4963 5398 431  | 4 digits     | Any future date                  |
| Discover             | 6011 1111 1111 1117 | 3 digits     | Any future date                  |
| Discover             | 6011 0009 9013 9424 | 3 digits     | Any future date                  |
| Diners Club          | 3056 9300 0902 0004 | 3 digits     | Any future date                  |
| JCB                  | 3566 0020 2036 0505 | 3 digits     | Any future date                  |

Use these details in your testing environment to verify the successful integration of the CMI payment gateway.

## Security

If you discover any security issues, please send an email to `baidou.abd@gmail.com` instead of using the issue tracker.

## Support
- **Abdellah Baidou**
- +212 661-176711
- baidou.abd@gmail.com

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
