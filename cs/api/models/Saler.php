<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%saler}}".
 *
 * @property int $id
 * @property string $saler 销售名称
 */
class Saler extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%saler}}';
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
            [['saler'], 'required'],
            [['saler'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'saler' => 'Saler',
        ];
    }
}
