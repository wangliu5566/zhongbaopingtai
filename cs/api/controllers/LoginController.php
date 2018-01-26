<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/7/17
 * Time: 16:34
 */

namespace api\controllers;


use api\models\Test;
use Symfony\Component\Console\Helper\Helper;
use yii\web\Controller;

class LoginController extends Controller
{
    public $layout=true;
    public function actionIndex()
    {
        return 2;

    }


}