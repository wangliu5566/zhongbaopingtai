<?php

namespace console\models;

use Yii;

class Trend extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%trend}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activeId', 'type', 'point', 'addTime'], 'required'],
            [['activeId', 'type', 'number', 'total', 'addTime'], 'integer'],
            [['point'], 'number'],
        ];
    }

}