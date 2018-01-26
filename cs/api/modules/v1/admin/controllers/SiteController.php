<?php

namespace api\modules\v1\admin\controllers;

use api\models\AuthItemChild;
use Yii;
use api\controllers\BaseAPIController;
use common\helpers\Helper;
use api\models\SystemUser;
use yii\helpers\ArrayHelper;

class SiteController extends BaseAPIController
{

	/**
	 * 用户登录
	 * @return [type] [description]
	 */
	public function actionLogin()
	{
		$username = Yii::$app->request->post('username');
		$password = Yii::$app->request->post('password');
		$rememberMe = Yii::$app->request->post('rememberMe');
		$token = Yii::$app->request->post('access_token');

		if(!$username){
			return Helper::formatJson(1001, '请输入用户名');
		}

		if(!$password){
			return Helper::formatJson(1001, '请输入密码');
		}

		$user = SystemUser::findByUsername($username);
		if(!$user){
			return Helper::formatJson(1004, '用户不存在');
		}

		if($rememberMe && $token){
			if($token !== $user->access_token){
				return Helper::formatJson(1005, 'token已过期');
			}

			if($password !== substr(sha1($user->password), 0, 18)){
				return Helper::formatJson(1005, '用户名或密码错误');
			}
		}else{
			if(!Yii::$app->security->validatePassword($password, $user->password)){
				return Helper::formatJson(1005, '用户名或密码错误');
			}
		}

		$role = Yii::$app->authManager->getRolesByUser($user->id);

		if(isset($role['guest'])){
			unset($role['guest']);
		}
		if(is_array($role)){
			$auth_parent = array_keys($role)[0];
			$rolePermission = AuthItemChild::find()->where(['parent'=>$auth_parent])->asArray()->all();
			$rolePermission = array_column($rolePermission,'child');
		}else{
			return Helper::formatJson(1001);
		}


		$userInfo = ArrayHelper::toArray($user);
		$userInfo['permission'] = $rolePermission;

		$role = ArrayHelper::toArray($role);
		$userInfo['isAdministrator'] = isset($role['超级管理员']) ? 1 : 0;

		return Helper::formatJson(200, 'Ok', $userInfo);
	}

}