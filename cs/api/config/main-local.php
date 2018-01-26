<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
           'cookieValidationKey' => 'QbRdBcgWlfoHacgadOJuabnQHTHD1EW4',
           /* 'cookieValidationKey' => false,*/
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
               /* 'text/json' => 'yii\web\JsonParser',*/
            ],


        ],
    ],
];

if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'] // 按需调整这里
    ];
}

return $config;
