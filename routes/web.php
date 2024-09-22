<?php

use Illuminate\Support\Facades\Route;

Route::post('/cmi/callback', function () {
    // معالجة ردود الدفع من CMI
    return response('Payment Callback Handled', 200);
});
