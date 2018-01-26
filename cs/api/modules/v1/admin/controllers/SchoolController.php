<?php

namespace api\modules\v1\admin\controllers;

use api\models\CustomerClassify;
use api\models\SchoolClassify;
use api\models\SchoolLevel;
use Yii;
use api\controllers\BaseAPIAuthController;
use common\helpers\Helper;
use api\models\Agency;
use api\models\AgencyIp;

class SchoolController extends BaseAPIAuthController
{

    public function actions()
    {
        return [];
    }

	/**
	 * @param $name         学校名
	 * @param $depart       部门
	 * @param $city         城市
	 * @param $area         省份
	 * @param $fromUserName 销售
	 * @param $startTime    开始时间
	 * @param $endTime      结束时间
	 * @param $startIp      开始IP段
	 * @param $endIp        结束IP段
	 * @return array
	 */
    public function actionCreate()
    {
        $name = Yii::$app->request->post('name');
        if(!$name){
            return Helper::formatJson(1001, '请填写学校名称');
        }

        $depart = Yii::$app->request->post('depart');
        if(!$depart){
            return Helper::formatJson(1001, '请填写部门');
        }

        $area = Yii::$app->request->post('area');
        if(!$area){
            return Helper::formatJson(1001, '请选择地区');
        }

        $city = Yii::$app->request->post('city');
        if(!$city){
            return Helper::formatJson(1001, '请选择城市');
        }

        $fromUserName = Yii::$app->request->post('fromUserName');
        if(!$fromUserName){
            return Helper::formatJson(1001, '请填写销售');
        }

        $startTime = Yii::$app->request->post('startTime');
        if(!$startTime){
            return Helper::formatJson(1001, '请选择开始时间');
        }

        $endTime = Yii::$app->request->post('endTime');
        if(!$endTime){
            return Helper::formatJson(1001, '请选择结束时间');
        }

        $customerTypeId = Yii::$app->request->post('customerType');
        if(!$customerTypeId){
	        return Helper::formatJson(1001, '请选择客户分类');
        }

	    $schoolTypeId = Yii::$app->request->post('schoolType');
	    if(!$schoolTypeId){
		    return Helper::formatJson(1001, '请选择学校分类');
	    }

	    $schoolLevelId = Yii::$app->request->post('schoolLevel');
	    if(!$schoolLevelId){
		    return Helper::formatJson(1001, '请选择学校级别');
	    }

        $ips = Yii::$app->request->post('ips');
        if(!$ips){
            return Helper::formatJson(1001, '请填写IP段');
        }

        $startIp = array();
        $endIp = array();
        foreach($ips as $item){
            $startIp[] = $item['startIp'];
            $endIp[] = $item['endIp'];
        }
        /*$startIp = Yii::$app->request->post('startIp');
        if(!$startIp){
            return Helper::formatJson(1001, '请填写开始IP段');
        }

        $endIp = Yii::$app->request->post('endIp');
        if(!$endIp){
            return Helper::formatJson(1001, '请填写结束IP段');
        }*/

	    $customer = explode('-', $customerTypeId);
	    $school = explode('-', $schoolTypeId);

	    $levelNameStr = '';
	    $levelIds = '';
	    foreach($schoolLevelId as $item){
			$arr = explode('-', $item);
			$levelIds .= $arr[0];
		    $levelNameStr .= $arr[1];
	    }

        $customerType = $customer[1];
        $schoolType = $school[1];
        $schoolLevel = $levelNameStr;

        //先同步数据到CRM平台
		$url = API_DOMAIN . API_URL['create'];
		$result = json_decode(Helper::curl_exec($url, 'POST', false, array(
			'agencyName' => trim($name),
			'areaName' => $area,
			'cityName' => $city,
			'saleMan' => trim($fromUserName),
			'customerType' => $customerType,
			'schoolType' => $schoolType,
			'schoolLevel' => $schoolLevel
		)));

		if(!$result->Success){
			return Helper::formatJson(1007, urldecode($result->Description));
		}

	    $uniCode = $result->Description;
	    $userNum = Yii::$app->request->post('userNum', 0);

	    $model = new Agency();
        $model->name = trim($name);
        $model->depart = trim($depart);
        $model->area = $area;
        $model->city = $city;
        $model->fromUserName = trim($fromUserName);
        $model->startTime = $startTime/1000;
        $model->endTime = $endTime/1000;
        $model->userNum = $userNum;
        $model->customerClassifyId = $customer[0];
        $model->schoolClassifyId = $school[0];
	    $model->schoolLevelId = $levelIds;
	    $model->uniCode = $uniCode;

        if($model->save()){
            $agencyId = $model->id;
            $model->otherAgencyId = $agencyId;
            $model->save();

            $agencyData = array();
            foreach ($startIp as $key => $value){
                $agencyData[$key]['startingIP'] = trim($value);
                $agencyData[$key]['endingIp'] = trim($endIp[$key]);
                $agencyData[$key]['agencyId'] = $agencyId;
                $agencyData[$key]['startingIPInt'] = bindec(decbin(ip2long($agencyData[$key]['startingIP'])));
                $agencyData[$key]['endingIpInt'] = bindec(decbin(ip2long($agencyData[$key]['endingIp'])));
            }

            Yii::$app->db2->createCommand()->batchInsert(
                AgencyIp::tableName(),
                ['startingIP', 'endingIp', 'agencyId', 'startingIPInt', 'endingIpInt'],
                $agencyData
            )->execute();

            return Helper::formatJson(200, 'Ok');
        }

        return Helper::formatJson(1007, '添加失败');
    }

