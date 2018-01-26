<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/7/19
 * Time: 14:35
 */

namespace api\modules\v1\admin\controllers;

use api\models\ActivityOrganization;
use api\models\AuthItemChild;
use api\models\Reason;
use api\models\SystemUser;
use Yii;
use api\controllers\BaseAPIAuthController;
use api\models\Activity;
use api\models\ActivityDistribution;
use api\models\ActivityScore;
use api\models\ActivityTime;
use api\models\IntroImage;
use api\models\Prize;
use common\helpers\Helper;
use api\models\Agency;

class ActivityController extends BaseAPIAuthController
{
    /**添加活动
     * @return array
     */
    public function actionAdd()
    {
        /*判断提交方式*/
        if (\Yii::$app->request->isPost) {
            //开启事务
            if(\Yii::$app->request->post('type') == 1){

                $transaction= \Yii::$app->db->beginTransaction();//创建事务
                $activity = new Activity();
                $id = \Yii::$app->request->post('activityId') ?? '';
                if($id){
                    $activity->id = intval($id);
                    $activity->setScenario('update');
                    $activity->setOldAttribute('id',intval($id));
                }else{
                    $activity->applicationDate = time();
                    $activity->setScenario('addActivity');
                }

                /*绑定活动的数据*/
                if ($activity->load(\Yii::$app->request->post(),'') && $activity->validate()) {

                    /*保存活动数据，拿到返回id*/
                    if($activity->save()){
                        $activityId = $activity->id;
                        /*绑定活动时间表数据*/
                        $activityTimeData = \Yii::$app->request->post('activityTime');
                        ActivityTime::deleteAll(['activityId'=>$activityId]);
                        foreach ($activityTimeData as $key=>$ac){
                            $activityTime = new ActivityTime();
                            $ac['sn'] = $key;
                            $ac['activityId'] = $activityId;
                            if($activityTime->load($ac,'') && $activityTime->validate()){
                                $activityTime->save();
                            }else{
                                $transaction->rollBack();
                                return Helper::formatJson(1002,'数据有误',$activityTime->getErrors());
                            }
                        }
                        /*绑定简介配图*/

                        $introImageData = \Yii::$app->request->post('introImage');
                        IntroImage::deleteAll(['activityId'=>$activityId]);
                        foreach ($introImageData as $intro ){
                            $introImage = new IntroImage();
                            $introImage->activityId = $activityId;
                            if($introImage->load($intro,'') && $introImage->validate()){
                                $introImage->save();
                            }else{
                                $transaction->rollBack();
                                return Helper::formatJson(1002,'数据有误',$introImage->getErrors());
                            }
                        }

                        /*绑定奖项*/
                        $prizeData = \Yii::$app->request->post('prize');
                        Prize::deleteAll(['activityId'=>$activityId]);
                        foreach ($prizeData as $pr ){
                            $prize = new Prize();
                            $prize->activityId = $activityId;
                            $prize->load($pr,'');
                            if($prize->load($pr,'') && $prize->validate()){
                                $prize->save();
                            }else{
                                $transaction->rollBack();
                                return Helper::formatJson(1002,'数据有误',$prize->getErrors());
                            }
                        }

                        /*绑定學校*/
                        $organization = \Yii::$app->request->post('organization');
                        ActivityOrganization::deleteAll(['activityId'=>$activityId]);
                        if($organization) {
                            foreach ($organization as $or) {
                                $organ = new ActivityOrganization();
                                $organ->activityId = $activityId;
                                $organ->load($or, '');
                                if ($organ->load($or, '') && $organ->validate()) {
                                    $organ->save();
                                } else {
                                    $transaction->rollBack();
                                    return Helper::formatJson(1002, '数据有误', $organ->getErrors());
                                }
                            }
                        }

                        $transaction->commit();
                        /*保存成功*/
                        return Helper::formatJson(200,'保存成功','');

                    }else{
                        $transaction->rollBack();
                        return Helper::formatJson(1002,'数据有误',$activity->getErrors());
                    }

                }else{
                    return Helper::formatJson(1002,'数据有误', $activity->getErrors());
                }
            }else{
                /*保存大赛*/
                $saveRes = $this->actionAddAnswer(\Yii::$app->request->post());
                if($saveRes){
                    return Helper::formatJson(200,'保存成功', '');
                }else{
                    return Helper::formatJson(1002,'数据有误', '');
                }

            }

        } else {
            return Helper::formatJson(1003);
        }
    }

