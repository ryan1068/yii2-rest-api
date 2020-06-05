<?php
use api\components\log\MongoTarget;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'runtimePath' => '@common/../../runtime/call-center',
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ]
    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'as format' => 'common\behaviors\ResponseFormatBehavior',
        ],
        'user' => [
            'identityClass' => 'api\resources\AdminUser',
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => MongoTarget::class,
                    'logTable' => ['cc', 'cc_ucenter_log'],
                    'levels' => ['error', 'warning'],
                    'prefix' => function ($message) {
                        return sprintf('[ucenter][%s]', \Yii::$app->requestedRoute);
                    },
                    'logVars' => [],
                    'categories' => [],
                ],
                [
                    'class' => MongoTarget::class,
                    'logTable' => ['cc', 'cc_ucenter_log'],
                    'levels' => ['info'],
                    'prefix' => function ($message) {
                        return sprintf('[ucenter][%s]', \Yii::$app->requestedRoute);
                    },
                    'logVars' => [],
                    'categories' => ['httpCall'],
                ]
            ],
        ],
        'session' => [
            'class' => 'yii\redis\Session',
            'timeout' => 1209600,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/account',
                        'v1/area',
                        'v1/org',
                        'v1/role',
                        'v1/session',
                    ],
                ],
                'POST v1/orgs/<orgId>/admins' => 'v1/org-admin/create',
                'PUT,PATCH v1/orgs/<orgId>/admins/<adminId>' => 'v1/org-admin/update',
                'DELETE v1/orgs/<orgId>/admins/<adminId>' => 'v1/org-admin/delete',
                'GET,HEAD,OPTIONS v1/orgs/<id>/unselected-areas' => 'v1/org/unselected-areas',
                'GET,HEAD,OPTIONS v1/roles/permission-tree' => 'v1/role/permission-tree',
                'PATCH v1/accounts/<id>/enablement' => 'v1/account/enable',
                'PATCH v1/accounts/<id>/disablement' => 'v1/account/disable',
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
            'defaultRoles' => ['administrator'],
        ],
    ],
    'params' => $params,
];
