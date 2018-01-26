<?php

namespace api\modules\v1\admin\controllers;

use Yii;
use api\controllers\BaseAPIAuthController;
use api\models\Data;
use common\helpers\Helper;

class FlowController extends BaseAPIAuthController
{

    public function actions()
    {
        return [];
    }

    /**
     * 获取今日流量
     * @return [type] [description]
     */
    public function actionCurrentDay()
    {
        $gran = Yii::$app->request->get('gran', 'hour');
        $days = Yii::$app->request->get('days', 0);
        $clientDevice = Yii::$app->request->get('deviceType');
        $type = Yii::$app->request->get('type', 'week');
        $num = 0;

        $cache_key = $type . '_' . $gran . '_' . $days . '_' . $clientDevice;
        $data = Yii::$app->cache->get($cache_key);
        if(!$data) {
            if ($gran == 'day') {
                $num = 6;
                if ($days < -6) {
                    $num = 29;
                }
            } elseif ($gran == 'week' || $gran == 'month') {
                $num = 364;
            }

            $start_date = date('Ymd', strtotime("$days day"));
            $end_date = date('Ymd', strtotime("+" . ($days + $num) . " day"));

            $data = array();
            $model = new Data();
            $result = json_decode($model->getList($start_date, $end_date, $gran, $clientDevice), true);

            if ($result) {
                $rows = $result['body']['data'][0]['result']['items'];
                $dataList = $rows[0];
                $pvs = $rows[1];

                foreach ($dataList as $key => $values) {
                    $data['list']['pv'][$key] = $data['table'][$key]['pv'] = $pvs[$key][0] == '--' ? 0 : $pvs[$key][0];
                    $data['list']['uv'][$key] = $data['table'][$key]['uv'] = $pvs[$key][1] == '--' ? 0 : $pvs[$key][1];
                    $data['table'][$key]['time'] = $values[0];
                }

                $data['list']['rows'] = $dataList;
            }

            /*昨日或上月数据*/
            /*$start_date = date('Ymd', strtotime($days - $num . " day"));
            $end_date = date('Ymd', strtotime("$days day"));
            $yesterday = json_decode($model->getList($start_date, $end_date, $gran, $clientDevice), true);

            if($yesterday){
                $number = $yesterday['body']['data'][0]['result']['sum'][0];
                $data['total']['last']['pv'] = $number[0] == '--' ? 0 : $number[0];
                $data['total']['last']['uv'] = $number[1] == '--' ? 0 : $number[1];
            }*/

            /*计算日环比、周同比*/
            /*if($data['total']['last']['uv']){
                $uvDiff = round(($data['total']['current']['uv'] - $data['total']['last']['uv'])/$data['total']['last']['uv'], 2);
                $pvDiff = round(($data['total']['current']['pv'] - $data['total']['last']['pv'])/$data['total']['last']['pv'], 2);
            }else{
                $uvDiff = $data['total']['current']['uv'];
                $pvDiff = $data['total']['current']['pv'];
            }

            if($type == 'week'){
                $start_date = date('Ymd', strtotime("-1 week"));
                $end_date = date('Ymd', strtotime("-1 week +1 day"));
            }elseif($type == 'month'){
                $week = date('w') == 0 ? 7 : date('w');

                $start_date = date('Ymd', strtotime("-1 month " . (-$week+1) . " day"));
                $end_date = date('Ymd', strtotime("-1 month " . (-$week+8) . " day"));
            }elseif($type == 'year'){
                $month = date('m');
                $year = date('Y') - 1;
                $start_date = $year . $month . '01';
                $end_date = $year . ($month + 1) . '01';
            }

            $tongBi = json_decode($model->getList($start_date, $end_date, $gran, $clientDevice), true);
            if($tongBi){
                $number = $tongBi['body']['data'][0]['result']['sum'][0];
                $data['total']['tong']['pv'] = $number[0] == '--' ? 0 : $number[0];
                $data['total']['tong']['uv'] = $number[1] == '--' ? 0 : $number[1];
            }

            if($data['total']['tong']['uv']){
                $uvTb = round(($data['total']['current']['uv'] - $data['total']['tong']['uv'])/$data['total']['tong']['uv'], 2);
                $pvTb = round(($data['total']['current']['pv'] - $data['total']['tong']['pv'])/$data['total']['tong']['pv'], 2);
            }else{
                $uvTb = $data['total']['current']['uv'];
                $pvTb = $data['total']['current']['pv'];
            }

            $data['point']['day']['pv'] = $pvDiff;
            $data['point']['day']['uv'] = $uvDiff;
            $data['point']['week']['pv'] = $pvTb;
            $data['point']['week']['uv'] = $uvTb;*/
            $res = $model->getTodayFlow($clientDevice, $type);
            $data['point'] = $res['point'];
            $data['total'] = $res['total'];

            Yii::$app->cache->set($cache_key, $data, 3600);
        }

        return Helper::formatJson(200, 'Ok', $data);
    }

}