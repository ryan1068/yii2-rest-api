<?php
return [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        'yecai' => [
            'class' => 'api\services\YecaiService',
            'host' => '',
            'client' => '',
            'secret' => '',
            'scope' => '',
            'grantType' => '',
        ],
    ],
];
