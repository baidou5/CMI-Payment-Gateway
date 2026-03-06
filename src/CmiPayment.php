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
namespace Baidouabdellah\CmiPaymentGateway;

class CmiPayment
{
    protected CmiPaymentService $service;

    public function __construct(CmiPaymentService $service)
    {
        $this->service = $service;
    }

    /**
     * @param int|float|string $amount
     * @param array<string, int|float|string|bool|null> $params
     * @return array<string, mixed>
     */
    public function pay($amount, string $orderId, string $description, array $params = []): array
    {
        return $this->service->createPayment($amount, $orderId, $description, $params);
    }
}
