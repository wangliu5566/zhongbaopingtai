<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%share}}".
 *
 * @property string $id
 * @property string $primaryId
 * @property string $share
 * @property int $type 类型:1 作品 ;2 活动
 */
class Share extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%share}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['primaryId'], 'required'],
            [['primaryId', 'share', 'type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'primaryId' => 'Primary ID',
            'share' => 'Share',
            'type' => 'Type',
        ];
    }

    /**
     * 查询分享
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getListByWhere($where)
    {
        return self::find()->where($where)->all();
    }

    /**
     * 根据活动id查询活动作品的分享
     * @param $activeId
     */
    public function getShareByActiveId($activeId)
    {
       $works = Works::find()->select(['GROUP_CONCAT(id) as workId'])->where(['activityId' => explode(',', $activeId)])->asArray()->one();
       if($works['workId']){
           return self::find()->where(['primaryId' => explode(',', $works['workId'])])->sum('share');
       }

       return 0;
    }
}
