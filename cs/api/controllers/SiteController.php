<?php
namespace api\controllers;

use Yii;

/**
 * Site controller
 */
class SiteController extends BaseAPIController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return [type] [description]
     */
    public function actionIndex()
    {
        return [];
    }

}
