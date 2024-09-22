<?php

namespace baidouabdellah\CMIPaymentGateway;

class CmiPayment
{
    protected $service;

    public function __construct(CmiPaymentService $service)
    {
        $this->service = $service;
    }

    public function pay($amount, $orderId, $description)
    {
        return $this->service->createPayment($amount, $orderId, $description);
    }
}
