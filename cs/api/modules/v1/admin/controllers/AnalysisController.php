<?php

namespace api\modules\v1\admin\controllers;

use api\controllers\BaseAPIAuthController;
use api\models\Clicks;
use api\models\Works;
use common\helpers\Helper;
use Yii;

class AnalysisController extends BaseAPIAuthController
{

    public function actions()
    {
        return [];
    }

    /**
     * 作品分析
     * @param $type
     * @param $gran
     */
    public function actionWorks($type = 1, $gran = 'today')
    {
        switch ($gran){
            case 'today':
                $startTime = strtotime(date('Y-m-d'));
                $endTime = strtotime(date('Y-m-d', strtotime("+1 day")));
                break;
            case 'yesterday' :
                $startTime = strtotime(date('Y-m-d', strtotime("-1 day")));
                $endTime = strtotime(date('Y-m-d'));
                break;
            case 'week' :
                $startTime = strtotime(date('Y-m-d', strtotime("-6 day")));
                $endTime = strtotime(date('Y-m-d', strtotime("+1 day")));
                break;
            case 'month' :
                $startTime = strtotime(date('Y-m-d', strtotime("-30 day")));
                $endTime = strtotime(date('Y-m-d', strtotime("+1 day")));
                break;
            default:
                $startTime = strtotime(date('Y-m-d'));
                $endTime = strtotime(date('Y-m-d', strtotime("+1 day")));
                break;
        }

        $workModel = new Works();
        $data = $workModel->analysisWorks($type, $startTime, $endTime);

        return Helper::formatJson(200, 'Ok', $data);
    }

    /**
     * 作品点击量
     * @param $gran
     */
    public function actionClick($gran = 'today')
    {
        switch ($gran){
            case 'today':
                $startTime = strtotime(date('Y-m-d'));
                $endTime = strtotime(date('Y-m-d', strtotime("+1 day")));
                break;
            case 'yesterday' :
                $startTime = strtotime(date('Y-m-d', strtotime("-1 day")));
                $endTime = strtotime(date('Y-m-d'));
                break;
            case 'week' :
                $startTime = strtotime(date('Y-m-d', strtotime("-6 day")));
                $endTime = strtotime(date('Y-m-d', strtotime("+1 day")));
                break;
            case 'month' :
                $startTime = strtotime(date('Y-m-d', strtotime("-30 day")));
                $endTime = strtotime(date('Y-m-d', strtotime("+1 day")));
                break;
            default:
                $startTime = strtotime(date('Y-m-d'));
                $endTime = strtotime(date('Y-m-d', strtotime("+1 day")));
                break;
        }

        $model = new Clicks();
        $data = $model->getWorksClick($gran, $startTime, $endTime);

        return Helper::formatJson(200, 'Ok', $data);
    }

}