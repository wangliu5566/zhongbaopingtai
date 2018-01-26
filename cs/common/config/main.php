<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<version:\w+\d+>/<controller:\w+>/<action:\w+>'=>'<version>/<controller>/<action>',
                '<version:\w+\d+>/<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<version>/<module>/<controller>/<action>',
                /*[
                    'class' => 'yii\rest\UrlRule',
                    'controller'=>['<module:\w+\d+>/user']
                ]*/
            ],
        ],


    ],
];

