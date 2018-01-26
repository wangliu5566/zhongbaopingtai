<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%hits}}".
 *
 * @property string $id
 * @property string $primaryId
 * @property string $hits
 * @property int $type 类型:1 作品 ;2活动
 */
class Hits extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hits}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['primaryId'], 'required'],
            [['primaryId', 'hits', 'type'], 'integer'],
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
            'hits' => 'Hits',
            'type' => 'Type',
        ];
    }

    /**
     * 查询点赞
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function getListByWhere($where)
    {
        return self::find()->where($where)->all();
    }
}
