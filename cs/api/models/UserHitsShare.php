<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%user_hits_share}}".
 *
 * @property string $id
 * @property string $primaryId
 * @property string $userId 用户ID
 * @property int $type 类型: 1 作品 2 活动
 */
class UserHitsShare extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_hits_share}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['primaryId', 'userId'], 'required'],
            [['primaryId', 'userId', 'type'], 'integer'],
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
            'userId' => 'User ID',
            'type' => 'Type',
        ];
    }
}
