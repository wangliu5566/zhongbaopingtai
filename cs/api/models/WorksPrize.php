<?php

namespace api\models;

use Yii;

class WorksPrize extends \yii\db\ActiveRecord
{

	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%works_prize}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['workId', 'prizeId'], 'required'],
            [['workId', 'prizeId'], 'integer'],
        ];
    }

    /**
     * 根据作品ID查询奖项
     * @param  [type] $workIds [description]
     * @return [type]          [description]
     */
    public function getPrizeByWorkIds($workIds)
    {
    	return self::find()->alias('a')
    		->select(['a.workId', 'b.prizeName'])
    		->where(['a.workId' => $workIds])
    		->innerJoin(Prize::tableName() . 'as b', 'b.id = a.prizeId')
            ->asArray()
    		->all();
    }

    /**
     * 查询作品已设置奖项
     * @param  [type] $workId [description]
     * @return [type]         [description]
     */
    public static function getPrizeList($workId)
    {
        return self::find()->where(['workId' => intval($workId)])->asArray()->all();
    }

    /**
     * 设置活动奖项
     * @param [type] $workId  [description]
     * @param [type] $prizeId [description]
     */
    public function setPrize($workId, $prizeId)
    {
    	$result = self::findOne(['workId' => intval($workId), 'prizeId' => intval($prizeId)]);
    	if($result){
    		/*已设置则删除*/
    		return $result->delete();
    	}else{
    		$this->workId = $workId;
    		$this->prizeId = $prizeId;
    		return $this->save();
    	}
    }

    /**
     * 判断作品是否已设置奖项
     * @param  [type] $workId [description]
     * @return [type]         [description]
     */
    public function isSetPrize($workId)
    {
    	if(self::findOne(['workId' => intval($workId)])){
    		return true;
    	}

    	return false;
    }

    /**
     * 统计奖项设置数量
     * @param  [type] $prizeId [description]
     * @return [type]          [description]
     */
    public function getCountWorks($prizeId)
    {
    	return self::find()->where(['prizeId' => intval($prizeId)])->count('id');
    }

}