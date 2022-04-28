<?php

return [
    # End point URL of connect which will be used to access data
    'endpoint_url' => env('CONNECT_ENDPOINT_URL'),

    # Client ID & Client Secret (to make connection and access data)
    'client_merchant_id' => env('CONNECT_CLIENT_MERCHANT_ID'),
    'client_merchant_secret' => env('CONNECT_CLIENT_MERCHANT_SECRET'),

    'client_fulfiller_id' => env('CONNECT_CLIENT_FULFILLER_ID'),
    'client_fulfiller_secret' => env('CONNECT_CLIENT_FULFILLER_SECRET'),

    'shop_url' => env('CONNECT_SHOP_URL'),

    'callback_url' => env('CALLBACK_URL'),
];
