<?php

namespace api\modules\v1\admin\controllers;

use api\models\Area;
use common\helpers\Helper;
use api\controllers\BaseAPIController;
use Yii;

class AreaController extends BaseAPIController
{

    /**
     * 查詢省列表
     */
    public function actionProvince()
    {
        $data = (new Area())->getProvinceList();
        if($data){
            foreach($data as $key => $value){
                $data[$key]['loading'] = false;
                $data[$key]['children'] = array();
            }
        }

        return Helper::formatJson(200, 'Ok', $data);
    }

    /**
     * @param $code 查询城市列表
     */
    public function actionCity()
    {
        $code = Yii::$app->request->get('code', 110000);
        $data = (new Area())->getCityList($code);

        return Helper::formatJson(200, 'Ok', $data);
    }

}