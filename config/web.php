<?php

$config = [
    'id' => 'app',
    'language' => 'ru-RU',
    'modules' => [
        'settings' => [
            'class' => 'app\modules\settings\SettingsModule',
        ],
        'admin' => [
            'class' => 'app\modules\admin\Module',
            //   'layout' => '@app/views/layouts/admin',

            'layout' => '@app/views/layouts/main.php',
            'as access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin', 'client'],
                    ]
                ]
            ],

            'modules' => [
                'url' => [
                    'class' => 'app\modules\url\UrlModule',
                    'controllerNamespace' => 'app\modules\url\controllers\backend',
                    'viewPath' => '@app/modules/url/views/backend',
                ],
                'textpage' => [
                    'class' => 'app\modules\textpage\TextpageModule',
                    'controllerNamespace' => 'app\modules\textpage\controllers\backend',
                    'viewPath' => '@app/modules/textpage/views/backend',
                ],

                'google' => [
                    'class' => 'app\modules\google\GoogleModule',
                    'controllerNamespace' => 'app\modules\google\controllers\backend',
                    'viewPath' => '@app/modules/google/views/backend',
                ],
                'image' => [
                    'class' => 'app\modules\image\ImageModule',
                    'controllerNamespace' => 'app\modules\image\controllers\backend',
                    'viewPath' => '@app/modules/image/views/backend',
                ],
                'news' => [
                    'class' => 'app\modules\news\Module',
                    'controllerNamespace' => 'app\modules\news\controllers\backend',
                    'viewPath' => '@app/modules/news/views/backend',
                ],
                'user' => [
                    'class' => 'app\modules\user\Module',
                    'controllerNamespace' => 'app\modules\user\controllers\backend',
                    'viewPath' => '@app/modules/user/views/backend',
                ],

                'test' => [
                    'class' => 'app\modules\test\TestModule',
                    'controllerNamespace' => 'app\modules\test\controllers\backend',
                    'viewPath' => '@app/modules/test/views/backend',
                ],
                'app' => [
                    'class' => 'app\modules\app\AppModule',
                    'controllerNamespace' => 'app\modules\block\controllers\backend',
                    'viewPath' => '@app/modules/block/views/backend',
                ],

                'api' => [
                    'class' => 'app\modules\api\ApiModule',
                    'controllerNamespace' => 'app\modules\api\controllers\backend',
                    'viewPath' => '@app/modules/api/views/backend',
                ],


                'settings' => [
                    'class' => 'app\modules\settings\SettingsModule',
                    'controllerNamespace' => 'app\modules\settings\controllers\backend',
                    'viewPath' => '@app/modules/settings/views/backend',
                ],

            ]
        ],
        'url' => [
            'class' => 'app\modules\url\UrlModule',
            'controllerNamespace' => 'app\modules\url\controllers\frontend',
            'viewPath' => '@app/modules/url/views/frontend',
        ],
        'textpage' => [
            'class' => 'app\modules\textpage\TextpageModule',
            'controllerNamespace' => 'app\modules\textpage\controllers\frontend',
            'viewPath' => '@app/modules/textpage/views/frontend',
        ],
        'google' => [
            'class' => 'app\modules\google\GoogleModule',
            'controllerNamespace' => 'app\modules\google\controllers\frontend',
            'viewPath' => '@app/modules/google/views/frontend',
        ],

        'test' => [
            'class' => 'app\modules\test\TestModule',
            'controllerNamespace' => 'app\modules\test\controllers\frontend',
            'viewPath' => '@app/modules/test/views/frontend',
        ],

        'helper' => [
            'class' => 'app\modules\helper\HelperModule',
        ],
        'news' => [
            'class' => 'app\modules\news\Module',
            'controllerNamespace' => 'app\modules\news\controllers\frontend',
            'viewPath' => '@app/modules/news/views/frontend',
        ],

        'main' => [
            'class' => 'app\modules\main\Module',
            'controllerNamespace' => 'app\modules\main\controllers\frontend',
            'viewPath' => '@app/modules/main/views/frontend',
        ],
        'user' => [
            'class' => 'app\modules\user\Module',
            'controllerNamespace' => 'app\modules\user\controllers\frontend',
            'viewPath' => '@app/modules/user/views/frontend',
        ],

        'app' => [
            'class' => 'app\modules\app\AppModule',
            'controllerNamespace' => 'app\modules\user\controllers\frontend',
            'viewPath' => '@app/modules/user/views/frontend',
        ],
        'api' => [
            'class' => 'app\modules\api\ApiModule',
            'controllerNamespace' => 'app\modules\api\controllers\frontend',
            'viewPath' => '@app/modules/api/views/frontend',
        ],
        'ckeditor' => [
            'class' => 'wadeshuler\ckeditor\Module',
        ],
    ],
    'components' => [

        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-yellow-light',
                ],
            ],
        ],
        'formatter' => [
            'timeZone' => 'Europe/Moscow',
            'dateFormat' => 'dd.m.yyyy h:m',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
        ],

        'user' => [
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => true,
            //  'loginUrl' => ['/'],
            'loginUrl' => ['user/default/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'main/default/error',
        ],
        'request' => [
            'cookieValidationKey' => 'fgdfilgkdfgjk4ljt8923rj32ork2j90rf2iur2',
            'csrfParam' => '_csrffe',
            'baseUrl' => ''
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
        ],
    ],
];



if ($_SERVER['HTTP_HOST'] != 'svoiprog.ru' /*YII_ENV_DEV*/) {

    // configuration adjustments for 'dev' environment

    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],

    ];
    //

    $config['bootstrap'][] = 'gii';

    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => [
            '*',
            '127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'
        ],
        'generators' => [ //here
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'adminlte' => '@vendor/dmstr/yii2-adminlte-asset/gii/templates/crud/simple',
                ]
            ]
        ],
    ];

}

return $config;
