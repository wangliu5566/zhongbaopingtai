<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%user_score}}".
 *
 * @property string $id
 * @property string $activityId
 * @property string $userId
 * @property string $username
 * @property int $score 抽奖积分
 * @property string $getTime 抽奖时间
 */
class UserScore extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_score}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activityId', 'userId', 'score', 'getTime'], 'required'],
            [['activityId', 'userId', 'score', 'getTime'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activityId' => 'Activity ID',
            'userId' => 'User ID',
            'score' => 'Score',
            'getTime' => 'Get Time',
        ];
    }
}
