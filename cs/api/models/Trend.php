<?php

namespace api\models;

use Yii;
use yii\db\Query;
use yii\data\Pagination;

class Trend extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%trend}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activeId', 'type', 'number', 'total', 'addTime'], 'required'],
            [['activeId', 'type', 'number', 'total', 'addTime'], 'integer'],
        ];
    }

    /**
     * 获取活动参与率数据
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function getActivityData($type, $userId)
    {
        $where = array();
        $query = self::find(['a.*'])->alias('a')
            ->leftJoin(Activity::tableName() . 'as b', 'b.id = a.activeId');

    	if($type){
    		$where['a.type'] = $type;
    	}

        if(!(new AuthItem())->userIsAdmin($userId)){
            $where['b.userId'] = intval($userId);
        }

        $rows = $query->where($where)->all();
    	
    	$data = array(
    		'0-15' => 0,
    		'15-30' => 0,
    		'30-45' => 0,
    		'45-60' => 0,
    		'60-75' => 0,
    		'75-90' => 0,
    		'90-' => 0
    	);

    	if($rows){
    		foreach($rows as $key => $value){
    			$point = $value['point'];
    			if($point < 0.15){
    				$data['0-15']++;
    			}elseif($point >= 0.15 && $point < 0.3){
    				$data['15-30']++;
    			}elseif($point >= 0.3 && $point < 0.45){
    				$data['30-45']++;
    			}elseif($point >= 0.45 && $point < 0.60){
    				$data['45-60']++;
    			}elseif($point >= 0.60 && $point < 0.75){
    				$data['60-75']++;
    			}elseif($point >= 0.75 && $point < 0.90){
    				$data['75-90']++;
    			}elseif($point >= 0.90){
    				$data['90-']++;
    			}	
    		}
    	}

        $list['yAlias'] = array_values($data);

    	return $list;
    }

    /**
     * 查询活动列表
     * @return [type] [description]
     */
    public function getActiveList($type, $point, $userId, $pageSize = 20)
    {
        $query = self::find()->alias('a')
            ->select(['b.name', 'b.companyName', 'b.id', 'b.worksCount', 'b.status', 'b.type', 'a.number', 'round(a.point*100) as point'])
            ->leftJoin(Activity::tableName() . ' as b', 'a.activeId = b.id');
        if($point){
            $range = explode('-', $point);
            $min = $range[0];
            $max = $range[1] ? $range[1] : 10000;

            $where = array('>=', 'a.point', $min/100);
            $query->where($where)->andWhere(array('<', 'a.point', $max/100));
        }

        $andWhere = array();
        if(!(new AuthItem())->userIsAdmin($userId)){
            $andWhere['b.userId'] = intval($userId);
        }

        if($type){
            $andWhere['b.type'] = $type;
        }
        $query->andWhere($andWhere);

        /*分页获取数据*/
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => $pageSize]);

        $list['rows'] = $query->offset($pagination->offset)
        ->limit($pageSize)
        ->asArray()
        ->all();

        $list['total'] = $count;

        return $list;
    }

}