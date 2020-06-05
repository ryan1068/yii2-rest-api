<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.2.240;port=5306;dbname=',
            'username' => '',
            'password' => '',
            'charset' => '',
        ],
        'mongodb' => [
            'class' => '\yii\mongodb\Connection',
            'dsn' => 'mongodb://192.168.2.240:11708/w_center',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '192.168.2.240',
            'port' => 6379,
            'database' => 0,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
