<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%works_file}}".
 *
 * @property int $id
 * @property string $detail 作品地址
 * @property string $type 作品类型 :1 图片2 视频 3 office文件
 * @property int $workId 作品id
 * @property string $pdfImage PDF图片地址

 */

class WorksFile extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%works_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['detail', 'type', 'workId'], 'required'],
            [['type', 'workId'], 'integer'],
            [['detail','pdfImage'], 'string', 'max' => 255],
        ];
    }

    /**
     * 
     * @param  [type] $workId [description]
     * @return [type]         [description]
     */
    public static function getWorksFileByWorkId($workId)
    {
        return self::find()
            ->where(['workId' => intval($workId)])
            ->asArray()->all();
    }

    public function getFileType($fileName){
        $image = [
            'jpg'=>1,
            'jpeg'=>1,
            'png'=>1,
            'gif'=>1,
            'svg'=>1,
            'dxf'=>1,
            'mp4'=>2,
            'rm'=>2,
            'rmvb'=>2,
            'wmv'=>2,
            'avi'=>2,
            '3gp'=>2,
            'mkv'=>2,
            'doc' => 3,
            'docx' => 3,
            'xls' => 3,
            'xlsx' => 3,
            'ppt' => 3,
            'pptx' => 3,
        ];

        $type = strtolower(pathinfo($fileName)['extension']);
        if(array_key_exists($type,$image)){
            $fileType = $image[$type];
            return $fileType;
        }else{
            return false;
        }

    }

}