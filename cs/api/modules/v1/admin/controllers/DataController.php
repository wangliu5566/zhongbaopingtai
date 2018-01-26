<?php

namespace api\modules\v1\admin\controllers;

use api\models\Agency;
use api\models\UserAgency;
use api\models\Works;
use Yii;
use api\controllers\BaseAPIAuthController;
use api\models\Activity;
use api\models\Trend;
use common\helpers\Helper;
use api\models\Share;

/**
 * Class DataController
 * @package api\modules\v1\admin\controllers
 */
class DataController extends BaseAPIAuthController
{

	public function actions()
	{
		return [];
	}

	/**
	 * 获取活动数据
	 * @return [type] [description]
	 */
	public function activity($userId, $day, $gran, $area)
	{
	    //$userId = Yii::$app->request->get('uid');
		$model = new Activity();
		$list = array(
			'activity' => array(
				'title' => '大赛',
				'init' => 0,
				'started' => 0,
				'end' => 0,
                'user' => 0,
                'share' => 0
			),
			'question' => array(
				'title' => '问卷',
				'init' => 0,
				'started' => 0,
				'end' => 0,
                'user' => 0,
                'share' => 0
			),
			'total' => array(
				'title' => '汇总',
				'init' => 0,
				'started' => 0,
				'end' => 0,
                'user' => 0,
                'share' => 0
			)
		);

		$data = $model->getData($userId, $day, $gran, $area);

		//$totalActive = $model->countActive($type);
		$init = 0;
		$started = 0;
		$end = 0;
        $share = 0;
        $user = 0;

		if($data){
			foreach($data as $key => $value){
				if($value['type'] == ACTIVITY_TYPE['active']){
					$type = 'activity';
				}else{
					$type = 'question';
				}

				if($value['status'] == ACTIVITY_STATUS['init']){
					$status = 'init';
					$init += intval($value['num']);
				}elseif($value['status'] == ACTIVITY_STATUS['started']){
					$status = 'started';
					$started += intval($value['num']);
				}elseif($value['status'] == ACTIVITY_STATUS['end']){
					$status = 'end';
					$end += intval($value['num']);
				}

				$shareCount = (new Share())->getShareByActiveId($value['activeId']);
                $share += $shareCount;
				$user += intval($value['user']);

				$list[$type][$status] = intval($value['num']);
				$list[$type]['share'] += $shareCount;
				$list[$type]['user'] += intval($value['user']);
			}
		}

		$list['total']['init'] = $init;
		$list['total']['started'] = $started;
		$list['total']['end'] = $end;
		$list['total']['share'] = $share;
		$list['total']['user'] = $user;

		return array_values(array_values($list));
	}

	/**
	 * 统计活动状态占百分比
	 * @return [type] [description]
	 */
	public function actionActivePoint()
	{
	    $userId = Yii::$app->request->get('uid');
		$model = new Activity();
		$type = Yii::$app->request->get('type');
		$day = Yii::$app->request->get('days', 0);
		$gran = Yii::$app->request->get('gran', 'hour');
		$area = Yii::$app->request->get('area');
		$list = array(
			'init' => 0,
			'started' => 0,
			'end' => 0,
			'total' => 0
		);

		$data = $model->countStatus(intval($type), $userId, $day, $gran, $area);
		$init = 0;
		$started = 0;
		$end = 0;
		$total = 0;

		if($data){
			foreach($data as $key => $value){
				if($value['status'] == ACTIVITY_STATUS['init']){
					$init += intval($value['num']);
				}elseif($value['status'] == ACTIVITY_STATUS['started']){
					$started += intval($value['num']);
				}elseif($value['status'] == ACTIVITY_STATUS['end']){
					$end += intval($value['num']);
				}

				$total += intval($value['num']);
			}
		}

		$list['total'] = $total;
		$list['init'] = $init;
		$list['started'] = $started;
		$list['end'] = $end;

		$rows['count'] = $this->activity($userId, $day, $gran, $area);
		$rows['point'] = $list;

		return Helper::formatJson(200, 'Ok', $rows);
	}

	/**
	 * 活动参与率
	 * @return [type] [description]
	 */
	public function actionParticipate()
	{
		$type = Yii::$app->request->get('type');
		$userId = Yii::$app->request->get('uid');

		$model = new Trend();
		$data = $model->getActivityData($type, $userId);

		return Helper::formatJson(200, 'Ok', $data);
	}

	/**
	 * 查询活动统计数据
	 * @return [type] [description]
	 */
	public function actionList()
	{
		$type = Yii::$app->request->get('type');
		$point = Yii::$app->request->get('point');
		$pageSize = Yii::$app->request->get('pageSize', 20);
		$userId = Yii::$app->request->get('uid');

		$model = new Trend();
		$data = $model->getActiveList($type, $point, $userId, $pageSize);

		return Helper::formatJson(200, 'Ok', $data);
	}

	/**
	 * 查询活动趋势
	 * @return [type] [description]
	 */
	public function actionTrend()
	{
        $userId = Yii::$app->request->get('uid');
		$model = new Activity();
		$data = $model->countActivity($userId);

		return Helper::formatJson(200, 'Ok', $data);
	}

    /**
     * 统计活动相关数据
     */
	public function actionActive($uid, $pageSize = 20)
    {
        if(!$uid){
            return Helper::formatJson(1001, '参数错误');
        }

        $model = new Activity();
        $data = $model->getActiveRelatedCount($uid, $pageSize);

        return Helper::formatJson(200, 'Ok', $data);
    }

    /**
     * 查询活动有哪些学校参加
     */
    public function actionSchoolList()
    {
        $activityId = Yii::$app->request->get('activityId');
        $pageSize = Yii::$app->request->get('pageSize', 20);
        if(!$activityId){
            return Helper::formatJson(1001, '参数错误');
        }

        $data = (new Works())->getJoinSchoolListByActivity($activityId, $pageSize);
        return Helper::formatJson(200, 'Ok', $data);
    }

    /**
     * 查询参加活动的用户列表
     */
    public function actionJoinUser()
    {
        $name = Yii::$app->request->get('name');
        $activityId = Yii::$app->request->get('activityId');
        $pageSize = Yii::$app->request->get('pageSize', 20);

        if(!$activityId || !$name){
            return Helper::formatJson(1001, '参数错误');
        }

        $data = (new Works())->getJoinActivityUser(trim($name), $activityId, $pageSize);
        return Helper::formatJson(200, 'Ok', $data);
    }

    /*public function actionUpdateWorks()
    {
        $userIds = array();
        $works = Works::find()->select(['userId', 'id'])->all();
        if($works){
            foreach($works as $work){
                $userIds[] = $work->userId;
            }
        }

        if($userIds){
            $res = UserAgency::find()->select(['b.name', 'a.userId'])->alias('a')
                ->leftJoin(Agency::tableName() . ' as b', 'b.id = a.agencyId')
                ->where(['a.userId' => $userIds])
                ->indexBy('userId')
                ->asArray()
                ->all();

            $sql = '';
            foreach($works as $work){
                if(isset($res[$work->userId])){
                    $sql .= "UPDATE cs_works set departments = '" . $res[$work->userId]['name'] . "' where id = " . $work->id . ";";
                }
            }

            Yii::$app->db->createCommand($sql)->execute();
        }
    }*/

}