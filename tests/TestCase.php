<?php

namespace Baidouabdellah\CmiPaymentGateway\Tests;

use Baidouabdellah\CmiPaymentGateway\CmiPaymentServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            CmiPaymentServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('cmi.client_id', '100001');
        $app['config']->set('cmi.store_key', 'testStoreKey');
        $app['config']->set('cmi.base_uri', 'https://testpayment.cmi.co.ma/fim/est3Dgate');
        $app['config']->set('cmi.ok_url', 'https://example.com/cmi/ok');
        $app['config']->set('cmi.fail_url', 'https://example.com/cmi/fail');
        $app['config']->set('cmi.shop_url', 'https://example.com/checkout');
        $app['config']->set('cmi.callback_url', 'https://example.com/cmi/callback');
        $app['config']->set('cmi.lang', 'fr');
        $app['config']->set('cmi.currency', '504');
    }
}
