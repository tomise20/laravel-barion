<?php

/**
 * More information about the available options can be found at: https://docs.barion.com/Main_Page
 */

use Illuminate\Support\Facades\Storage;

return [

    /** Available environments: test, prod */
    'environment' => env('BARION_ENVIRONMENT', 'test'),

    'paymentType' => env('BARION_PAYMENT_TYPE', 'Immediate'),
    'paymentWindow' => env('BARION_PAYMENT_WINDOW', '00:30:00'),
    'guestCheckout' => env('BARION_GUEST_CHECKOUT', true),
    'fundingSources' => ['All'],

    'posKey' => env('BARION_POST_KEY'),
    
    // Barion Wallet api key for wallet authentication
    'apiKey' => env('BARION_API_KEY'),
    'payee' => env('BARION_PAYEE'),
    'redirectUrl' => env('BARION_REDIRECT_URL'),
    'callbackUrl' => env('BARION_CALLBACK_URL'),

    // download path for wallet download. default path local disk/barion
    'downloadPath' => null,

    'locale' => 'hu-HU',
    'currency' => 'HUF',
];