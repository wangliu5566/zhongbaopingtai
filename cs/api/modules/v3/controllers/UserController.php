<?php

namespace api\modules\v3\controllers;

use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'api\models\User';
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionTest(){
        var_dump(2);exit;
    }

}
