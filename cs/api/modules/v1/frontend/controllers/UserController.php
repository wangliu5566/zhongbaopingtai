<?php

namespace api\modules\v1\frontend\controllers;

use Yii;
use api\controllers\BaseAPIAuthController;
use common\helpers\Helper;
use api\models\User;

class UserController extends BaseAPIAuthController
{

	public $modelClass = 'api\models\User';

	public function actions()
	{
		$actions = parent::action();
		unset($actions['index'], $actions['create'], $actions['delete'], $actions['options']);
	}

	/**
	 * 修改密码
	 * @return [type] [description]
	 */
	public function actionModifyPassword()
	{
		$oldPass = Yii::$app->request->post('oldPassword');
		$newPass = Yii::$app->request->post('newPassword');
		$rePass = Yii::$app->request->post('rePassword');

		/*验证密码*/
		if($newPass !== $rePass){
			return Helper::formatJson(1005, '两次输入的密码不一致');
		}

		$userModel = User::findIdentity($this->userId);
		if(!Yii::$app->getSecurity()->validatePassword($oldPass, $userModel->password)){
			return Helper::formatJson(1005, '当前密码错误');
		}

		$userModel->password = Yii::$app->getSecurity()->generatePasswordHash($newPass);
		if($userModel->save()){
			return Helper::formatJson(200, '修改成功');
		}else{
			return Helper::formatJson(1007, '修改失败');
		}
	}

}