<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%signup}}".
 *
 * @property string $activityId 活动id
 * @property string $userId 报名用户id
 * @property int $status 状态:1报名 2 已参赛
 * @property int $signUpTime 报名时间
 */
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activityId', 'userId'], 'required'],
            [['activityId', 'userId', 'status', 'signUpTime'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'activityId' => 'Activity ID',
            'userId' => 'User ID',
            'status' => 'Status',
            'signUpTime' => 'Sign Up Time',
        ];
    }

    /**获取用户已参加活动ids
     * @param $userId
     * @return array|bool|null
     */
    public static function getUserActivityIds($userId)
    {
        if(!$userId) {
            return null;
        }
        $activityIds = self::find()
            ->select('activityId as id')
            ->where(['userId' => $userId])
            ->asArray()
            ->all();
        if(!$activityIds) {
            return false;
        }
        $ids = [];
        foreach ( $activityIds as $activityId) {
            $ids[] = $activityId['id'];
        }
        return $ids;
    }
}
