<?php

namespace api\models;

use Yii;
use yii\tongji\LoginService;
use yii\tongji\ReportService;

class Data extends \yii\base\Model
{	
	private $login_url = 'https://api.baidu.com/sem/common/HolmesLoginService';

	private $api_url = 'https://api.baidu.com/json/tongji/v1/ReportService';

	/**
	 * 获取百度接口数据
	 * @return [type] [description]
	 */
	public function getList($start_date, $end_date, $gran = 'hour', $clientDevice = '')
	{
		$info = Yii::$app->cache->get('tongji_user');
		if(!$info){
			$info = $this->doLogin();
			Yii::$app->cache->set('tongji_user', $info, 3600 * 24);
		}
		
		if($info){
			$reportService = new ReportService($this->api_url, TONGJI['username'], TONGJI['token'], $info['ucid'], $info['st'], TONGJI['uuid'], TONGJI['account_type']);
			
			/*获取网站siteId*/
			/*$ret = $reportService->getSiteList();
			$siteList = $ret['body']['data'][0]['list'];
			if (count($siteList) > 0) {
    			$siteId = $siteList[0]['site_id'];
    		}*/
    		$siteId = TONGJI['site_id'];
    		$params = array(
		        'site_id' => $siteId,                   
		        'method' => 'trend/time/a',             
		        'start_date' => $start_date,            
		        'end_date' => $end_date,                
		        'metrics' => 'pv_count,visitor_count',  
		        'max_results' => 0,                    
		        'gran' => $gran,                        
		    );

    		if($clientDevice){
    			$params['clientDevice'] = $clientDevice;
    		}
    		$response = $reportService->getData($params);

    		if(!$response['raw']){
    			return $this->repeat($start_date, $end_date, $gran, $clientDevice);
    		}

    		return $response['raw'];
		}

		return false;
	}

	/**
	 * 获取数据失败再次请求
	 * @param  [type] $start_date   [description]
	 * @param  [type] $end_date     [description]
	 * @param  string $gran         [description]
	 * @param  string $clientDevice [description]
	 * @return [type]               [description]
	 */
	public function repeat($start_date, $end_date, $gran = 'hour', $clientDevice = '')
	{
		$info = $this->doLogin();
		Yii::$app->cache->set('tongji_user', $info, 3600 * 24);
		
		if($info){
			$reportService = new ReportService($this->api_url, TONGJI['username'], TONGJI['token'], $info['ucid'], $info['st'], TONGJI['uuid'], TONGJI['account_type']);
			
			/*获取网站siteId*/
			/*$ret = $reportService->getSiteList();
			$siteList = $ret['body']['data'][0]['list'];
			if (count($siteList) > 0) {
    			$siteId = $siteList[0]['site_id'];
    		}*/
    		$siteId = TONGJI['site_id'];
    		$params = array(
		        'site_id' => $siteId,                   
		        'method' => 'trend/time/a',             
		        'start_date' => $start_date,            
		        'end_date' => $end_date,                
		        'metrics' => 'pv_count,visitor_count',  
		        'max_results' => 0,                    
		        'gran' => $gran,                        
		    );

    		if($clientDevice){
    			$params['clientDevice'] = $clientDevice;
    		}
    		$response = $reportService->getData($params);

    		return $response['raw'];
		}

		return false;
	}

	/**
	 * 用户登录
	 * @return [type] [description]
	 */
	public function doLogin()
	{
		$loginService = new LoginService($this->login_url, TONGJI['uuid'], TONGJI['account_type']);
		//$loginService->preLogin(TONGJI['username'], TONGJI['token']);
		$user = $loginService->doLogin(TONGJI['username'], TONGJI['password'], TONGJI['token']);

		if($user){
			return $user;
		}

		return false;
	}

