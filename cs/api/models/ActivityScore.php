<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%activity_score}}".
 *
 * @property int $id
 * @property int $integration 积分总数
 * @property int $activityId 活动id
 * @property int $number 总共发放的积分个数
 * @property int $float 抽奖积分浮动数
 * @property int $unsend 未发放个数
 * @property int $average 平均积分
 * @property int $leftScore 剩余积分
 */
class ActivityScore extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activity_score}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['integration', 'activityId', 'number', 'float', 'unsend', 'average', 'leftScore'], 'required'],
            [['integration', 'activityId', 'number', 'float', 'unsend', 'average', 'leftScore'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'integration' => 'Integration',
            'activityId' => 'Activity ID',
            'number' => 'Number',
            'float' => 'Float',
            'unsend' => 'Unsend',
            'average' => 'Average',
            'leftScore' => 'Left Score',
        ];
    }
}
