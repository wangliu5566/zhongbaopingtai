<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1'=>[
            'class'=> 'api\modules\v1\Module',
            'modules' => [
                'kckf' => 'api\modules\v1\kckf\Module',
                'academic' => 'api\modules\v1\academic\Module',
                'admin' => 'api\modules\v1\admin\Module',
                'frontend' => [
                    'class' => 'api\modules\v1\frontend\Module'
                ],
            ],
        ],
        'v2'=>[
            'class'=>'api\modules\v2\Module'
        ],
        'v3'=>[
            'class'=>'api\modules\v3\Module'
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event){
                $response = $event->sender;
                if(isset($response->data['code'])){
                    unset($response->data['code']);
                }
                if(isset($response->data['name'])){
                    unset($response->data['name']);
                }
                $message = $response->data['message'] ?? $response->statusText;
                $data = $response->data['data'] ?? $response->data;
                $status = $response->data['status'] ?? $response->getStatusCode();
                if($status === 404){
                    $data = '';
                }
                $response->data = [
                    'message' => $message,
                    'data' => $data,
                    'status' => $status
                ];
            },
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest'],
        ],
    ],
    'params' => $params,
];
