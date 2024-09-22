 ![CMI-Payment-Gateway](https://raw.githubusercontent.com/baidou5/CMI-Payment-Gateway/main/cmi.jpg)
# CMI Payment Gateway for Laravel
  
[![Latest Version](https://img.shields.io/github/v/release/baidou5/CMI-Payment-Gateway)](https://github.com/baidou5/CMI-Payment-Gateway/releases)
[![License](https://img.shields.io/github/license/baidou5/CMI-Payment-Gateway)](https://github.com/baidou5/CMI-Payment-Gateway/blob/main/LICENSE)

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
    'client_id' => env('CMI_CLIENT_ID', 'your-client-id'),
    'client_secret' => env('CMI_CLIENT_SECRET', 'your-client-secret'),
    'store_key' => env('CMI_STORE_KEY', 'your-store-key'),
    'currency' => env('CMI_CURRENCY', 'MAD'), 
    'callback_url' => env('CMI_CALLBACK_URL', 'your-callback-url'),
];
```

You can also add these environment variables to your `.env` file:

```env
CMI_CLIENT_ID=your-client-id
CMI_CLIENT_SECRET=your-client-secret
CMI_STORE_KEY=your-store-key
CMI_CURRENCY=MAD
CMI_CALLBACK_URL=https://your-domain.com/callback
```

## Usage

To process a payment, you can use the provided `CMIPayment` facade:

1. First, inject the `CMIPayment` service into your controller or use the facade.

2. Example usage in a controller:

```php
use YourNamespace\CMIPaymentGateway\CMIPayment;

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

## Security

If you discover any security issues, please send an email to `baidou.abd@gmail.com` instead of using the issue tracker.

## Support
- **Abdellah Baidou**
- +212 661-176711
- baidou.abd@gmail.com

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

