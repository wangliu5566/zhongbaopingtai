<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/7/31
 * Time: 14:46
 */

namespace api\modules\v1\frontend\controllers;


use api\controllers\BaseAPIAuthController;
use api\models\News;
use common\helpers\Helper;

class NewsController extends BaseAPIAuthController
{
    /**新闻详情
     * @return array
     */
    public function actionGetInfo()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $newsId = intval($request->get('newsId'));
        if(!$newsId) {
            return Helper::formatJson(1001,'缺少参数');
        }
        $news = News::getDetail($newsId);
        return Helper::formatJson(200,'OK',$news);
    }
}