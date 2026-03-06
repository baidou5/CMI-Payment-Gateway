<?php

namespace Baidouabdellah\CmiPaymentGateway\Facades;

use Illuminate\Support\Facades\Facade;

class CmiPayment extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Baidouabdellah\CmiPaymentGateway\CmiPayment::class;
    }
}
