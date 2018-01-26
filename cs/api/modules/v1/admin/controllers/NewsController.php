<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/7/24
 * Time: 17:29
 */

namespace api\modules\v1\admin\controllers;


use api\controllers\BaseAPIAuthController;
use api\models\Activity;
use api\models\News;
use common\helpers\Helper;

/**
 * Class NewsController
 * @package api\modules\v1\admin\controllers
 */
class NewsController extends BaseAPIAuthController
{
    /**
     * 新闻添加
     */
    public function actionNewsAdd(){
        if(\Yii::$app->request->isPost){
            $news = new News();
            $news->setScenario('create');
            if($news->load(\Yii::$app->request->post(),'') && $news->validate()) {
                $news->publishTime = time();
                $res = $news->save();
                if($res){
                    return Helper::formatJson(200);
                }else{
                    return Helper::formatJson(1002);
                }
            }else{
                return Helper::formatJson(1002,'数据有误');
            }
        }else{
            return Helper::formatJson(1003,'请求方式不正确');
        }
    }

    /**
     * 新闻的删除
     */
    public function actionNewsDelete(){
        if(\Yii::$app->request->isPost){
            $news = new News();
            $news->setScenario('delete');
            if($newsId = \Yii::$app->request->post('newsId')) {
                if(is_array($newsId)){
                    $newsId = implode(',',$newsId);
                }else{
                    return Helper::formatJson(1002,'数据有误');
                }
                $res = $news->deleteAll('id in('.$newsId.')');
                if($res){
                    return Helper::formatJson(200);
                }else{
                    return Helper::formatJson(1002);
                }
            }else{
                return Helper::formatJson(1002,'数据有误');
            }
        }else{
            return Helper::formatJson(1003,'请求方式不正确');
        }
    }

    /**
     * 新闻的修改
     */
    public function actionModifyNews(){
        if(\Yii::$app->request->isPost){
            $news = new News();

            $news->setScenario('update');

            $newsId = \Yii::$app->request->post('newsId') ?? '';
            if(empty($newsId)){
                return Helper::formatJson(1003);
            }

            $news->setOldAttribute('id',$newsId);
            //$news->publishTime = time();
            if($news->load(\Yii::$app->request->post(),'') && $news->validate()) {
                $res = $news->save();
                if($res){
                    return Helper::formatJson(200);
                }else{
                    return Helper::formatJson(1002);
                }
            }else{
                return Helper::formatJson(1002,'数据有误');
            }
        }else{
            return Helper::formatJson(1003,'请求方式不正确');
        }
    }

    /**
     * 活动查看
     */
    public function actionViewNews(){

        /*当前页码*/
        $currentPage = \Yii::$app->request->post('page') ?? 1;

        /*每页显示数量*/
        $pageSize = \Yii::$app->request->post('pageSize') ?? '10';

        //条件
        $condition  = \Yii::$app->request->post('condition') ?? '1=1';
        $titleLike = '';
        if(is_array($condition)){
            foreach($condition as $key=>$co){
                if(empty($co)){
                    unset($condition[$key]);
                }
                if($key == 'title' ){
                    if(!empty($co) || $co =='0'){
                        unset($condition['title']);
                        $titleLike = $co;
                    }

                }

                if($key=='userId'){
                    //判断角色
                    $auth = \Yii::$app->authManager;
                    $role = $auth->getRolesByUser($co);

                    if(!isset($role[SUPERADMIN])){
                      $condition['a.userId'] = $condition['userId'];
                    }
                    unset($condition['userId']);
                }

            }
        }else{
            return Helper::formatJson(1002);
        }
        $activityId = \Yii::$app->request->post('activityId') ?? '1=1';
        $title = \Yii::$app->request->post('title') ?? '';


        /*case when activityEndTime>'.time().' then 未开始 else 已结束*/
        $news = News::find()
            ->from(News::tableName(). 'as a')
            ->leftJoin(Activity::tableName() . ' as b', 'b.id = a.activityId')
            ->select(['a.id','a.userId','a.username','a.activityId','b.name',
                'a.title', "FROM_UNIXTIME(a.publishTime,'%Y-%m-%d') as publishTime","a.publishTime as or"
                ])
            ->orderBy('or DESC');


        //如果title条件不为空
        if(empty($titleLike) && $titleLike!='0'){
            $co = $news->where($condition);
        }else{
            $co = $news

                ->where($condition)
                ->andWhere(['like','title',$titleLike]);
        }
        /*总条数*/
            $count = $co->count();



        /*计算偏移量*/
        $offset = ($currentPage-1) * $pageSize;
        /*分页数据*/
        $data = $news
            ->offset($offset)
            ->limit($pageSize)
            ->asArray()
            ->all();
        //echo $news->createCommand()->getRawSql();exit;
        $total['total'] = $count;
        $total['rows'] = $data;
        return Helper::formatJson(200,'',$total);

    }

    public function actionGetNewsById(){
        if(\Yii::$app->request->isGet){
            $newsId = \Yii::$app->request->get('newsId') ?? '';
            if(!empty($newsId)){
                $news = News::find()
                    ->from(News::tableName(). 'as a')
                    ->leftJoin(Activity::tableName() . ' as b', 'b.id = a.activityId')
                    ->select(['a.id','a.userId','a.username','a.activityId','b.name',
                        'a.newsSummary','a.coverPath','a.mainBody','a.status','a.headImage',
                        'a.title', "FROM_UNIXTIME(a.publishTime,'%Y-%m-%d') as publishTime"
                    ])
                    ->where('a.id = :id',[':id'=>$newsId])
                    ->asArray()
                    ->orderBy('publishTime DESC')
                    ->one();

                return Helper::formatJson(200,'',$news);
            }else{
                return Helper::formatJson(1002);
            }

        }else{
            return Helper::formatJson(1003);
        }
    }

}