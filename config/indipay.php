<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Indipay Service Config
    |--------------------------------------------------------------------------
    |   gateway = CCAvenue / PayUMoney / EBS / Citrus / InstaMojo
    |   view    = File
    */

    'gateway' => 'CCAvenue',                // Replace with the name of default gateway you want to use

    'testMode'  => true,                   // True for Testing the Gateway [For production false]

    'ccavenue' => [                         // CCAvenue Parameters
        'merchantId'  => '130338',
        'accessCode'  => 'AVNQ01FA49CC78QNCC',
        'workingKey' => '1B5BABD2757CEB237A396C43EEB12A47',

        // Should be route address for url() function
        'redirectUrl' => env('INDIPAY_REDIRECT_URL', 'ccavenue/response'),
        'cancelUrl' => env('INDIPAY_CANCEL_URL', 'ccavenue/response'),

        'currency' => env('INDIPAY_CURRENCY', 'INR'),
        'language' => env('INDIPAY_LANGUAGE', 'EN'),
    ],

    // Add your response link here. In Laravel 5.2 you may use the api middleware instead of this.
    'remove_csrf_check' => [
        'ccavenue/response'
    ],





];
