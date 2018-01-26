<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/8/1
 * Time: 16:57
 */

namespace api\modules\v1\admin\controllers;


use api\controllers\BaseAPIAuthController;
use api\models\SystemUser;
use common\helpers\Helper;

class SystemController extends BaseAPIAuthController
{
    /*
     * 添加后台用户
     */
    public function actionAddUser(){
        if(\Yii::$app->request->isAjax){
            $systemUser = new SystemUser();
            if($systemUser->load(\Yii::$app->request->post(),'') && $systemUser->validate()) {
                $systemUser->password = \Yii::$app->security->generatePasswordHash(\Yii::$app->request->post('password'));
                $systemUser->save();
                //为用户添加角色
                $userId = $systemUser->id;
                $auth = \Yii::$app->authManager;
                $role = $auth->getRole(\Yii::$app->request->post('role'));
                $res = $auth->assign($role,$userId);
                if($res){
                    return Helper::formatJson(1);
                }else{
                    return Helper::formatJson(1002);
                }
            }else{
                return Helper::formatJson(1002,'数据有误');
            }
        }else{
            return Helper::formatJson(1003,'请求方式不正确');
        }
    }

    /**
     *用户分页显示
     */
    public function actionSystemIndex(){
        /*每页显示数量*/
        $pageSize = 10;
        /*当前页码*/
        $currentPage = \Yii::$app->request->post('currentPage') ?? 1;
        $condition = \Yii::$app->request->post('condition') ?? '1=1';
        /*case when activityEndTime>'.time().' then 未开始 else 已结束*/
        $systemUser = SystemUser::find()
            ->joinWith(['authAssignment'])
            ->select(['id','username','realName','agency','department','signTime','{{%auth_assignment}}.item_name']);

        /*总条数*/
        $count = $systemUser->count();
        /*计算偏移量*/
        $offset = ($currentPage-1) * $pageSize;
        /*分页数据*/
        $data = $systemUser
            ->where($condition)
            ->offset($offset)
            ->limit($pageSize)
            ->all();
        $data['count'] = $count;
        return Helper::formatJson(1,'',$data);
    }

}