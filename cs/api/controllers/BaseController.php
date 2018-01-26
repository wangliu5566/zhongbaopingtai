<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/6/21
 * Time: 11:41
 */

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use yii\web\Response;

class BaseController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

	public function behaviors()
    {
        $behaviors = ArrayHelper::merge([
            [
                'class' => Cors::className(),
                /*'cors' => [
                    'Access-Control-Allow-Origin' => '*',
                ],*/
            ],

        ], parent::behaviors());

        $behaviors['contentNegotiator']['formats'] = [
            'application/json' => Yii::$app->request->get('format') ?? Response::FORMAT_JSON,
        ];
        return $behaviors;
    }

    public function init()
    {   
        /*POST方式请求数据*/
        if(Yii::$app->request->isPost){
            $params = Yii::$app->request->bodyParams;
            $sign = Yii::$app->request->getBodyParam('sign');
        }
    	
        /*GET方式请求数据*/
        if(Yii::$app->request->isGet){
            $paramStr = Yii::$app->request->queryString;
            $sign = Yii::$app->request->get('sign');

            parse_str($paramStr, $params);
        }

        /*验证请求的合法性*/
        /*if(!$sign){
            echo json_encode(array(
                'status' => 400,
                'message' => '非法请求',
                'data' => array()
            ));
            die;
        }*/

        /*token 验证*/
        /*$secret = SECRETS['default'];
        unset($params['sign']);

        $paramStr = '';
        ksort($params);
        if($params){
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
        }

        $paramStr .= $secret['default'];
        $authToken = sha1($paramStr);
        
        if(strncasecmp($authToken, $sign, 40) !== 0){
            echo json_encode(array(
                'status' => 400,
                'message' => '非法请求',
                'data' => array()
            ));
            die;
        }*/
    }




}