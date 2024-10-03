<?php
/**
 * CMI Payment Gateway Library for Laravel
 *
 * This library provides a simple way to integrate CMI payment gateway into your Laravel application.
 * It allows you to process payments, manage transactions, and handle payment responses from the CMI platform.
 *
 * @package    CMI-Payment-Gateway
 * @version    1.0.0
 * @license    MIT
 * @author     Baidou Abdellah <baidou.abd@gmail.com>
 * @link       https://github.com/baidou5/CMI-Payment-Gateway
 *
 * ### Requirements:
 * - PHP 7.4 or higher
 * - Laravel 8.x or higher
 * - CMI payment account
 *
 * ### License:
 *
 * This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
 */

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
