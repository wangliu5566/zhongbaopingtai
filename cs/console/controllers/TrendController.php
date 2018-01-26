<?php

namespace console\controllers;

use Yii;
use common\helpers\Helper;
use console\models\Activity;
use console\models\Trend;
use console\models\Signup;
use api\models\ActivityOrganization;

class TrendController extends \yii\console\Controller
{

	/**
	 * 统计已结束活动参与人数
	 * @return [type] [description]
	 */
	public function actionCountUser()
	{
	    $notInIds = array();
        $activeList = Trend::find()->select(['activeId'])->all();
        if($activeList){
            foreach ($activeList as $key => $value){
                $notInIds[] = $value->activeId;
            }
        }

		/*查询已结束活动*/
		$model = new Activity();
        $organization = new ActivityOrganization();
		$activeList = $model->queryEndActive($notInIds);

		$activeIds = array();
		/*$userIds = array();*/
		if($activeList){
			foreach($activeList as $key => $active){
				$activeIds[] = $active->id;
				/*$userIds[] = $active->userId;*/
			}
		}

		/*查询活动参与人数*/
		$activeCount = array();
		$agencyIds = '';
		if($activeIds){
			$userJoinCount = (new Signup())->countJoinUser($activeIds);
			if($userJoinCount){
				foreach($userJoinCount as $k => $count){
					$activeCount[$count['activityId']] = $count['number'];
				}
			}

            $agencyIds = $organization->getAgencyIdByActiveId($activeIds);
		}

		/*查询活动结束时该学校总人数*/
		/*$userIds = array_unique($userIds);
		$agencyIds = (new SystemUser())->getAgencyIdByUserId($userIds);*/

		if(!$agencyIds){
			return false;
		}

		$body = array(
			'agencyId' => $agencyIds,
			'sign' => Helper::paramSign(array('agencyId' => $agencyIds))
		);

		$url = API_DOMAIN . '/v1/frontend/agency/user-count';
		$result = Helper::curl_exec($url, 'POST', true, $body);
		if($result){
			$list = (array)$result->data;
		}

		if($list){
			foreach($activeList as $key => $val){
				$trendModel = new Trend();
				$trendModel->activeId = $val->id;
				$trendModel->type = $val->type;
				$trendModel->number = isset($activeCount[$val->id]) ? $activeCount[$val->id] : 0;
				$trendModel->addTime = time();

				$trendModel->total = 0;
                $activeAgency = $organization->getAgencyIdByActiveId($val->id);
                if($activeAgency){
                    $ids = explode(',', $activeAgency);
                    foreach ($list as $item){
                        if(in_array($item->id, $ids)){
                            $trendModel->total += $item->userNum;
                        }
                    }
                }
                $trendModel->point = $trendModel->total ? round($trendModel->number/$trendModel->total, 2) : $trendModel->number;

				$trendModel->save();
			}
		}
	}	

	/**
	 * 更新活动状态
	 * @return [type] [description]
	 */
	public function actionUpdateActiveStatus()	
	{
		/*更新未开始活动为进行中*/
		$activeModel = new Activity();
		$list = $activeModel->getInitActive();

		if($list['ids']){
			$ids = $list['ids'];
			Yii::$app->db->createCommand()->update(Activity::tableName(), ['status' => ACTIVE_STATUS['started']], 'id in (' . $ids . ')')->execute();
		}

		/*更新进行中活动为已结束*/
		$rows = $activeModel->getStartedActive();

		if($rows['ids']){
			$ids = $rows['ids'];
			Yii::$app->db->createCommand()->update(Activity::tableName(), ['status' => ACTIVE_STATUS['end']], 'id in (' . $ids . ')')->execute();
		}
	}
}