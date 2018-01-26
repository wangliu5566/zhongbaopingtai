<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/7/20
 * Time: 19:16
 */

namespace common\helpers;



class Image
{
    private $allow = ['png', 'jpeg', 'jpg'];
    private $allowSize ;
    private $file;
    private $fileName;
    private $format;
    private $src;
    /*
     * @param1 string  $_Files
     * @param2 string $fileName 图片名称
     * @param3 string $src 保存地址
     * */
    public function __construct($file,$fileName,$src){
        $this->file = $file;
        $this->fileName = $fileName;
        $this->src = $src;
        $file_types = explode(".", $file[$fileName]['name']);
        $this->format = $file_types[count($file_types) - 1];
    }

    /*
     * 检查格式是否正确
     *
     * */
    public function checkFormat(){
        if (!in_array(strtolower($this->format), $this->allow)) {
            return false;
        }
        return true;
    }

    /*
     * 保存图片
     * @return 图片存放位置
     * */
    public function saveImage(){
        $basePathApi = \Yii::$app->basePath;
        /*判断图片存放地址是否存在*/
        if (!file_exists($basePathApi.'\web'.$this->src)) {
            mkdir($basePathApi.'\web'.$this->src, 0775, true);
        }
        $activityImageName = time().rand(100,999);
        $imgDetail = $this->src . '\\' . $activityImageName;
        $img = file_get_contents($_FILES[$this->fileName]["tmp_name"]);

        $res = file_put_contents($basePathApi.'\web'.'\\'.$imgDetail.'.'.$this->format,$img);
        if($res){
            return $imgDetail.'.'.$this->format;
        }else{
            return false;
        }
    }
}