    /**
     * 删除院校
     */
    public function actionRemove()
    {
        $schoolId = Yii::$app->request->post('schoolId');
        if(!$schoolId){
            return Helper::formatJson(1001, '请选择要删除的学校');
        }

	    $info = Agency::findOne(['id' => $schoolId]);
        if(!$info){
	        return Helper::formatJson(1004, '学校不存在');
        }

	    //同步删除CRM平台数据
	    $url = API_DOMAIN . API_URL['delete'];
	    $result = json_decode(Helper::curl_exec($url, 'POST', false, array('uniCode' => $info->uniCode)));

	    if(!$result->Success){
		    return Helper::formatJson(1007, urldecode($result->Description));
	    }

        Agency::deleteAll(['id' => $schoolId]);
        AgencyIp::deleteAll(['agencyId' => $schoolId]);

        return Helper::formatJson(200, 'Ok');
    }

	/**
	 * 查询学校等级列表
	 */
    public function actionSchoolLevel()
    {
		$data = (new SchoolLevel())->getList();
		return Helper::formatJson(200, $data);
    }

	/**
	 * 客户分类
	 */
    public function actionCustomer()
    {
    	$data = (new CustomerClassify())->getList();
    	return Helper::formatJson(200, $data);
    }

	/**
	 * 学校分类
	 */
	public function actionType()
	{
		$data = (new SchoolClassify())->getList();
		return Helper::formatJson(200, $data);
	}

    /**
     * 获取学校详情
     */
    public function actionInfo()
    {
        $schoolId = Yii::$app->request->get('schoolId');
        if(!$schoolId){
            return Helper::formatJson(1001, '请选择学校');
        }

        $model = new Agency();
        $data = $model->getSchoolInfo(intval($schoolId));

        return Helper::formatJson(200, 'Ok', $data);
    }

