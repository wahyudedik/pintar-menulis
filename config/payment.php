<?php

return [

    'midtrans' => [
        'enabled'     => env('MIDTRANS_ENABLED', false),
        'environment' => env('MIDTRANS_ENVIRONMENT', 'sandbox'),
        'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
        'client_key'  => env('MIDTRANS_CLIENT_KEY', ''),
        'server_key'  => env('MIDTRANS_SERVER_KEY', ''),
        'snap_url'    => env('MIDTRANS_ENVIRONMENT', 'sandbox') === 'production'
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js',
    ],

    'xendit' => [
        'enabled'       => env('XENDIT_ENABLED', false),
        'environment'   => env('XENDIT_ENVIRONMENT', 'sandbox'),
        'secret_key'    => env('XENDIT_SECRET_KEY', ''),
        'public_key'    => env('XENDIT_PUBLIC_KEY', ''),
        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', ''),
        'base_url'      => 'https://api.xendit.co',
    ],

];
