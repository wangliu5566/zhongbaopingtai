<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%activity_time}}".
 *
 * @property string $id
 * @property string $type 类型名称
 * @property string $activityId
 * @property string $signStartTime
 * @property string $signEndTime
 * @property string $reviewStartTime 评审时间
 * @property string $announceAwardsTime 评奖时间
 * @property string $sn
 */
class ActivityTime extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activity_time}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'activityId', 'signStartTime', 'signEndTime', 'reviewStartTime', 'announceAwardsTime','sn'], 'required'],
            [['type'], 'string', 'max' => 20],
        ];
    }

    public function beforeSave($insert)
    {

        if(parent::beforeSave($insert))
        {
            if($this->isNewRecord)
            {
                $this->signStartTime=mb_substr( $this->getAttributes(['signStartTime'])['signStartTime'], 0, 10);
                $this->signEndTime=intval(mb_substr( $this->getAttributes(['signEndTime'])['signEndTime'], 0, 10))+86399;
                $this->reviewStartTime=mb_substr( $this->getAttributes(['reviewStartTime'])['reviewStartTime'], 0, 10);
                $this->announceAwardsTime=mb_substr( $this->getAttributes(['announceAwardsTime'])['announceAwardsTime'], 0, 10);
            }
            return true;
        }
        else
            return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'activityId' => 'Activity ID',
            'signStartTime' => 'Sign Start Time',
            'signEndTime' => 'Sign End Time',
            'reviewStartTime' => 'Review Start Time',
            'announceAwardsTime' => 'Announce Awards Time',
            'sn' => 'Sn',
        ];
    }
}
