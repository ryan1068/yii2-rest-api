<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'call-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'runtimePath' => '@common/../../runtime/call-console',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => '{{%cc_ucenter_auth_item}}',
            'itemChildTable' => '{{%cc_ucenter_auth_item_child}}',
            'assignmentTable' => '{{%cc_ucenter_auth_assignment}}',
            'ruleTable' => '{{%cc_ucenter_auth_rule}}',
            'cache' => 'cache',
            'cacheKey' => 'call-center-rbac',
        ],
    ],
    'params' => $params,
];
