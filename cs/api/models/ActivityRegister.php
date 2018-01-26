<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%activity_register}}".
 *
 * @property int $id
 * @property int $activityId
 * @property int $number 注册用户数
 */
class ActivityRegister extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activity_register}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activityId'], 'required'],
            [['activityId', 'number'], 'integer'],
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
            'number' => 'Number',
        ];
    }
}
