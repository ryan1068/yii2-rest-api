<?php
return [
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'formatter' => [
            'dateFormat' => 'php:Y-m-d',
            'timeFormat' => 'php:H:i:s',
            'datetimeFormat' => 'php:Y-m-d H:i:s',
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => 'redis'
        ],
        'http' => [
            'class' => yii\httpclient\Client::class,
            'transport' => 'yii\httpclient\CurlTransport',
        ],
        'queue' => [
            'class' => yii\queue\redis\Queue::class,
            'channel' => 'queue',
            'as log' => common\behaviors\QueueLogBehavior::class,
        ],
        'i18n' => [
            'translations' => [
                'exception*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
                'yii*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
    ],
];
