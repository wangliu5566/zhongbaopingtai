<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/7/20
 * Time: 19:16
 */

namespace common\helpers;

class File
{
    private $allow = ['zip'];
    private $allowSize ;
    private $file;
    private $fileName;
    private $format;
    private $src;
    private $serverName;
    /*
     * @param1 string  $_Files
     * @param2 string $fileName 图片名称
     * @param3 string $src 保存地址
     * */
    public function __construct($file,$fileName,$src,$serverName){
        $this->file = $file;
        $this->fileName = $fileName;
        $this->serverName = $serverName;
        $this->src = '\data\zip'.$src;
//        $file_types = explode(".", $file[$fileName]['name']);
        $file_types = explode(".", $this->file);
        $this->format = strtolower($file_types[count($file_types) - 1]);
    }

    /*
     * 检查格式是否正确
     *
     * */
    private function checkFormat(){
        if (!in_array($this->format, $this->allow)) {
            return false;
        }
        return true;
    }

    /*
     * 保存文件
     * @return 文件存放位置
     * */
    public function saveFile(){
        //判断文件格式是否正确
        $isAllow = $this->checkFormat();
        if(!$isAllow) {
            return ['status' => false,'message' => '上传失败,文件格式不正确'];
        }
        //获取上传文件内容
        $file = file_get_contents($_FILES[$this->fileName]["tmp_name"]);
        if(empty($file)) {
            return ['status' => false,'message' => '上传失败,上传内容为空'];
        }
        $baseURL = \Yii::$app->basePath.'\web';
        /*判断文件存放地址是否存在*/
        if (!file_exists($baseURL.$this->src)) {
            mkdir($baseURL.$this->src, 0775, true);
        }
        $fileUrl = $this->src.'\\'.$this->serverName.'.'.$this->format;
        $res = file_put_contents($baseURL.$fileUrl,$file,FILE_APPEND|LOCK_EX);
        if(!empty($res)){
//            return $data = ['fileUrl' => $fileUrl,'uploadSize' => $uploadSize];
            //上传成功记录文件信息
           /* $uploadSize = filesize($baseURL.$fileUrl);
            $serverName = $this->serverName.'.'.$this->format;
            return $data = ['uploadSize' => $uploadSize,'serverName' => $serverName];*/
            return ['status' => true,'message' => '上传成功','data' => $res];
        }else{
//            return $error = ['url' => $baseURL.$fileUrl ,'file' => $_FILES[$this->fileName]["tmp_name"],'uploadSize' => $res];
            return ['status' => false,'message' => '上传失败,文件写入失败'];
        }
    }
}