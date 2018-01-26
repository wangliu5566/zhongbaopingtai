<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%files_info}}".
 *
 * @property string $id
 * @property string $activityId 活动ID
 * @property string $userId 用户ID
 * @property string $clientFileName 文件客户端名称
 * @property string $serverFileName 文件服务端名称
 * @property int $lastModifiedTime 文件最后修改时间
 * @property string $uploadSize 文件已上传大小
 * @property string $totalSize 文件总大小
 * @property int $status 文件状态:0 未完成 1 已上传
 */
class FilesInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%files_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activityId', 'userId', 'clientFileName', 'serverFileName', 'lastModifiedTime', 'uploadSize', 'totalSize'], 'required'],
            [['activityId', 'userId', 'lastModifiedTime', 'uploadSize', 'totalSize', 'status'], 'integer'],
            [['clientFileName', 'serverFileName'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activityId' => 'Activity ID',
            'userId' => 'User ID',
            'clientFileName' => 'Client File Name',
            'serverFileName' => 'Server File Name',
            'lastModifiedTime' => 'Last Modified Time',
            'uploadSize' => 'Upload Size',
            'totalSize' => 'Total Size',
            'status' => 'Status',
        ];
    }


    /**保存上传文件信息
     * @param $data
     * @return bool|null
     */
    public static function saveInfo($data)
    {
        if(empty($data)) {
            return false;
        }
        extract($data);
        //查找是否有文件信息
        $file = self::find()
            ->where([
                'clientFileName' => $clientFileName,
                'serverFileName' => $serverFileName,
                'activityId' => $activityId,
                'userId' => $userId,
                'lastModifiedTime' => $lastModifiedTime,
                'totalSize' => $totalSize])
//            ->createCommand()->getRawSql();
            ->one();
//        var_dump($file);exit;
        //如果有文件记录并且文件全部上传更新相应数据
        if($file && $totalSize < $uploadSize) {
            $file->uploadSize = $totalSize;
            $file->status = FILE_STATUS['finish'];
            $file->save();
        }else if($file && $totalSize > $uploadSize) {
            $file->load($data,'');
            if($file->validate()) {
                $file->save();
            }else {
                return $file->getErrors();
            }
        }else if(!$file){
            //第一次上传
            $file = new FilesInfo();
            if($totalSize < $uploadSize) {
                $data['uploadSize'] = $totalSize;
                $file->status = FILE_STATUS['finish'];
            }
            $file->load($data,'');
            if($file->validate()) {
                $file->save();
            }else {
                return $file->getErrors();
            }
        }
        return true;
    }
}
