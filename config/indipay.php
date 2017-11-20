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

    'testMode'  => false,                   // True for Testing the Gateway [For production false]

    'ccavenue' => [                         // CCAvenue Parameters
        'merchantId'  => '130338',
        'accessCode'  => 'AVDJ70ED21AY50JDYA',
        'workingKey' => '3B83F2DA86F1C30D1FCAD48A01D00F45',

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