    /**
     * 获取今日流量
     */
	public function getTodayFlow($clientDevice, $type)
    {
        $cache_key = 'today'. '_' . $type . '_' . $clientDevice;
        $row = Yii::$app->cache->get($cache_key);

        if(!$row){
            if($type == 'week'){
                $num = 1;
                $days = 0;
                $gran = 'hour';

                $start_date1 = date('Ymd', strtotime("-1 week"));
                $end_date1 = date('Ymd', strtotime("-1 week"));
            }elseif($type == 'month'){
                $num = 6;
                $days = -7;
                $gran = 'day';

                //$week = date('w') == 0 ? 7 : date('w');

                $start_date1 = date('Ymd', strtotime("-36 day"));
                $end_date1 = date('Ymd', strtotime("-30 day"));
            }elseif($type == 'year'){
                $num = 29;
                $days = -30;
                $gran = 'day';

                $start_date1 = date('Ymd', strtotime("-394 day"));
                $end_date1 = date('Ymd', strtotime("-365 day"));
            }

            $data = array();
            if($type == 'week'){
                $start_date = date('Ymd', strtotime("$days day"));
                $end_date = date('Ymd', strtotime("$days day"));
            }else{
                $start_date = date('Ymd', strtotime( $days+1 . " day"));
                $end_date = date('Ymd', strtotime("+" . ($days + $num + 1) . " day"));
            }
            $result = json_decode($this->getList($start_date, $end_date, $gran, $clientDevice), true);

            if($result){
                $total = $result['body']['data'][0]['result']['sum'][0];
                $data['current']['pv'] = $total[0] == '--' ? 0 : $total[0];
                $data['current']['uv'] = $total[1] == '--' ? 0 : $total[1];
            }

            /*计算日环比*/
            if($type == 'week'){
                $start_date = date('Ymd', strtotime($days - $num . " day"));
                $end_date = date('Ymd', strtotime($days - $num . " day"));
            }else{
                $start_date = date('Ymd', strtotime($days - $num . " day"));
                $end_date = date('Ymd', strtotime($days . " day"));
            }
            $yesterday = json_decode($this->getList($start_date, $end_date, $gran, $clientDevice), true);

            if($yesterday){
                $number = $yesterday['body']['data'][0]['result']['sum'][0];
                $data['last']['pv'] = $number[0] == '--' ? 0 : $number[0];
                $data['last']['uv'] = $number[1] == '--' ? 0 : $number[1];
            }

            if($data['last']['uv']){
                $uvDiff = round(($data['current']['uv'] - $data['last']['uv'])/$data['last']['uv'], 2) * 100;
                $pvDiff = round(($data['current']['pv'] - $data['last']['pv'])/$data['last']['pv'], 2) * 100;
            }else{
                $uvDiff = $data['current']['uv'] * 100;
                $pvDiff = $data['current']['pv'] * 100;
            }

            /*计算周同比*/
            $tongBi = json_decode($this->getList($start_date1, $end_date1, $gran, $clientDevice), true);
            if($tongBi){
                $number = $tongBi['body']['data'][0]['result']['sum'][0];
                $data['tong']['pv'] = $number[0] == '--' ? 0 : $number[0];
                $data['tong']['uv'] = $number[1] == '--' ? 0 : $number[1];
            }

            if($data['tong']['uv']){
                $uvTb = round(($data['current']['uv'] - $data['tong']['uv'])/$data['tong']['uv'], 2) * 100;
                $pvTb = round(($data['current']['pv'] - $data['tong']['pv'])/$data['tong']['pv'], 2) * 100;
            }else{
                $uvTb = $data['current']['uv'] * 100;
                $pvTb = $data['current']['pv'] * 100;
            }

            $point['day']['pv'] = $pvDiff;
            $point['day']['uv'] = $uvDiff;
            $point['week']['pv'] = $pvTb;
            $point['week']['uv'] = $uvTb;

            $row['point'] = $point;
            $row['total']['last'] = $data['last'];
            $row['total']['current'] = $data['current'];

            Yii::$app->cache->set($cache_key, $row, 3600);
        }

        return $row;
    }
}