<?php
/**
 * 解压zip文件
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/8/14
 * Time: 11:24
 */

namespace common\helpers;

class Unzip{
    public function __construct(){
        //init code here...

    }
    /**
     * 解压文件到指定目录
     *
     * @param  $src_file string  zip压缩文件的路径
     * @param  $dest_dir string  解压文件的目的路径
     * @param  $create_zip_name_dir boolean 是否以压缩文件的名字创建目标文件夹
     * @param  $overwrite boolean 是否重写已经存在的文件
     *@return boolean 返回成功 或失败
     */
    public function unzip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true){
        if ($zip = zip_open($src_file)){
            if ($zip){
                $src_file = str_replace('/','\\',$src_file);
                $splitter = ($create_zip_name_dir === true) ? "." : "\\";
                if($dest_dir === false){
                    $src_file = pathinfo($src_file);
                    $dest_dir = $src_file['dirname'].'\\'.time().rand(100,999)."\\";
                    $this->delDirAndFile($src_file['dirname']);
                }
                // 如果不存在 创建目标解压目录
                $this->create_dirs($dest_dir);

                // 对每个文件进行解压
                while ($zip_entry = zip_read($zip)){
                    // 文件不在根目录
                    $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");

                    if ($pos_last_slash !== false){
                        // 创建目录 在末尾带 /
                        $this->create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
                    }

                    // 打开包
                    if (zip_entry_open($zip,$zip_entry,"r")){
                        // 文件名保存在磁盘上
                        $file_name = $dest_dir.zip_entry_name($zip_entry);
                        //$file_name = iconv( "UTF-8","GB2312", $file_name);
                        // 检查文件是否需要重写
                        if ($overwrite === true || $overwrite === false && !is_file($file_name)){
                            // 读取压缩文件的内容

                            $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                            $fileType = pathinfo($file_name);

                            if(isset($fileType['extension'])){
                                $allow = ZIPALLOW[$fileType['extension']] ?? '';
                            }else{
                                $allow = '';
                            }
                            //查看格式是否正确,不正确的格式不让解压
                           if($allow){
                                @file_put_contents($dest_dir.time().rand(100,999).'.'.$fileType['extension'], $fstream);
                           }


                            // 设置权限
                            //chmod($file_name, 0777);

                        }
                        // 关闭入口
                        zip_entry_close($zip_entry);
                    }
                }
                // 关闭压缩包
                zip_close($zip);
                return $dest_dir;
            }
        }else{
            return false;
        }
        return true;
    }
    /**
     * 创建目录
     */
    public function create_dirs($path){
        if (!is_dir($path)){
            $directory_path = "";
            $directories = explode("\\",$path);

            array_pop($directories);

            foreach($directories as $directory){
                $directory_path .= $directory."/";
                if (!is_dir($directory_path)){
                    mkdir($directory_path,0777);
                   // chmod($directory_path, 0777);
                }
            }
        }
    }


    /**删除文件
     * @param $dirName
     * @return bool
     */
    public function delDirAndFile($dirName)
    {
        if ($handle = opendir($dirName)) {
            while (false !== ($item = readdir($handle))) {
                $file = strtolower(substr($item,strrpos($item,'.') + 1));
                if ($file != '' && $file != 'zip') {
                    if (is_dir($dirName.'\\'.$item)) {
                        $this->delDirAndFile($dirName.'\\'.$item);
                    }else {
                        unlink($dirName.'\\'.$item);
                    }
                }
            }
            closedir($handle);
        }
        $this->delEmptyDir($dirName);
        return true;
    }

    public function delEmptyDir($dirName)
    {
        if ($handle = opendir($dirName)) {
            while (false !== ($item = readdir($handle))) {
                $file = strtolower(substr($item,strrpos($item,'.') + 1));
                if ($file != '' && $file != 'zip') {
                    if (is_dir($dirName.'\\'.$item)) {
                        rmdir($dirName.'\\'.$item);
                    }
                }
            }
            closedir($handle);
        }
    }
    
}