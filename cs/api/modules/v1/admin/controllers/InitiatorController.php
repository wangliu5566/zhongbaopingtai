<?php

namespace api\modules\v1\admin\controllers;

use Yii;
use api\controllers\BaseAPIAuthController;
use api\models\Activity;
use common\helpers\Helper;

class InitiatorController extends BaseAPIAuthController
{

    public function actions()
    {
        return [];
    }

    /**
     * 添加活动用户列表
     * @return [type] [description]
     */
    public function actionRelatedUser()
    {
        $pageSize = Yii::$app->request->get('pageSize', 20);
        $keyword = Yii::$app->request->get('keyword');

        $orWhere = array();
        if(isset($keyword)){
            $orWhere = array(
                array('like', 'companyName', $keyword),
                array('like', 'userName', $keyword)
            );
        }

        $model = new Activity();
        $data = $model->countUserPublish($orWhere, $pageSize);

        return Helper::formatJson(200, 'Ok', $data);
    }

    /**
     * 查询用户发布的活动
     * @return [type] [description]
     */
    public function actionUserActiveList()
    {
        $userId = Yii::$app->request->get('userId');
        $type = Yii::$app->request->get('type');
        $status = Yii::$app->request->get('status');
        $pageSize = Yii::$app->request->get('pageSize', 20);

        if(!$userId){
            return Helper::formatJson(1001, '用户ID不能为空');
        }

        $where['userId'] = intval($userId);
        if($type){
            $where['type'] = intval($type);
        }

        if($status){
            $where['status'] = intval($status);
        }

        $model = new Activity();
        $data = $model->userActivity($where, $pageSize);

        return Helper::formatJson(200, 'Ok', $data);
    }

}