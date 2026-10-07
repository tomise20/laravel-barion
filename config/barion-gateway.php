<?php

/**
 * More information about the available options can be found at: https://docs.barion.com/Main_Page
 */

return [

    /** Available environments: test (sandbox), prod */
    'environment' => env('BARION_ENVIRONMENT', 'test'),

    /** Immediate, Reservation or DelayedCapture (see Tomise\Barion\Enums\PaymentType) */
    'paymentType' => env('BARION_PAYMENT_TYPE', 'Immediate'),

    /** How long the customer has to pay on the Barion page, "hh:mm:ss" */
    'paymentWindow' => env('BARION_PAYMENT_WINDOW', '00:30:00'),

    /** Reservation payments: how long they can be finished, "d.hh:mm:ss", at most 1 year (unfinished ones are refunded) */
    'reservationPeriod' => env('BARION_RESERVATION_PERIOD', '7.00:00:00'),

    /** DelayedCapture payments: how long they can be captured, "d.hh:mm:ss", at most 7 days (21 days for Hungarian shops) */
    'delayedCapturePeriod' => env('BARION_DELAYED_CAPTURE_PERIOD', '7.00:00:00'),

    'guestCheckout' => env('BARION_GUEST_CHECKOUT', true),
    'fundingSources' => ['All'],

    /** The secret key of your Barion shop (POS); BARION_POST_KEY is the old, misspelled name */
    'posKey' => env('BARION_POS_KEY', env('BARION_POST_KEY')),

    // Barion Wallet api key for wallet authentication
    'apiKey' => env('BARION_API_KEY'),

    /** The e-mail address of the Barion wallet that receives the money */
    'payee' => env('BARION_PAYEE'),

    'redirectUrl' => env('BARION_REDIRECT_URL'),
    'callbackUrl' => env('BARION_CALLBACK_URL'),

    /** Seconds to wait for Barion's answer */
    'timeout' => env('BARION_TIMEOUT', 30),

    /** Overrides of the API and the payment page address, e.g. a local fake Barion server for tests (empty: by environment) */
    'apiUrl' => env('BARION_API_URL'),
    'gatewayUrl' => env('BARION_GATEWAY_URL'),

    // Folder of the wallet statement downloads on the default disk.
    'downloadPath' => null,

    'locale' => 'hu-HU',
    'currency' => 'HUF',

    // If you want to sync the payment request with the order number, set to true
    'sync_payment_request_id' => false,
];
