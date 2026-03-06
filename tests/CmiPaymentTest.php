<?php

use Baidouabdellah\CmiPaymentGateway\CmiPayment;
use Baidouabdellah\CmiPaymentGateway\CmiPaymentService;

it('resolves payment services from the container', function () {
    expect(app(CmiPaymentService::class))->toBeInstanceOf(CmiPaymentService::class);
    expect(app(CmiPayment::class))->toBeInstanceOf(CmiPayment::class);
});

it('builds hosted form payload and hash', function () {
    /** @var CmiPaymentService $service */
    $service = app(CmiPaymentService::class);

    $result = $service->createPayment(120.50, 'ORDER-1001', 'Test payment', [
        'email' => 'customer@example.com',
    ]);

    expect($result)->toHaveKeys(['gateway_url', 'payload', 'hash', 'transport']);
    expect($result['payload'])->toBeArray();
    /** @var array<string, mixed> $payload */
    $payload = $result['payload'];
    expect($payload)->toHaveKey('hash');
    expect($payload['oid'] ?? null)->toBe('ORDER-1001');
    expect($result['transport'])->toBe('hosted_form');
});
