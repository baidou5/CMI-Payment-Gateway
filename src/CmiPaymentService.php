<?php

namespace baidouabdellah\CMIPaymentGateway;

use GuzzleHttp\Client;

class CmiPaymentService
{
    protected $merchantId;
    protected $apiKey;
    protected $secretKey;
    protected $sandbox;
    protected $callbackUrl;

    public function __construct()
    {
        $this->merchantId = config('cmi.merchant_id');
        $this->apiKey = config('cmi.api_key');
        $this->secretKey = config('cmi.secret_key');
        $this->sandbox = config('cmi.sandbox');
        $this->callbackUrl = config('cmi.callback_url');
    }

    public function createPayment($amount, $orderId, $description)
    {
        $endpoint = $this->sandbox ? "https://sandbox.cmi.co.ma/api" : "https://cmi.co.ma/api";

        // بناء الطلب للدفع
        $data = [
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
            'order_id' => $orderId,
            'description' => $description,
            'callback_url' => $this->callbackUrl,
        ];

        // إرسال الطلب باستخدام Guzzle
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