    private function actionAddAnswer($post){
        $activity = new Activity();
        $id = \Yii::$app->request->post('activityId') ?? '';
        if($id){
            $activity->id = intval($id);
            $activity->setScenario('updateAnswer');
            $activity->setOldAttribute('id',intval($id));
        }else{
            $activity->applicationDate = time();
            $activity->setScenario('addAnswer');
        }

        if ($activity->load(\Yii::$app->request->post(),'') && $activity->validate()){
            $transaction = \Yii::$app->db->beginTransaction();
            $activity->save();

            ActivityScore::deleteAll(['activityId'=>$id]);

            //绑定學校
            $organization = \Yii::$app->request->post('organization');
            ActivityOrganization::deleteAll(['activityId'=>$activity->id]);
	        if($organization) {
		        foreach ( $organization as $or ) {
			        $organ             = new ActivityOrganization();
			        $organ->activityId = $activity->id;
			        $organ->load( $or, '' );
			        if ( $organ->load( $or, '' ) && $organ->validate() ) {
				        $organ->save();
			        } else {
				        $transaction->rollBack();

				        return Helper::formatJson( 1002, '数据有误', $organ->getErrors() );
			        }
		        }
	        }


            //保存积分表
            $scoreData = \Yii::$app->request->post('score');
            $score = new ActivityScore();
            $score->activityId = $activity->id;
            $score->float = ceil($scoreData['integration']/SCORE_FLOAT);
            $score->unsend = $scoreData['number'];
            $score->leftScore = $scoreData['integration'];
            $score->average = ceil($scoreData['integration']/$scoreData['number']);
            if($score->load($scoreData,'') && $score->validate()){
                $score->save();
                $transaction->commit();
                /*保存成功*/
                return Helper::formatJson(200,'保存成功','');
            }else{
                $transaction->rollBack();
                return Helper::formatJson(1002,'数据有误',$score->getErrors());
            }

        }else{
            return Helper::formatJson(1002,'数据有误', $activity->getErrors());
        }
    }

    public function actionGetReason(){
        $activityId = Yii::$app->request->post('activityId') ?? '';
        if(empty($activityId)){
            return Helper::formatJson(1002,'缺少参数');
        }
        $reason = Reason::find()->where(['activityId'=>$activityId])->one();
        if(isset($reason['reason'])){
            return Helper::formatJson(200,'',$reason);
        }else{
            return Helper::formatJson(200,'','');
        }
    }


    /**
     * 审核活动
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionReviewActivity(){
        $activityId = Yii::$app->request->post('activityId') ?? 0;
        $allow = Yii::$app->request->post('allow') ?? 0;
        $activityId = intval($activityId);
        $allow = intval($allow);
        $activity = Activity::find()->where(['id' => $activityId])->one();
        if(!$activity) {
            return Helper::formatJson(1004,'活动不存在,请确认');
        }
        $transaction = Yii::$app->db->beginTransaction();
        if(!empty($activityId) && !empty($allow)){
            $status = $activity->status;
            //审核通过并所设置的开始时间小于当前时间
            if($allow == ACTIVITY_AUDITSTATE['pass'] && $activity->activityStartTime <= time()) {
                $status = ACTIVITY_STATUS['started'];
            }
            $res = Activity::updateAll(['auditState' => $allow,'status' => $status],['id' => $activityId]);
            if($res>0){
                if($allow == ACTIVITY_AUDITSTATE['fail']){
                    //审核未通过,将未通过原因保存到数据库
                    $userId = Yii::$app->request->post('userId') ?? '';
                    $reason = Yii::$app->request->post('reason') ?? '';
                    if(!empty($userId) && !empty($reason)){
                        //通过userId查询到真是姓名
                        $userData = SystemUser::find()->where(['id'=>$userId])->one();
                        if(isset($userData['realName'])){
                            $userName = $userData['realName'];
                           $reasonModel = new Reason();
                            $reasonModel->deleteAll(['activityId'=>$activityId]);
                            $reasonModel->activityId = $activityId;
                            $reasonModel->userId = $userId;
                            $reasonModel->username = $userName;
                            $reasonModel->reason = $reason;
                            $reasonModel->reviewDate = time();
                            $reasonRes = $reasonModel->save();
                            if($reasonRes){
                                $transaction->commit();
                                return Helper::formatJson(200);
                            }else{
                                $transaction->rollBack();
                                return Helper::formatJson(1002,'管理员失效');
                            }
                        }else{
                            $transaction->rollBack();
                            return Helper::formatJson(1002,'管理员失效');
                        }
                    }else{
                        $transaction->rollBack();
                        return Helper::formatJson(1002,'更新失败');
                    }
                }
                $transaction->commit();
                return Helper::formatJson(200);
            }else{
                $transaction->rollBack();
                return Helper::formatJson(1002,'更新失败');
            }
        }else{
            return Helper::formatJson(1002,'数据有误', '');
        }
    }

    /**删除活动
     * @return array
     */
    public function actionActivityDelete(){
        if(\Yii::$app->request->isPost){

            $activityId = \Yii::$app->request->post('activityId');
            /*删除活动表数据*/

            $transaction = Yii::$app->db->beginTransaction();
            $activity = new Activity();
            $activity->findOne(['id'=>$activityId])->delete();

            /*删除活动奖项表数据*/
            $prize = new Prize();
            $prize->deleteAll(['id'=>$activityId]);

            /*删除活动时间表*/
            $activityTime = new ActivityTime();
            $activityTime->deleteAll(['id'=>$activityId]);

            /*删除简介介绍图片*/
            $introImage = new IntroImage();
            $introImage->deleteAll(['id'=>$activityId]);

            $transaction->commit();
            /*返回结果*/
            return Helper::formatJson(200,'删除成功');
        }else{
            return Helper::formatJson(1003);
        }
    }

