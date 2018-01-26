<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=118.178.92.235;dbname=crowd_test;port=6002',
            'username' => 'qbz',
            'password' => 'qbz123456',
            'charset' => 'utf8',
            'tablePrefix' => 'cs_',
        ],
        'db2' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=118.178.92.235;dbname=share_develop;port=6002',
            'username' => 'qbz',
            'password' => 'qbz123456',
            'charset' => 'utf8',
            'tablePrefix' => 'hawk_',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