    /**
     * 修改学校信息
     */
    public function actionModify()
    {
        $schoolId = Yii::$app->request->post('schoolId');
        if(!$schoolId){
            return Helper::formatJson(1001, '请选择学校');
        }

        $name = Yii::$app->request->post('name');
        if(!$name){
            return Helper::formatJson(1001, '请填写学校名称');
        }

        $depart = Yii::$app->request->post('depart');
        if(!$depart){
            return Helper::formatJson(1001, '请填写部门');
        }

        $area = Yii::$app->request->post('area');
        if(!$area){
            return Helper::formatJson(1001, '请选择地区');
        }

        $city = Yii::$app->request->post('city');
        if(!$city){
            return Helper::formatJson(1001, '请选择城市');
        }

        $fromUserName = Yii::$app->request->post('fromUserName');
        if(!$fromUserName){
            return Helper::formatJson(1001, '请填写销售');
        }

        $startTime = Yii::$app->request->post('startTime');
        if(!$startTime){
            return Helper::formatJson(1001, '请选择开始时间');
        }

        $endTime = Yii::$app->request->post('endTime');
        if(!$endTime){
            return Helper::formatJson(1001, '请选择结束时间');
        }

        $ips = Yii::$app->request->post('ips');
        if(!$ips){
            return Helper::formatJson(1001, '请填写IP段');
        }

	    $customerTypeId = Yii::$app->request->post('customerType');
	    if(!$customerTypeId){
		    return Helper::formatJson(1001, '请选择客户分类');
	    }

	    $schoolTypeId = Yii::$app->request->post('schoolType');
	    if(!$schoolTypeId){
		    return Helper::formatJson(1001, '请选择学校分类');
	    }

	    $schoolLevelId = Yii::$app->request->post('schoolLevel');
	    if(!$schoolLevelId){
		    return Helper::formatJson(1001, '请选择学校级别');
	    }

        $startIp = array();
        $endIp = array();
        foreach($ips as $item){
            $startIp[] = $item['startIp'];
            $endIp[] = $item['endIp'];
        }

	    $customer = explode('-', $customerTypeId);
	    $school = explode('-', $schoolTypeId);

	    $levelNameStr = '';
	    $levelIds = '';
	    foreach($schoolLevelId as $item){
		    $arr = explode('-', $item);
		    $levelIds .= $arr[0] . ',';
		    $levelNameStr .= $arr[1] . ',';
	    }

	    $customerType = $customer[1];
	    $schoolType = $school[1];
	    $schoolLevel = rtrim($levelNameStr, ',');
        $userNum = Yii::$app->request->post('userNum', 0);

        $model = Agency::findOne(['id' => intval($schoolId)]);
        $model->name = trim($name);
        $model->depart = trim($depart);
        $model->area = $area;
        $model->city = $city;
        $model->fromUserName = trim($fromUserName);
        $model->startTime = $startTime/1000;
        $model->endTime = $endTime/1000;
        $model->userNum = $userNum;
	    $model->customerClassifyId = $customer[0];
	    $model->schoolClassifyId = $school[0];
	    $model->schoolLevelId = rtrim($levelIds, ',');

	    //同步更新数据到CRM平台
	    $url = API_DOMAIN . API_URL['update'];
	    $result = json_decode(Helper::curl_exec($url, 'POST', false, array(
		    'agencyName' => trim($name),
		    'areaName' => $area,
		    'cityName' => $city,
		    'saleMan' => trim($fromUserName),
		    'customerType' => $customerType,
		    'schoolType' => $schoolType,
		    'schoolLevel' => $schoolLevel,
		    'uniCode' => $model->uniCode
	    )));

	    if(!$result->Success){
		    return Helper::formatJson(1007, urldecode($result->Description));
	    }

        if($model->save()){
            $agencyId = $model->id;
            AgencyIp::deleteAll(['agencyId' => intval($agencyId)]);

            $agencyData = array();
            foreach ($startIp as $key => $value){
                $agencyData[$key]['startingIP'] = trim($value);
                $agencyData[$key]['endingIp'] = trim($endIp[$key]);
                $agencyData[$key]['agencyId'] = $agencyId;
                $agencyData[$key]['startingIPInt'] = bindec(decbin(ip2long($agencyData[$key]['startingIP'])));
                $agencyData[$key]['endingIpInt'] = bindec(decbin(ip2long($agencyData[$key]['endingIp'])));
            }

            Yii::$app->db2->createCommand()->batchInsert(
                AgencyIp::tableName(),
                ['startingIP', 'endingIp', 'agencyId', 'startingIPInt', 'endingIpInt'],
                $agencyData
            )->execute();

            return Helper::formatJson(200, 'Ok');
        }

        return Helper::formatJson(1007, '修改失败');
    }

}