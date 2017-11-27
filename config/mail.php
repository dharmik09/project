<?php

return [
    'driver' => env('MAIL_DRIVER','smtp'),
    'host' => env('MAIL_HOST','smtp.gmail.com'),
    'port' => env('MAIL_PORT',587),
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'username' => env('MAIL_USERNAME', "custom.owncloud@gmail.com"),
    'password' => env('MAIL_PASSWORD', "customcloud1234?"),
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
    "pretend" => false,
];
