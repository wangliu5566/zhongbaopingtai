<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/7/19
 * Time: 14:35
 */

namespace api\modules\v1\frontend\controllers;


use api\controllers\BaseAPIAuthController;
use api\models\Activity;
use api\models\ActivityOrganization;
use api\models\ActivityTime;
use api\models\Clicks;
use api\models\IntroImage;
use api\models\Prize;
use api\models\ActivityScore;
use api\models\Signup;
use api\models\User;
use api\models\UserScore;
use common\helpers\Helper;
use common\helpers\Office2pdf;
use yii\data\Pagination;

class ActivityController extends BaseAPIAuthController
{
    /**获取活动详情
     * @return array
     */
    public function actionGetInfo()
    {
        $request = \Yii::$app->request;
        if($request->isGet) {
            $activityId = intval($request->get('activityId'));
            $type = intval($request->get('type'));
            $userId = intval($request->get('userId')) ?? 0;
            if(!$activityId || !$type) {
                return Helper::formatJson(1001,'缺少参数');
            }
            /*活动信息*/
            $activity = Activity::find()
                ->where(['id' => $activityId])
                ->one();
            if(!$activity) {
                return Helper::formatJson(1004,'活动不存在,请确认!');
            }
            //记录活动点击数据
            if(!Clicks::saveInfo($activityId,CLICK_TYPE['act_click'])) {
                return Helper::formatJson(1007,'记录点击数据失败');
            }
            $data = [];
            $signStatus = SIGN_STATUS['not_sign'];
            /*如果有用户ID查询该用户是否报名*/
            if($userId) {
                $isSignUp = Signup::find()
                        ->where(['userId' => $userId,'activityId' => $activityId])
                        ->one();
                if($isSignUp) {
                    $signStatus = $isSignUp->status;
                    if($type == ACTIVITY_TYPE['question']) {
                        $isSend = UserScore::find()->where(['userId' => $userId,'activityId' => $activityId])->one();
                        if($isSend) {
                            $signStatus = SIGN_STATUS['isSend'];
                        }
                    }
                }
            }
            if($type == ACTIVITY_TYPE['active']) {
                /*简介图片*/
                $introImages = $activity->getImages()->all();
                /*活动奖项*/
                $prizes = $activity->getPrizes()->all();
                /*活动时间流程*/
                $activityTime = $activity->getActivityTime()->all();
                $data = [
                    'introImages' => $introImages,
                    'prizes' => $prizes,
                    'activityTime' => $activityTime,
                ];
            }
            $data['signStatus'] = $signStatus;
            $data['activity'] = $activity;
            return Helper::formatJson(200,'OK',$data);
        }
        return Helper::formatJson(1003,'请求方式错误');
    }

