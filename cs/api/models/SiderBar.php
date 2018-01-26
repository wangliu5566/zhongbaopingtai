<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%sider_bar}}".
 *
 * @property string $id
 * @property string $label
 * @property string $icon
 * @property string $path
 * @property int $parentId
 * @property string $role
 * @property int $sn 排序
 */
class SiderBar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sider_bar}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label', 'path', 'role'], 'required'],
            [['parentId', 'sn'], 'integer'],
            [['label', 'icon', 'role'], 'string', 'max' => 20],
            [['path'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => 'Label',
            'icon' => 'Icon',
            'path' => 'Path',
            'parentId' => 'Parent ID',
            'role' => 'Role',
            'sn' => 'Sn',
        ];
    }
}
