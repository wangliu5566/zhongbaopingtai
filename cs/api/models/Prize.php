<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%prize}}".
 *
 * @property int $id
 * @property int $activityId
 * @property string $prizeName
 * @property string $prizeIntro
 * @property int $totalPeople
 * @property int $winners
 */
class Prize extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%prize}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activityId', 'prizeName', 'totalPeople','type'], 'required'],
            [['activityId', 'totalPeople'], 'integer'],
            [['prizeName'], 'string', 'max' => 20],
            [['prizeIntro'], 'string', 'max' => 256],
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
            'prizeName' => 'Prize Name',
            'prizeIntro' => 'Prize Intro',
            'totalPeople' => 'Total People',
            'winners' => 'Winners',
            'type' => 'Type',
        ];
    }

    /**
     * 查询活动奖项
     * @param  [type] $activeId [description]
     * @return [type]           [description]
     */
    public static function getListByActiveId($activeId, $type)
    {
        return self::find()->where(['activityId' => intval($activeId), 'type' => $type])->asArray()->all();
    }
}
