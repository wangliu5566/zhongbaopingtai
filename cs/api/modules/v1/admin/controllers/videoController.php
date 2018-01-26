<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/6/30
 * Time: 14:08
 */

namespace api\modules\v1\admin\controllers;


use api\controllers\BaseAPIAuthController;

class videoController extends BaseAPIAuthController
{
    public function actionGetVideoPlayer(){
        /*获取视频地址*/
        $videoSrc = \Yii::$app->request->post('videoSrc');
        /*获取模板*/
        $videoModel = fopen('videoModel.html','r+');
        var_dump($videoModel);exit;

    }

}