    /**活动总览
     * @return array
     */
    public function actionActivityIndex()
    {

        $userId = \Yii::$app->request->post('userId') ?? '';
        $auth  =  Yii::$app->authManager;

        /*当前页码*/
        $currentPage = \Yii::$app->request->post('page') ?? 1;
        $condition = \Yii::$app->request->post('condition') ?? [];

        if(is_array($condition)){
            foreach($condition as $key=>$co){

                if(empty($co)){
                    unset($condition[$key]);
                }
            }
        }

        //通过useriD拿到角色
        $role = $auth->getRolesByUser($userId);
        if(isset($role['guest'])){
            unset($role['guest']);
        }
        if(is_array($role)){
            $role = array_keys($role)[0];
            if($role != '超级管理员') $condition['isDeleted'] = ACTIVITY_IS_DELETED['no'];
            $rolePermission = AuthItemChild::find()->where(['parent'=>$role])->asArray()->all();
            $rolePermission = array_column($rolePermission,'child','child');
        }else{
            return Helper::formatJson(1001);
        }


        if(!isset($rolePermission['activity/viewAllActivity'])){
            $condition['userId'] = $userId;
            $audit = '1=1';
        }else{
            $audit = ['!=','auditState',4];
        }
        /*每页显示数量*/
        $pageSize = \Yii::$app->request->post('pageSize') ?? '10';
        /*case when activityEndTime>'.time().' then 未开始 else 已结束*/
        $activityQuery = Activity::find()
            ->select(['id','userId','userName','companyName','name','status','type',
                "FROM_UNIXTIME(applicationDate,'%Y-%m-%d') as applicationDate",
                'applicationDate as or',
                "FROM_UNIXTIME(activityEndTime,'000') as activityEndTime",
                'auditState','isDeleted']);

        /*总条数*/

        $count = $activityQuery->where($condition)->andWhere($audit)->count();
        /*计算偏移量*/
        $offset = ($currentPage-1) * $pageSize;
        /*分页数据*/
        $data = $activityQuery
            ->offset($offset)
            ->limit($pageSize)
            ->orderBy('or DESC')
            ->asArray()
            ->all();

        $total['total'] = $count;
        $total['rows'] = $data;
        return Helper::formatJson(200,'',$total);

    }



