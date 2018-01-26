<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/8/2
 * Time: 15:02
 */

namespace api\modules\v1\frontend\controllers;


use api\controllers\BaseAPIAuthController;
use api\models\UserScore;
use common\helpers\Helper;

class UserScoreController extends BaseAPIAuthController
{

    /**用户抽奖列表
     * @return array
     */
    public function actionLists()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $lists = UserScore::find()
            ->select(['id','username','score'])
            ->orderBy('getTime DESC')
            ->all();
        foreach ($lists as & $list) {
            //用户名只显示第一位和倒数两位中间全为*
            $list['username'] = $this->actionSubstrCut($list['username']);
        }
        return Helper::formatJson(200,'OK',$lists);
    }

    private function actionSubstrCut($username){
        $strlen = mb_strlen($username, 'utf-8');
        $firstStr = mb_substr($username, 0, 1, 'utf-8');
        $lastStr = mb_substr($username, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($username, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }
}