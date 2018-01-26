<?php

namespace common\helpers;


use api\models\User;
use Yii;
use yii\web\Response;

class Helper
{
	/**
	 * 格式化返回数据结构
	 * @param  integer $status  [description]
	 * @param  string  $message [description]
	 * @param  array   $data    [description]
	 * @return [type]           [description]
	 */
	public static function formatJson($status = 200, $message = '', $data = array())
	{
		return array('status' => $status, 'message' => $message, 'data' => $data);
	}

	/**
	 * curl 请求
	 * @param  string $url     请求地址
	 * @param  string $type    请求的方式
	 * @param  boolean $format 是否格式化
	 * @param  array  $header  设置请求头信息
	 * @param  array  $body    设置请求参数
	 * @return [type]         [description]
	 */
	public static function curl_exec($url, $type = 'GET', $format = true, $body = array(), $header = array())
	{
		if(!$url){
			return false;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);

		if($header){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
 
        if($type == 'POST') 
        { 
            curl_setopt($ch, CURLOPT_POST, true); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body)); 
        } 

        $result = curl_exec($ch); 
        curl_close($ch);

        if($result){
        	if($format){
        		return json_decode($result);
        	}else{
        		return $result;
        	}
        }

        return false;
	}

	/**
	 * 参数签名
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public static function paramSign($params)
	{
		if(!$params){
			return false;
		}	

		$paramStr = '';
        ksort($params);

        foreach($params as $key => $value){
            if(is_array($value)){
                $str = '';
                foreach ($value as $v){
                    $str .= $v;
                }

                $paramStr .= $key . '=' . $str;
            }else{
                $paramStr .= $key . '=' . $value;
            }
        }

        /*secret*/
        $paramStr .= SECRET;
        return sha1($paramStr);
	}

    /**
     * GET方式请求无参数时调用
     * @return string
     */
	public static function getNoParams()
    {
        $params = GET_PARAMS;
        $paramStr = '';

        foreach ($params as $key => $val){
            $paramStr .= $key . '=' . $val . '&';
        }

        $paramStr .= 'sign=' . self::paramSign($params);
        return $paramStr;
    }

    /**将EXCEL转化成html
     * @param $excelName
     * @param $htmlName
     * @return bool
     */
    public static function excel2html($excelName,$htmlName)
    {
        $basePath = Yii::$app->getBasePath();
        $filePath = $basePath.'\web\\'.$excelName;
        $fileType =\PHPExcel_IOFactory::identify($filePath); //文件名自动判断文件类型
        $objReader = \PHPExcel_IOFactory::createReader($fileType);
        $objPHPExcel = $objReader->load($filePath);
        $savePath = $basePath.'\web\\'.$htmlName; //这里记得将文件名包含进去
        $objWriter = new \PHPExcel_Writer_HTML($objPHPExcel);
        $objWriter->setSheetIndex(0); //可以将括号中的0换成需要操作的sheet索引
        $objWriter->save($savePath); //保存为html文件
        return true;
    }

    /*public static function word2html($wordName,$htmlName)
    {
        $word = new \COM("word.application") or die("Unable to instanciate Word");
        $word->Visible = 1;
        $word->Documents->Open($wordName);
        $word->Documents[1]->SaveAs($htmlName,8);
        $word->Quit();
        return true;
    }*/

    public static function pdf2png($pdf,$path,$page=0)
    {
        if(!is_dir($path))
        {
            mkdir($path,true);
        }
        if(!extension_loaded('imagick'))
        {
            echo '没有找到imagick！' ;
            return false;
        }
        if(!file_exists($pdf))
        {
            echo '没有找到pdf' ;
            return false;
        }
        $im = new \Imagick();
        $im->setResolution(227,150);   //设置图像分辨率
        $im->setCompressionQuality(60); //压缩比
        $im->readImage($pdf."[".$page."]"); //设置读取pdf的第一页
        //$im->thumbnailImage(200, 100, true); // 改变图像的大小
        $im->scaleImage(500,150,true); //缩放大小图像
        $imageName = time().'.png';
        $filename = $path."\\". $imageName;
        if($im->writeImage($filename) == true)
        {
            return $imageName;
        }else {
            return null;
        }
    }

    public static function getFileType($fileName){
        $image = [
            'jpg' => 1,
            'jpeg' => 1,
            'png' => 1,
            'gif' => 1,
            'svg' => 1,
            'dxf' => 1,
            'mp4' => 2,
            'rm' => 2,
            'rmvb' => 2,
            'wmv' => 2,
            'avi' => 2,
            '3gp' => 2,
            'mkv' => 2,
            'doc' => 3,
            'docx' => 3,
            'xls' => 3,
            'xlsx' => 3,
            'ppt' => 3,
            'pptx' => 3,
            'pdf' => 4,
        ];

        $type = strtolower(pathinfo($fileName)['extension']);
        if(array_key_exists($type,$image)){
            $fileType = $image[$type];
            return $fileType;
        }else{
            return false;
        }

    }

    /**批量添加用户
     *
     */
    public static function insertUsers()
    {
        $phpExcel = new \PHPExcel();
        $filePath = 'F:\222.xlsx';
        $PHPReader = new \PHPExcel_Reader_Excel2007(); // Reader很关键，用来读excel文件
        $PHPExcel = $PHPReader->load($filePath);
        $objWorksheet = $PHPExcel->getActiveSheet();
        $highestRow = intval($objWorksheet->getHighestRow());
        /*获得EXCEL宽*/
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
        //组装所有数据
        $strs = [];
        for($roo = 2;$roo <= $highestRow; $roo ++) {
            //注意highestColumnIndex的列数索引从1开始
            for ($col = 1; $col < intval($highestColumnIndex); $col++) {
                    $strs[$roo][$col] = $objWorksheet->getCellByColumnAndRow($col, $roo)->getValue();
                    if (is_object($strs[$roo][$col])) {
                        $strs[$roo][$col] = $strs[$roo][$col]->__toString();
                    }
            }
        }
        foreach ($strs as &$str) {
            $str[1] = addslashes($str[1]);
            $str[2] = Yii::$app->getSecurity()->generatePasswordHash($str[2]);
            $str[3] = addslashes($str[3]);
            $str[4] = Yii::$app->security->generateRandomString();
        }
        \Yii::$app->db2->createCommand()->batchInsert(User::tableName(), ['username','password','realName','access_token'], $strs)->execute();
        /*\Yii::$app->db2->createCommand()->batchInsert(UserAgency::tableName(), ['userId','agencyId'], $arr)->execute();*/
        return true;
    }

    /**
     * 读取机构的excel
     */
    public static function readAgency()
    {
        $phpExcel = new \PHPExcel();
        $filePath = 'F:\1111.xlsx';
        $PHPReader = new \PHPExcel_Reader_Excel2007(); // Reader很关键，用来读excel文件
        $PHPExcel = $PHPReader->load($filePath);
        $objWorksheet = $PHPExcel->getActiveSheet();
        $highestRow = intval($objWorksheet->getHighestRow());
        /*获得EXCEL宽*/
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
        //组装所有数据
        $strs = [];
        for($roo = 2;$roo <= $highestRow; $roo ++) {
            //注意highestColumnIndex的列数索引从1开始
            for ($col = 1; $col < intval($highestColumnIndex) - 1; $col++) {

                $strs[$roo][$col] = $objWorksheet->getCellByColumnAndRow($col, $roo)->getValue();
                if (is_object($strs[$roo][$col])) {
                    $strs[$roo][$col] = $strs[$roo][$col]->__toString();
                }
            }
        }
        $maxId = 0;
        $newData = [];
        $newD = [];
        foreach ($strs as &$str) {
            //记录最大机构ID
            if($str[12] > $maxId) $maxId = $str[12];
            $str[9] = \PHPExcel_Shared_Date::ExcelToPHP($str[9]);
            $str[10] = \PHPExcel_Shared_Date::ExcelToPHP($str[10]);
            foreach ($strs as &$s) {
                if($str[1] == $s[1]) {
                    if($str[11]) $s[11] = $str[11];
                    if($str[12]) $s[12] = $str[12];
                }
            }
            if(!$str[12]) {
                $newData[] = $str[1];
            }
        }
        $newData = array_unique($newData);
//        var_dump($newData);exit;
        foreach ($newData as $new) {
            $newD[] = ['agencyId' => $maxId + 1,'name' => $new];
            $maxId ++;
        }
//        var_dump($newD);exit;
        foreach ($strs as &$str) {
            //没有机构ID
            if(!$str[12]) {
                foreach ($newD as $d) {
                    if($str[1] == $d['name']) {
                        $str[12] = $d['agencyId'];
                    }
                }
            }
        }
        $strs = serialize($strs);
        file_put_contents('F:\111.txt',$strs);
        echo 'done';exit;
    }

    /**
     * 机构导入
     */
    public static function agencyRuku()
    {
        $array = file_get_contents('F:\111.txt');
        $array = unserialize($array);
//        var_dump(count($array));exit;
//        $array = array_slice($array,6658);
//        var_dump(count($array));exit;
        $data = [];
        foreach ($array as $arr) {
            if(!in_array($arr[1],$data)) {
                $data[$arr[1]] = [$arr[1],$arr[2],$arr[3],$arr[4],$arr[9],$arr[10],$arr[11],$arr[12]];
            }
            /*//保存ip段位
            $ip = new HawkAgencyIp();
            $ip->agencyId = intval($arr[12]);
            $ip->startingIP = $arr[5];
            $ip->endingIp = $arr[6];
            $ip->startingIPInt = $arr[7];
            $ip->endingIpInt = $arr[8];

            $ip->save();*/
        }
//        var_dump($data);exit;
        /*foreach ($data as $d) {
            $agency = new HawkAgency();
            $agency->name = $d[0];
            $agency->depart = $d[1];
            $agency->city = $d[3];
            $agency->area = $d[2];
            $agency->fromUserName = $d[6] ?? '无';
            $agency->startTime = $d[4];
            $agency->endTime = $d[5];
            $agency->id = intval($d[7]);
            $agency->otherAgencyId = intval($d[7]);
            $agency->save();
        }*/
        ECHO 'DONE';
    }
}