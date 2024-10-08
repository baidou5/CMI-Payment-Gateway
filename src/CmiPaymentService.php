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
 namespace baidouabdellah\CMIPaymentGateway;

 use GuzzleHttp\Client;

 class CmiPaymentService
 {
     protected $merchantId;
     protected $clientId;
     protected $storeKey;
     protected $apiKey;
     protected $secretKey;
     protected $sandbox;
     protected $baseUri;
     protected $okUrl;
     protected $failUrl;
     protected $shopUrl;
     protected $callbackUrl;

     public function __construct()
     {
         $this->merchantId = config('cmi.merchant_id');
         $this->clientId = config('cmi.client_id');
         $this->storeKey = config('cmi.store_key');
         $this->apiKey = config('cmi.api_key');
         $this->secretKey = config('cmi.secret_key');
         $this->sandbox = config('cmi.sandbox');
         $this->baseUri = config('cmi.base_uri');
         $this->okUrl = config('cmi.ok_url');
         $this->failUrl = config('cmi.fail_url');
         $this->shopUrl = config('cmi.shop_url');
         $this->callbackUrl = config('cmi.callback_url');
     }

     public function createPayment($amount, $orderId, $description)
     {
         $endpoint = $this->sandbox ? "https://sandbox.cmi.co.ma/api" : $this->baseUri;

         // Build the order for payment
         $data = [
             'merchant_id' => $this->merchantId,
             'client_id' => $this->clientId,
             'store_key' => $this->storeKey,
             'amount' => $amount,
             'order_id' => $orderId,
             'description' => $description,
             'callback_url' => $this->callbackUrl,
             'ok_url' => $this->okUrl,
             'fail_url' => $this->failUrl,
             'shop_url' => $this->shopUrl,
         ];

         // Send the request using Guzzle
         return $this->sendRequest($endpoint, $data);
     }

     protected function sendRequest($url, $data)
     {
         $client = new Client();
         $response = $client->post($url, [
             'form_params' => $data,
             'headers' => [
                 'Authorization' => 'Bearer ' . $this->apiKey,
             ],
         ]);

         return json_decode($response->getBody(), true);
     }
 }
