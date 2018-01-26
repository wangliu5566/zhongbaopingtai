<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "hawk_school_level".
 *
 * @property int $id
 * @property string $level 学校级别
 */
class SchoolLevel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hawk_school_level';
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
            [['level'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Level',
        ];
    }

	/**
	 * 获取等级列表
	 */
    public function getList()
    {
    	return self::find()->select(['CONCAT(id, "-", level) as id', 'level'])->asArray()->all();
    }
}
