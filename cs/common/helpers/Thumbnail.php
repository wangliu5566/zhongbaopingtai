<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/7/20
 * Time: 18:46
 */

namespace common\helpers;

//图片处理工具类,制作缩略图
class Thumbnail{
    //属性
    private $thumb_width; //缩略图的宽
    private $thumb_height;
    public $thumbName = 'thumb_';
    //错误属性
    public $thumb_error;
    //构造方法
    public function __construct($width = 560,$height = 370){
        $this->thumb_width = ($width == 0) ? $GLOBALS['config']['admin_goods_thumb']['width'] : $width;
        $this->thumb_height = ($height == 0) ? $GLOBALS['config']['admin_goods_thumb']['height'] : $height;
    }
    /*
     * 制作缩略图
     * @param1 string $src，原图路径，/uploads/20150122101010abcdef.gif
     * @param2 string $path，缩略图保存路径/uploads/thumb_20150122101010abcdef.gif
     * @return 缩略图的名字
    */
    public function makeThumb($src,$path,$location = true){
        //判断原图是否存在
        if(!file_exists($src)){
            $this->thumb_error = '原图不存在！';
            return false;
        }

        //打开原图资源
        //获取能够使用的后缀
        $ext = $this->getFunctionName($src); //gif
        //拼凑函数名
        $open = 'imagecreatefrom' . $ext;    //imagecreatefromgif
        $save = 'image' . $ext;          //imagegif
        //如果不清楚；echo $open,$save;exit;
        //可变函数打开原图资源
        $src_img = $open($src); //利用可变函数打开图片资源
        //imagecreatefromgif($src)
        //缩略图资源
        $dst_img = imagecreatetruecolor($this->thumb_width,$this->thumb_height);
        //背景色填充白色
        $dst_bg_color = imagecolorallocate($dst_img,255,255,255);
        imagefill($dst_img,0,0,$dst_bg_color);
        //宽高比确定宽高
        $dst_size = $this->thumb_width / $this->thumb_height;
        //获取原图数据
        $file_info = getimagesize($src);
        $src_size = $file_info[0]/$file_info[1];
        //求出缩略图宽和高
        if($src_size > $dst_size){
            //原图宽高比大于缩略图
            $width = $this->thumb_width;
            $height = round($width / $src_size);
        }else{
            $height = $this->thumb_height;
            $width = round($height * $src_size);
        }
        //求出缩略图起始位置
        if($location === true){
            $dst_x = round($this->thumb_width - $width)/2;
            $dst_y = round($this->thumb_height - $height)/2;
        }else{
            $dst_x = round($this->thumb_width - $width)/2;
            $dst_y = 0;
        }

        //制作缩略图
        if(imagecopyresampled($dst_img,$src_img,$dst_x,$dst_y,0,0,$width,$height,$file_info[0],$file_info[1])){
            //采样成功：保存，将文件保存到对应的路径下
            $thumb_name = $this->thumbName . basename($src);
            $save($dst_img,$path . '\\' . $thumb_name);
            //保存成功
            return $thumb_name;
        }else{
            //采样失败
            $this->thumb_error = '缩略图采样失败！';
            return false;
        }
    }
    /*
     * 获取文件要调用的函数名
     * @param1 string $file，文件名字
     * @return 通过文件后缀名得到的函数字符串
    */
    private function getFunctionName($file){
        //得到文件的后缀
        $file_info = pathinfo($file);
        $ext = $file_info['extension']; //后缀：gif,png,jpg,jpeg,pjpeg
        //imagecreatefromgif,imagecreatefromjpeg,imagecreatefrompng
        //定义一个数组保存函数名
        $func = array(
            'gif' => 'gif',
            'png' => 'png',
            'jpg' => 'jpeg',
            'jpeg' => 'jpeg',
            'pjpeg' => 'jpeg'
        );
        //返回值
        return $func[strtolower($ext)];
    }


}