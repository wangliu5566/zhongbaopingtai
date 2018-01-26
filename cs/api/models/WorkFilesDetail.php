<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%work_files_detail}}".
 *
 * @property int $id
 * @property int $workId 作品ID
 * @property int $picNum 图片文件总数
 * @property int $picSize 图片文件总大小
 * @property int $videoNum 视频文件总数
 * @property int $videoSize 视频文件总大小
 * @property int $otherNum 其他文件总数
 * @property int $otherSize 其他文件总大小
 * @property int $totalNum 其他文件总大小
 * @property int $totalSize 其他文件总大小
 */
class WorkFilesDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%work_files_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['workId', 'picNum', 'picSize', 'videoNum', 'videoSize', 'otherNum', 'otherSize','totalNum','totalSize'], 'required'],
            [['workId', 'picNum', 'picSize', 'videoNum', 'videoSize', 'otherNum', 'otherSize','totalNum','totalSize'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workId' => 'Work ID',
            'picNum' => 'Pic Num',
            'picSize' => 'Pic Size',
            'videoNum' => 'Video Num',
            'videoSize' => 'Video Size',
            'otherNum' => 'Other Num',
            'otherSize' => 'Other Size',
            'totalNum' => 'Total Num',
            'totalSize' => 'Total Size',
        ];
    }
}
