<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%area}}".
 *
 * @property int $id
 * @property int $code 代码
 * @property string $province 省
 * @property string $city 市
 * @property string $area
 * @property int $number 区号
 * @property int $zipcode 邮编
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%area}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'province'], 'required'],
            [['code', 'number', 'zipcode'], 'integer'],
            [['province', 'city', 'area'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'number' => 'Number',
            'zipcode' => 'Zipcode',
        ];
    }

    /**
     * 省份列表
     */
    public function getProvinceList()
    {
        return self::find()->select(['province as label', 'province as value', 'code as id'])->where(['city' => ''])->asArray()->all();
    }

    /**
     * 城市列表
     */
    public function getCityList($code)
    {
        return self::find()->select(['city as label', 'city as value'])
            ->where(['area' => ''])
            ->andWhere(['!=', 'city', ''])
            ->andWhere(['>=', 'code', $code])
            ->andWhere(['<', 'code', $code + 10000])
            ->asArray()
            ->all();
    }
}
