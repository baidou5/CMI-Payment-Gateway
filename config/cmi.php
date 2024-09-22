<?php
phpreturn [
    'merchant_id' => env('CMI_MERCHANT_ID'),
    'api_key' => env('CMI_API_KEY'),
    'secret_key' => env('CMI_SECRET_KEY'),
    'sandbox' => env('CMI_SANDBOX', true),
    'callback_url' => env('CMI_CALLBACK_URL', 'your_callback_url'),
];
