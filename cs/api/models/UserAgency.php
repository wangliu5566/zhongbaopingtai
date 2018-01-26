<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%user_agency}}".
 *
 * @property string $id
 * @property string $userId 用户ID
 * @property string $agencyId 机构ID
 */
class UserAgency extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_agency}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'agencyId'], 'required'],
            [['userId', 'agencyId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'agencyId' => 'Agency ID',
        ];
    }
}