    /**
     * 获取活动详情后台
     * @return array
     */
    public function actionGetInfoAdmin()
    {
        $request = \Yii::$app->request;
        if($request->isGet) {
            $activityId = intval($request->get('activityId')) ?? 0;
            $type = intval($request->get('type')) ?? 0;
            if(!$activityId || !$type) {
                return Helper::formatJson(1001,'缺少参数');
            }
            /*活动信息*/
            $activity = Activity::find()
                ->select(['id','name','intro','reviewStandard','pcImage','mobileImage','smallImage','bigImage'
                    ,'type','auditState',
                    "CONCAT(activityStartTime,'000') as activityStartTime",
                    "CONCAT(activityEndTime-86399,'000') as activityEndTime",
                    'status','companyName','questionAnswerDetail', 'allowJoin'])
                ->where(['id' => $activityId]);
            $activityData = clone $activity;
            $activity = $activity->one();

           $activityData = $activityData->asArray()->one();
            $data = [];

            if($type == ACTIVITY_TYPE['active']) {
                /*简介图片*/

                $introImages = $activity->getImages()->all();

                /*活动奖项*/
                $prizes = $activity->getPrizes()->all();
                /*活动时间流程*/
                $activityTime = $activity->getActivityTime()->select([
                    'activityId',
                    'id',
                    'type',
                    "CONCAT(signStartTime,'000') as signStartTime"
                    ,"CONCAT(signEndTime,'000') as signEndTime",
                    "CONCAT(reviewStartTime,'000') as reviewStartTime",
                    "CONCAT(announceAwardsTime,'000') as announceAwardsTime",
                    "sn",
                ])->asArray()->all();
                $organization = $activity->getActivityOrganization()->all();
                $data = [
                    'introImages' => $introImages,
                    'prizes' => $prizes,
                    'activityTime' => $activityTime,
                    'organization' => $organization,
                ];
            }elseif($type == ACTIVITY_TYPE['question']){
                $score = $activity->getActivityScore()->one();
                if(empty($score)){
                    $score['integration'] = '';
                    $score['number'] = '';
                }
                $organization = $activity->getActivityOrganization()->all();
                $data['score'] = $score;
                $data['organization'] = $organization;
            }
            $data['activity'] = $activityData;
            return Helper::formatJson(200,'OK',$data);
        }
        return Helper::formatJson('1003','请求方式错误');
    }

    /**
     * @return array
     */
    public function actionGetAllActivity(){
        $request = Yii::$app->request;
        $userId = $request->get('userId') ?? '';

        if(empty($userId)){
            return Helper::formatJson('1001');
        }
        $auth = Yii::$app->authManager;
        $role = $auth->getRolesByUser($userId);

        $userAgency = SystemUser::find()->where(['id'=>$userId])->select('agency')->one();
        if(!isset($userAgency->agency)){
            return Helper::formatJson('1001','用户信息缺少,请联系管理员');
        }
        if(!$request->isGet) {
            return Helper::formatJson('1003','请求方式错误');
        }
        $activity = new Activity();
        $status = $request->get('status') ?? '2,3';
        $status = explode(',',$status);
        $auditState = Yii::$app->request->get('auditState') ?? 1;
        $activityModel = $activity
            ->find()
            ->select(['id','name','status'])
            ->where(['auditState'=>$auditState])
            ->andWhere(['in','status' , $status]);
          if(isset($role[SUPERADMIN])){
              $activityData = $activityModel->all();
          }else{
              $activityData = $activityModel
                  ->andWhere(['companyName'=>$userAgency->agency])
                  ->all();
          }


        return Helper::formatJson('200','OK',$activityData);
    }

    /**
     * 参赛学校
     * @return [type] [description]
     */
    public function actionSchoolList()
    {
        $area = Yii::$app->request->post('area');
        $city = Yii::$app->request->post('city');
        $keyword = Yii::$app->request->post('keyword');
        $pageSize = Yii::$app->request->post('pageSize', 20);
        $page = Yii::$app->request->post('page');

        $where = array();
        $andWhere = array();

        if($area){
            $andWhere[] = array('like', 'area', $area);
        }

        if($city){
            $where['city'] = $city;
        }

        if(isset($keyword)){
            $andWhere[] = array('like', 'name', $keyword);
        }

        $list['data'] = (new Agency())->getListByCondition($where, $andWhere, $page, $pageSize);

        return Helper::formatJson(200, 'Ok', $list);
    }


    /**修改活动激活状态
     * @return array
     */
    public function actionUpdateState()
    {
        $request = Yii::$app->request;
        if(!$request->isPost) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $activityId = intval($request->post('activityId'));
        $isDeleted = $request->post('state');
        $activity = Activity::findOne($activityId);
        if(!$activity) return Helper::formatJson(1004,'活动不存在,请确认');
        if($activity->auditState == ACTIVITY_AUDITSTATE['pass']) Helper::formatJson(1005,'活动已发布,不能执行此操作');
        $activity->isDeleted = $isDeleted;
        $activity->setScenario('update');
        $activity->setOldAttribute('id',$activityId);
        if($activity->save()) {
            return Helper::formatJson(200,'OK');
        }else {
            return Helper::formatJson(1007,'修改失败');
        }
    }

}