<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/6/27
 * Time: 15:38
 */

namespace api\modules\v1\academic\controllers;


use api\controllers\BaseAPIAuthController;
use api\models\User;

class UserController extends BaseAPIAuthController
{
    /**获取用户列表
     * @return array
     */
    public function actionLists(){
        $groupId = 0;
        $department = null;
        $key = null;
        $page = 0;
        $pageSize = 2;
        if(\Yii::$app->request->isGet) {
            $params = \Yii::$app->request->get();
            extract($params);
        }
        $user = new User();
        $lists = $user->getUsersBySearch($groupId,$department,$key,$page,$pageSize);
        return $lists;
    }

    /**
     * 添加用户
     */
    public function actionAdd()
    {
        if(\Yii::$app->request->isPost) {
            $user = new User();
            $data = \Yii::$app->request->post();
            $data['password'] = \Yii::$app->getSecurity()->generatePasswordHash($data['password']);
            $data['access_token'] = \Yii::$app->security->generateRandomString();
            $user->modify($data);
            $user->scenario = 'add';
            if(!$user->save()) {
                return [
                    'data' => $user->getErrors(),
                    'message' => '添加失败',
                    'status' => 200,
                ];
            }else {
                return [
                    'data' => '',
                    'message' => '添加成功',
                    'status' => 200,
                ];
            }
        }
    }

    /**修改用户信息
     * @return array
     */
    public function actionEdit()
    {
        if(\Yii::$app->request->isGet) {
            $id = \Yii::$app->request->get('userId');
            //通过ID找到该用户信息
            $user = User::findOne(intval($id));
            if(!$user) {
                //并没有相关用户
                return [
                    'data' => '',
                    'message' => '没有相关用户信息',
                    'status' => 200,
                ];
            }else {
                $user->password = '******';
                return $user;
            }
        }
        if(\Yii::$app->request->isPost) {
            $id = \Yii::$app->request->post('userId');
            //通过ID找到该用户信息
            $user = User::findOne(intval($id));
            if(!$user) {
                //并没有相关用户
                return [
                    'data' => '',
                    'message' => '没有相关用户信息',
                    'status' => 200,
                ];
            }
            $data = \Yii::$app->request->post();
            if(isset($data['password'])) {
                if($data['password'] == '******') {
                    $data['password'] = $user->password;
                }else {
                    $data['password'] = \Yii::$app->getSecurity()->generatePasswordHash($data['password']);
                }
            }
            $user->modify($data);
            $user->scenario = 'edit';
            if(!$user->save()) {
                return [
                    'data' => $user->getErrors(),
                    'message' => '修改失败',
                    'status' => 200,
                ];
            }else {
                return [
                    'data' => '',
                    'message' => '修改成功',
                    'status' => 200,
                ];
            }

        }
    }

    /**用户软删除
     * @return array
     */
    public function actionDeleted()
    {
        if(\Yii::$app->request->isGet) {
            $id = \Yii::$app->request->get('userId');
            //通过ID找到该用户信息
            $user = User::findOne(intval($id));
            if(!$user) {
                //并没有相关用户
                return [
                    'data' => '',
                    'message' => '没有相关用户信息',
                    'status' => 200,
                ];
            }
            $user->isDeled = IS_DELETED['deleted'];
            if(!$user->save()) {
                return $user->getErrors();
            }else {
                return [
                    'data' => '',
                    'message' => '删除成功',
                    'status' => 200,
                ];
            }
        }
    }
}