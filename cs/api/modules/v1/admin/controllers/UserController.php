<?php

namespace api\modules\v1\admin\controllers;

use api\models\Agency;
use Yii;
use api\controllers\BaseAPIAuthController;
use api\models\SystemUser;
use api\models\AuthAssignment;
use common\helpers\Helper;

class UserController extends BaseAPIAuthController
{
	public function actions()
	{
		return [];
	}

	/**
	 * 获取用户列表
	 * @return [type] [description]
	 */
	public function actionList()
	{
		$agencyId = Yii::$app->request->get('agencyId');
		$keyword = Yii::$app->request->get('keyword');
		$pageSize = Yii::$app->request->get('pageSize', 20);
		$order = Yii::$app->request->get('order', 'DESC');

		$where = array();
		$orWhere = array();
		$orderBy = '';

		if($agencyId){
			$where['a.agencyId'] = intval($agencyId);
		}

		if(isset($keyword)){
			$orWhere = [
			    ['like', 'a.realName', $keyword],
                ['like', 'a.userName', $keyword]
            ];
		}

		if($order){
			$orderBy = 'a.signTime ' . $order;
		}

		$userModel = new SystemUser();
		$list = $userModel->getUserList($where, $orWhere, $orderBy, $pageSize);

		return Helper::formatJson(200, 'Ok', $list);
	}

	/**
	 * 查询公司列表
	 * @return [type] [description]
	 */
	public function actionAgencyList()
	{
        $list['data'] = (new Agency())->getList();

		return Helper::formatJson(200, 'Ok', $list);
	}

	/**
	 * 删除用户
	 * @return [type] [description]
	 */
	public function actionDeleteUser()
	{
		$userId = Yii::$app->request->post('userId');
		if(!$userId){
			return Helper::formatJson(1001, '请选择用户');
		}

		if(!is_array($userId)){
			return Helper::formatJson(1005, '参数格式错误');
		}

		$user = SystemUser::findAll(['id' => $userId]);
		if($user){
			foreach($user as $key => $object){
				$object->delete();
				$role = AuthAssignment::findOne(['user_id' => $object->id]);
				$role->delete();
			}
		}

		return Helper::formatJson(200, 'Ok');
	}

	/**
	 * 添加用户
	 * @return [type] [description]
	 */
	public function actionCreate()
	{
		$userId = Yii::$app->request->post('userId');
		$username = Yii::$app->request->post('username');
		$password = Yii::$app->request->post('password');
		$realName = Yii::$app->request->post('realName');
		$agencyId = Yii::$app->request->post('agencyId');
		$agency = Yii::$app->request->post('company');
		$department = Yii::$app->request->post('department');
		$role = Yii::$app->request->post('role');
		$bigImage = Yii::$app->request->post('bigImage');
		$smallImage = Yii::$app->request->post('smallImage');

		if(!$username){
			return Helper::formatJson(1001, '请填写用户名');
		}

		if(!$userId && !$password){
			return Helper::formatJson(1001, '请填写密码');
		}

		if(!$realName){
			return Helper::formatJson(1001, '请填写真实姓名');
		}

		if(!$agencyId || !$agency){
			return Helper::formatJson(1001, '请选择公司');
		}

		if(!$department){
			return Helper::formatJson(1001, '请填写部门');
		}

		if(!$role){
			return Helper::formatJson(1001, '请选择角色');
		}

		/*if(!$bigImage || !$smallImage){
			return Helper::formatJson(1001, '请上传头像');
		}*/
		$userModel = new SystemUser();
		if($userId){
			$userModel = SystemUser::findOne(['id' => intval($userId)]);
		}

		if(!$userId){
			$userModel->access_token = Yii::$app->security->generateRandomString();
			$userModel->password = Yii::$app->getSecurity()->generatePasswordHash($password);
            $userModel->signTime = time();
		}
		
		$userModel->username = $username;
		$userModel->realName = $realName;
		$userModel->agencyId = intval($agencyId);
		$userModel->agency = $agency;
		$userModel->department = $department;
		$userModel->bigImage = $bigImage;
		$userModel->smallImage = $smallImage;

		if($userModel->save()){
			if($userId){
				$authModel = AuthAssignment::findOne(['user_id' => $userModel->id]);
				if($authModel){
					$authModel->delete();
				}
			}

			$authModel = new AuthAssignment();
			$authModel->user_id = (string)$userModel->id;
			$authModel->created_at = time();
			$authModel->item_name = $role;

			$res = $authModel->save();
			return Helper::formatJson(200, 'Ok');
		}

		return Helper::formatJson(1007, '添加失败');
	}

