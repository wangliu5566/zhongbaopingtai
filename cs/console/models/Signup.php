<?php

namespace console\models;

use Yii;

class Signup extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%signup}}';
    }

	/**
     * 统计参加活动的用户
     * @return [type] [description]
     */
    public function countJoinUser($activeIds)
    {
        return self::find()->select(['count(userId) as number', 'activityId'])
            ->where(['activityId' => $activeIds])
            ->groupBy('activityId')
            ->asArray()
            ->all();
    }
}