    /**活动倒计时时间
     * @return array
     */
    public function actionEndTime()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $activityId = intval($request->get('activityId'));
        if(!$activityId) {
            return Helper::formatJson(1001,'缺少参数');
        }
        $signTime = Activity::getSignTime($activityId);
        if($signTime < 0) {
            return Helper::formatJson(1004,'活动不存在或已经结束');
        }
        return Helper::formatJson(200,'OK',$signTime);
    }

    public function actionGetAllActivity(){
        $activity = new Activity();
        $activityData = $activity->find()->select(['id','name'])->all();
        $activityData = empty($activityData)?'':$activityData;
        return Helper::formatJson(200,'OK',$activityData);
    }

    /**用户报名
     * @return array
     */
    public function actionUserSignUp()
    {
        $request = \Yii::$app->request;
        if($request->isGet) {
            $userId = intval($request->get('userId'));
            $activityId = intval($request->get('activityId'));
            if(!$userId || !$activityId) {
                return Helper::formatJson(1001,'缺少参数');
            }
            //判断用户是否能参赛
            if(!ActivityOrganization::juadeMatch($activityId,$userId)) {
                return Helper::formatJson(1005,'该活动不属于用户所属学校');
            }
            $activity = Activity::findOne($activityId);
            if(!$activity) {
                return Helper::formatJson(1004,'活动不存在');
            }
            $signTime = Activity::find()->alias('a')
                ->select(['MAX(t.signEndTime) as endTime'])
                ->leftJoin('cs_activity_time as t','t.activityId = a.id')
                ->where(['a.id' => $activityId])
                ->asArray()
                ->one();
            if($signTime['endTime'] && $signTime['endTime'] < time()) {
                return Helper::formatJson(1006,'报名时间已结束');
            }
            if($activity->activityEndTime < time() || $activity->status == ACTIVITY_STATUS['end']) {
                return Helper::formatJson(1006,'活动已结束');
            }
            $isSignUp = Signup::find()
                    ->where(['userId' => $userId,'activityId' => $activityId])
                    ->one();
            if($isSignUp) {
                return Helper::formatJson(1006,'用户已经报名');
            }else {
                $signUp = new Signup();
                $signUp->userId = $userId;
                $signUp->activityId = $activityId;
                $signUp->signUpTime = time();
                if($signUp->save()) {
                    return Helper::formatJson(200,'报名成功');
                }
            }
        }
            return Helper::formatJson(1003,'请求方式错误');
    }


    /**积分抽奖
     * @return array
     */
    public function actionLuckyDraw()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $userId = intval($request->get('userId'));
        if(!$userId) {
            return Helper::formatJson(1008,'用户未登录');
        }
        $activityId = intval($request->get('activityId'));
        if(!$activityId || !$userId) {
            return Helper::formatJson(1001,'缺少参数');
        }
        //查询活动是否存在
        $activity = Activity::findOne($activityId);
        if(!$activity) {
            return Helper::formatJson(1004,'活动不存在');
        }
        //判断用户是否能参赛
        if(!ActivityOrganization::juadeMatch($activityId,$userId)) {
            return Helper::formatJson(1005,'该活动不属于用户所属学校');
        }
        //查询活动是否过期
        if($activity->activityEndTime < time() || $activity->status == ACTIVITY_STATUS['end']) {
            return Helper::formatJson(1006,'活动已结束');
        }
        //查询用户是否存在
        $user = User::find()->select(['username','access_token'])->where(['id' => $userId])->one();
        if(!$user) {
            return Helper::formatJson(1004,'用户不存在');
        }
        $isSign = Signup::find()->where(['userId' => $userId,'activityId' => $activityId])->one();
        if(!$isSign) {
            return Helper::formatJson(1004,'还未报名');
        }
        /*elseif ($isSign->status == SIGN_STATUS['init']) {
            return Helper::formatJson('1004','还未参加答题');
        }*/
        $isSend = UserScore::find()->where(['userId' => $userId,'activityId' => $activityId])->one();
        if($isSend) {
            return Helper::formatJson(1006,'您已经抽过奖啦');
        }
        $point = ActivityScore::find()->where(['activityId' => $activityId])->one();
        if(!$point) {
            return Helper::formatJson(1004,'未设置积分发放,请联系管理员');
        }
        //积分已发放完成
        if($point->unsend == 0) {
            return Helper::formatJson(201,'积分已全部发放,下次请早');
        }
        //开启事务
        $transaction = \Yii::$app->db->beginTransaction();
        if($point->unsend == 1) {
            $score = $point->leftScore;
        }else {
            $float = intval($point->float);
            $average = intval($point->average);
            //生成随机分
            $score = rand($average - $float ,$average + $float);
        }
        $point->leftScore -= $score;
        $point->unsend -= 1;
        $point->save();
        $userScore = new UserScore();
        $userScore->activityId = $activityId;
        $userScore->userId = $userId;
        $userScore->username = $user->username;
        $userScore->score = $score;
        $userScore->getTime = time();
        if(!$userScore->save()) {
            $transaction->rollBack();
            return Helper::formatJson(1007,'抽奖失败');
        }
        //将抽奖积分写入积分系统
        $transaction->commit();
        //判断用户是否计入积分系统
        $result = Helper::curl_exec(POINT_API['checkUser'].'?projectNo=sft','GET',true,[],['Authorization : '.$user['access_token']]);
        if($result->Status == 1) {
            //将用户写入积分系统
            $result = Helper::curl_exec(POINT_API['addUser'],'POST',true,[
                'userID' => $userId,
                'token'  => $user['access_token']
            ]);
        }
        $res = Helper::curl_exec(POINT_API['setPoint'].'?type=Lottery&projectNo=sft&value='.$score,'POST',true,[],['Authorization : '.$user['access_token']]);
        if($res->Status == 0) {
            return Helper::formatJson(200,'OK',$score);
        }else {
            return Helper::formatJson(1007,'记录失败');
        }
    }

    /**返回手机端活动大图
     * @return array
     */
    public function actionGetMobileImage()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $activityId = intval($request->get('activityId'));
        if(!$activityId) {
            return Helper::formatJson(1001,'缺少参数');
        }
        $activity = Activity::find()->select('mobileImage')->where(['id' => $activityId])->one();
        return Helper::formatJson(200,'OK',$activity->mobileImage);
    }

    /**记录用户是否参加问答
     * @return bool
     */
    public function actionRecordAnswer()
    {
        $request = \Yii::$app->request;
        if(!$request->isPost) {
            return false;
        }
        $activityId = intval($request->post('activityId'));
        $access_token = $request->post('code');
        if(!$activityId || !$access_token) {
            return false;
        }
        $activity = Activity::find()
            ->where([
                'id' => $activityId,
                'type' => ACTIVITY_TYPE['question'],
                'status' => ACTIVITY_STATUS['started'],
            ])
            ->andWhere(['>','activityEndTime',time()])
            ->one();
        $userId = User::getUserId($access_token);
        if(!$activity || !$userId) {
            return false;
        }
        $isSignUp = Signup::find()
            ->where(['userId' => $userId,'activityId' => $activityId])
            ->one();
        if($isSignUp && $isSignUp->status == SIGN_STATUS['init']) {
            Signup::updateAll(['status' => SIGN_STATUS['match']],['userId' => $userId,'activityId' => $activityId]);
        }else {
            return false;
        }
        return true;
    }

    /**判断用户是否能参加问答
     * @return bool
     */
    public function actionJuadeAnswer()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return false;
        }
        $activityId = intval($request->get('activityId'));
        $access_token = $request->get('code');
        if(!$activityId || !$access_token) {
            return false;
        }
        //获取用户id
        $userId = User::getUserId($access_token);
        if(!$userId) {
            return false;
        }
        //判断该问答用户是否能参与
        $result = ActivityOrganization::juadeMatch($activityId,$userId);
        if(!$result) {
            return false;
        }
        $activity = Activity::find()
            ->where([
                'id' => $activityId,
                'type' => ACTIVITY_TYPE['question'],
                'status' => ACTIVITY_STATUS['started'],
            ])
            ->andWhere(['>','activityEndTime',time()])
            ->one();
        if(!$activity) {
            return false;
        }
        $isSignUp = Signup::find()
            ->where(['userId' => $userId,'activityId' => $activityId])
            ->one();
        if(!$isSignUp || $isSignUp->status == SIGN_STATUS['match']) {
            return false;
        }
        return true;
    }
}