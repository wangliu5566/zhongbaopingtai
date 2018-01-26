<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%introimage}}".
 *
 * @property int $id
 * @property int $activityId
 * @property string $introImage
 */
class IntroImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%intro_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activityId', 'introImage'], 'required'],
            [['activityId'], 'integer'],
            [['introImage'], 'string', 'max' => 60],
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
            'introImage' => 'Intro Image',
        ];
    }
}
