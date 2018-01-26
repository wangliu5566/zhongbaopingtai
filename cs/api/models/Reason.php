<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%reason}}".
 *
 * @property string $id
 * @property int $activityId
 * @property int $userId
 * @property string $username
 * @property string $reason
 * @property int $reviewDate
 */
class Reason extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reason}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activityId', 'reason'], 'required'],
            [['activityId', 'userId', 'reviewDate'], 'integer'],
            [['username'], 'string', 'max' => 10],
            [['reason'], 'string', 'max' => 255],
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
            'username' => 'Username',
            'reason' => 'Reason',
            'reviewDate' => 'Review Date',
        ];
    }
}
