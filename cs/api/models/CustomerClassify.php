<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%customer_classify}}".
 *
 * @property int $id
 * @property string $classify 客户分类
 */
class CustomerClassify extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customer_classify}}';
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
            [['classify'], 'required'],
            [['classify'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'classify' => 'Classify',
        ];
    }

	/**
	 * 列表
	 */
	public function getList()
	{
		return self::find()->select(['CONCAT(id, "-", classify) as id', 'classify'])->asArray()->all();
	}

}
