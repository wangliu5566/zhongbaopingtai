<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/7/20
 * Time: 17:45
 */

namespace api\modules\v1\admin\controllers;


use api\controllers\BaseAPIAuthController;
use api\models\Activity;
use common\helpers\Helper;
use common\helpers\Image;
use common\helpers\Thumbnail;
use yii\rest\ViewAction;
use yii\web\UploadedFile;
use dosamigos\qrcode;

class ImageController extends BaseAPIAuthController
{
    /**
     * 保存图片
     * @return array
     */
    public function actionSaveActivityBannerImage(){
        if(\Yii::$app->request->isPost){
            /*保存地址*/
            $activityImageName = 'file';

            $imageDetail = '\data\activityImage\banner\\'.date('Y-m-d',time());
            $image = new Image($_FILES,$activityImageName,$imageDetail);
            $res =  $image->saveImage();
            $data['pcImage'] = $res;
            $thumbnail = new Thumbnail('750','195');
            $thumbImage =  $thumbnail->makeThumb(\Yii::$app->basePath.'\web'.$res,
                \Yii::$app->basePath.'\web'.$imageDetail,false);
            $data['mobileImage'] = $imageDetail.'\\'.$thumbImage;
           if(is_string($res)){
               return Helper::formatJson('200','',$data);
           }else{
               return Helper::formatJson(1003);
           }
        }else{
            return Helper::formatJson(1003);
        }
    }


    public function actionSaveActivitySmallImage(){
        if(\Yii::$app->request->isPost){
            /*保存地址*/
            $activityImageName = 'file';
            $imageDetail = '\data\activityImage\small\\'.date('Y-m-d',time());
            $image = new Image($_FILES,$activityImageName,$imageDetail);
            $res =  $image->saveImage();
            $thumbnail = new Thumbnail('264','190');
            $thumbImage =  $thumbnail->makeThumb(\Yii::$app->basePath.'\web'.$res,\Yii::$app->basePath.'\web'.$imageDetail);



            if(is_string($res)){
                return Helper::formatJson('200','',['smallImage'=>$imageDetail.'\\'.$thumbImage,'bigImage'=>$res]);
            }else{
                return Helper::formatJson(1003);
            }
        }else{
            return Helper::formatJson(1003);
        }
    }

    public function actionSaveActivityIntroImage(){
        if(\Yii::$app->request->isPost){
            /*保存地址*/
            $activityImageName = 'file';
            $imageDetail = '\data\activityImage\intro\\'.date('Y-m-d',time());
            $image = new Image($_FILES,$activityImageName,$imageDetail);
            $res =  $image->saveImage();
            $thumbnail = new Thumbnail('558','368');
            $thumbImage =  $thumbnail->makeThumb(\Yii::$app->basePath.'\web'.$res,\Yii::$app->basePath.'\web'.$imageDetail);
            if(is_string($res)){
                return Helper::formatJson('200','',['introImage'=>$imageDetail.'\\'.$thumbImage]);
            }else{
                return Helper::formatJson(1003);
            }
        }else{
            return Helper::formatJson(1003);
        }
    }

    /**
     * 新聞封面圖片處理
     * @return array
     */
    public function actionSaveNewsCoverImage(){
        if(\Yii::$app->request->isPost){
            /*保存地址*/
            $activityImageName = 'file';
            $imageDetail = '\data\newsImage\cover\\'.date('Y-m-d',time());
            $image = new Image($_FILES,$activityImageName,$imageDetail);
            $res =  $image->saveImage();
            $thumbnail = new Thumbnail('264','140');
            $thumbImage =  $thumbnail->makeThumb(\Yii::$app->basePath.'\web'.$res,\Yii::$app->basePath.'\web'.$imageDetail);
            $headImage = $this->actionSaveNewsHeadImage();
            if(is_string($res)){
                return Helper::formatJson('200','',['coverPath'=>$imageDetail.'\\'.$thumbImage,'headImage'=>$imageDetail.'\\'.$headImage]);
            }else{
                return Helper::formatJson(1003);
            }
        }else{
            return Helper::formatJson(1003);
        }
    }

    /**
     * 新聞正文图片处理
     * @return array
     */
    public function actionSaveNewsHeadImage(){

            /*保存地址*/
            $activityImageName = 'file';
            $imageDetail = '\data\newsImage\cover\\'.date('Y-m-d',time());
            $image = new Image($_FILES,$activityImageName,$imageDetail);
            $res =  $image->saveImage();
            $thumbnail = new Thumbnail('1200','632');
            $thumbnail->thumbName = 'head_';
            $thumbImage =  $thumbnail->makeThumb(\Yii::$app->basePath.'\web'.$res,\Yii::$app->basePath.'\web'.$imageDetail);
            return  $thumbImage;
    }

    public function actionSaveNewsMainImage(){
        if(\Yii::$app->request->isPost){
            /*保存地址*/
            $activityImageName = 'file';
            $imageDetail = '\data\newsImage\main\\'.date('Y-m-d',time());
            $image = new Image($_FILES,$activityImageName,$imageDetail);
            $res =  $image->saveImage();
            if(is_string($res)){
                return Helper::formatJson('200','',$res);
            }else{
                return Helper::formatJson(1003);
            }
        }else{
            return Helper::formatJson(1003);
        }
    }

    public function actionSaveWorkCoverImage(){
        if(\Yii::$app->request->isPost){
            /*保存地址*/
            $activityImageName = 'file';
            $imageDetail = '\data\worksImage\worksCover\\'.date('Y-m-d',time());
            $image = new Image($_FILES,$activityImageName,$imageDetail);
            $res =  $image->saveImage();
            if(is_string($res)){
                return Helper::formatJson('200','',$res);
            }else{
                return Helper::formatJson(1003);
            }
        }else{
            return Helper::formatJson(1003);
        }
    }

    /**
     * 生成二维码
     * @return array
     */
    public function actionQrCode($detail,$matrixPointSize=4){
        $errorCorrectionLevel = 'L';  // 纠错级别：L、M、Q、H
        ob_start();
       qrcode\QrCode::png($detail,false,$errorCorrectionLevel,$matrixPointSize,2);
        $data =ob_get_contents();
        ob_end_clean();
        return Helper::formatJson(200,'',base64_encode($data));

    }



}