	/**
	 * 查询用户详情
	 * @return [type] [description]
	 */
	public function actionInfo()
	{
		$userId = Yii::$app->request->get('userId');
		if(!$userId){
			return Helper::formatJson(1001, '用户ID不能为空');
		}

		$info = SystemUser::getUserInfo($userId);
		$info['company'] = $info['agency'];
		$info['role'] = $info['item_name'];
		unset($info['agency'], $info['password'], $info['item_name']);

		return Helper::formatJson(200, 'Ok', $info);
	}

	/**
	 * 用户名验证
	 * @return [type] [description]
	 */
	public function actionVerifyUsername()
	{
		$username = Yii::$app->request->post('username');
		if(!$username){
			return Helper::formatJson(1001, '请填写用户名');
		}

		if(SystemUser::findByUsername($username)){
			return Helper::formatJson(1006, '用户已存在');
		}

		return Helper::formatJson(200, 'Ok');
	}

	/**
	 * 修改密码
	 * @return [type] [description]
	 */
	public function actionModifyPassword()
	{
		$userId = Yii::$app->request->post('userId');
		$oldpass = Yii::$app->request->post('oldpass');
		$password = Yii::$app->request->post('password');
		$repass = Yii::$app->request->post('repass');

		if(!$userId){
			return Helper::formatJson(1001, '用户ID不能为空');
		}

		if(!$oldpass){
			return Helper::formatJson(1001, '原始密码不能为空');
		}

		if(!$password){
			return Helper::formatJson(1001, '密码不能为空');
		}

		if($repass != $password){
			return Helper::formatJson(1005, '两次输入的密码不一致');
		}

		$user = SystemUser::findOne(['id' => intval($userId)]);
		if(!Yii::$app->security->validatePassword($oldpass, $user->password)){
			return Helper::formatJson(1005, '原始密码错误');
		}

		$user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
		if(!$user->save()){
			return Helper::formatJson(1007, '修改失败');
		}

		return Helper::formatJson(200, 'Ok');
	}

	/**
	 * 重置密码
	 * @return [type] [description]
	 */
	public function actionResetPassword()
	{
		$userId = Yii::$app->request->post('userId');
		if(!$userId){
			return Helper::formatJson(1001, '用户ID不能为空');
		}

		$user = SystemUser::findOne(['id' => intval($userId)]);
		$user->password = Yii::$app->getSecurity()->generatePasswordHash('123456');

		if(!$user->save()){
			return Helper::formatJson(1007, '修改失败');
		}

		return Helper::formatJson(200, 'Ok');
	}

	/**
	 * 上传用户头像
	 * @return [type] [description]
	 */
	public function actionUploadImage()
	{
		$rs = array();
		$imagName = time();
		$input = file_get_contents('php://input');
        $data = explode('--------------------', $input);

        $bigImage = 'data/avatar/' . $imagName . '_big.jpg';
        $smallImage ='data/avatar/' . $imagName . '_small.jpg';
        @file_put_contents($bigImage, $data[0]);
        @file_put_contents($smallImage, $data[1]);
        //@file_put_contents($smlPic, $data[2]);
        if(count($data)>2)
            @file_put_contents('data/avatar/' . $imagName . '.jpg', $data[2]);
        
        $rs['status'] = 1 . ',' . $bigImage . ',' . $smallImage;
        echo json_encode($rs);exit;
	}

}