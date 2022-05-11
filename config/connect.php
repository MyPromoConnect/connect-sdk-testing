<?php

return [
    'endpoint_url'            => env('CONNECT_ENDPOINT_URL'),
    'client_merchant_id'      => env('CONNECT_CLIENT_MERCHANT_ID'),
    'client_merchant_secret'  => env('CONNECT_CLIENT_MERCHANT_SECRET'),
    'client_fulfiller_id'     => env('CONNECT_CLIENT_FULFILLER_ID'),
    'client_fulfiller_secret' => env('CONNECT_CLIENT_FULFILLER_SECRET'),
    'shop_url'                => env('CONNECT_SHOP_URL'),
    'callback_url'            => env('CALLBACK_URL'),
    'test_sku_parent'         => env('TEST_SKU_PARENT'),
    'test_sku_child'          => env('TEST_SKU_CHILD'),
    'test_sku_child_intent'   => env('TEST_SKU_CHILD_INTENT'),
    'test_sku_child_qty'      => env('TEST_SKU_CHILD_QTY'),
];
