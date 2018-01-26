<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/7/25
 * Time: 14:39
 */

namespace api\modules\v1\frontend\controllers;


use api\controllers\BaseAPIAuthController;
use api\models\Activity;
use api\models\ActivityOrganization;
use api\models\ActivityTime;
use api\models\Clicks;
use api\models\FilesInfo;
use api\models\Share;
use api\models\Signup;
use api\models\User;
use api\models\UserAgency;
use api\models\Works;
use api\models\Hits;
use api\models\UserHitsShare;
use common\helpers\File;
use common\helpers\Helper;
/**
 * 作品控制器
 * Class worksController
 * @package api\modules\v1\frontend\controllers
 */
class WorksController extends BaseAPIAuthController
{

    private $page = 1;
    private $length = 8;
    /**获取文件下载数据
     * @return array
     */
    public function actionFileProgress()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $clientName = $request->get('clientName');
        $extend = pathinfo($clientName);
        $extend = strtolower($extend [ "extension" ]);
        if($extend != 'zip') {
            return Helper::formatJson(1005,'文件格式不正确');
        }
        $activityId = intval($request->get('activityId'));
        $userId = intval($request->get('userId'));
        $totalSize = $request->get('totalSize');
        $lastModifiedTime = $request->get('lastModifiedTime');
        if(!$activityId || !$userId || !$clientName || !$totalSize || !$lastModifiedTime) {
            return Helper::formatJson(1001,'缺少参数');
        }
        //查询是否有文件上传
        $data = $this->actionCheckUpload($activityId,$userId,$clientName,$totalSize,$lastModifiedTime);
        if(!empty($data)) {
            //已经上传过不允许上传
            return Helper::formatJson(1006,'只能上传一次作品',$data);
        }
        $file = FilesInfo::find()
            ->select('uploadSize')
            ->where([
                'activityId' => $activityId,
                'userId' => $userId,
                'clientFileName' => $clientName,
                'lastModifiedTime' => $lastModifiedTime,
                'totalSize' => $totalSize,
            ])
            ->one();
        $upload = 0;
        if($file) {
            if($file->status == FILE_STATUS['finish']) {
                return Helper::formatJson(1006,'该文件已上传');
            }
            $upload = intval($file->uploadSize);
        }
        return Helper::formatJson(200,'OK',$upload);
    }


    /**文件上传
     * @return array
     */
    public function actionWorkFileUpload()
    {
        $request = \Yii::$app->request;
        if(!$request->isPost) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $data = $request->post();
//        return Helper::formatJson(200,$data);
        if(!empty($_FILES['file'])) {
            //文件名
            $fileName = iconv('utf-8', 'gbk', $data['file']);
            //文件总大小
            $totalSize = $data['totalSize'];
            //文件最后一次修改时间
            $lastModifiedTime = $data['lastModifiedDate'];
            $activityId = intval($data['activityId']);
            $userId = intval($data['userId']);
            if(!$activityId || !$userId || !$totalSize || !$lastModifiedTime) {
                return Helper::formatJson(1001,'缺少参数');
            }
            /*$activityId = 1;
            $userId = 20;*/
            $serverName = $totalSize + $lastModifiedTime;
            $src = '\activity_'.$activityId.'\user_' . $userId;
            $fileObj = new File($fileName, 'file',$src,$serverName);
            $result = $fileObj->saveFile();
//            return Helper::formatJson(888,$res);
            if($result['status']) {
                return Helper::formatJson(200,$result['message'],$result['data']);
            }else {
                return Helper::formatJson(1007,$result['message']);
            }
        }
    }


    /**查询是否有上传过文件
     * @param $activityId
     * @param $userId
     * @param $clientName
     * @param $totalSize
     * @param $lastModifiedTime
     * @return array
     */
    private function actionCheckUpload($activityId,$userId,$clientName,$totalSize,$lastModifiedTime) {
        $isUpload = FilesInfo::find()
            ->where(['activityId' => $activityId,'userId' => $userId,'status' => FILE_STATUS['finish']])
            ->one();
        //文件已经上传完成
        if($isUpload) {
            if($isUpload->clientFileName != $clientName || $isUpload->lastModifiedTime != $lastModifiedTime || $isUpload->totalSize != $totalSize) {
                $data = ['clientFileName' => $isUpload->clientFileName,'totalSize' => $isUpload->totalSize,'lastModifiedTime' => $isUpload->lastModifiedTime];
                //已经上传过返回已上传文件信息
                return $data;
            }
        }
        return null;
    }

    public function actionSaveFileProgress()
    {
        $request = \Yii::$app->request;
        if(!$request->isPost) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $clientName = $request->post('clientName');
        $extend = pathinfo($clientName);
        $extend = strtolower($extend [ "extension" ]);
        if($extend != 'zip') {
            return Helper::formatJson(1005,'文件格式不正确');
        }
        $activityId = intval($request->post('activityId'));
        $userId = intval($request->post('userId'));
        $totalSize = $request->post('totalSize');
        $lastModifiedTime = $request->post('lastModifiedTime');
        $uploadSize = intval($request->post('uploadSize'));
        if(!$activityId || !$userId || !$clientName || !$totalSize || !$lastModifiedTime || !$uploadSize) {
            return Helper::formatJson(1001,'缺少参数');
        }
        //查询是否有文件上传
        $data = $this->actionCheckUpload($activityId,$userId,$clientName,$totalSize,$lastModifiedTime);
        if(!empty($data)) {
            //已经上传过不允许上传
            return Helper::formatJson(1006,'只能上传一次作品',$data);
        }
        $serverName = ($totalSize + $lastModifiedTime).'.zip';
        //文件上传成功记录数据
        $info = [
            'activityId' => $activityId,
            'userId' => $userId,
            'clientFileName' => $clientName,
            'lastModifiedTime' => $lastModifiedTime,
            'totalSize' => $totalSize,
            'serverFileName' => $serverName,
            'uploadSize' => $uploadSize
        ];
        $res = FilesInfo::saveInfo($info);
        if($res) {
            return Helper::formatJson(200,'记录成功');
        }else {
            return Helper::formatJson(1007,'记录失败');
        }
    }

    /**作品回显
     * @return array
     */
    public function actionUserWork()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $activityId = intval($request->get('activityId'));
        $userId = intval($request->get('userId'));
        if(!$activityId || !$userId) {
            return Helper::formatJson(1001,'缺少参数');
        }
        $work = Works::getWorkInfo($activityId,$userId);
        return Helper::formatJson(200,'OK',$work);
    }


    /**删除文件
     * @return array
     */
    public function actionRemoveFile()
    {
        $request = \Yii::$app->request;
        if(!$request->isPost) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $activityId = intval($request->post('activityId'));
        $userId = intval($request->post('userId'));
        $totalSize = $request->post('totalSize');
        $lastModifiedTime = $request->post('lastModifiedTime');
        if(!$activityId || !$userId || !$totalSize || !$lastModifiedTime) {
            return Helper::formatJson(1001,'缺少参数');
        }
        $serverName = $totalSize + $lastModifiedTime.'.zip';
        $file = \Yii::$app->basePath.'\web\data\zip\activity_'.$activityId.'\user_' . $userId.'\\'.$serverName;
        if(file_exists($file)) {
            if(@unlink($file)) {
                //删除文件成功后删除数据库数据
                $data = FilesInfo::find()
                    ->where([
                        'activityId' => $activityId,
                        'userId' => $userId,
                        'totalSize' => $totalSize,
                        'lastModifiedTime' => $lastModifiedTime,
                    ])
                    ->one();
                if($data) {
                    $data->delete();
                }
                return Helper::formatJson(200,'删除成功');
            }else {
                return Helper::formatJson(1007,'删除失败');
            }
        }else {
            return Helper::formatJson(1004,'文件不存在');
        }
    }
    
    
    /**
     * 添加作品
     */
    public function actionAddWork(){
        $request = \Yii::$app->request;
        if(!$request->isPost) {
            return Helper::formatJson(1003,'请求方式不正确');
        }
        $data = $request->post();
        $activityId = intval($data['activityId']);
        $userId = intval($data['userId']);
        if(!ActivityOrganization::juadeMatch($activityId,$userId)) {
                return Helper::formatJson(1005,'该活动不属于用户所属学校');
            }
        $activity = Activity::findOne($activityId);
        if(!$activity) {
            return Helper::formatJson(1004,'活动不存在');
        }
        if($activity->activityEndTime < time() || $activity->status == ACTIVITY_STATUS['end']) {
            return Helper::formatJson(1006,'活动已结束');
        }
        //判断用户是否绑定邮箱
        $user = User::findOne(['id' => $userId]);
        if(!$user) return Helper::formatJson(1004,'用户不存在');
        if(!$user->phone) return Helper::formatJson(1008,'该用户未绑定手机号');
        //查询是否有文件上传
        $isUpload = FilesInfo::find()
                ->where(['activityId' => $activityId,'userId' => $userId,'clientFileName' => $data['file'],'lastModifiedTime' => $data['lastModifiedTime']])
                ->one();
        //判断是否有作品上传
        if(!$isUpload) {
            return Helper::formatJson(1004,'请先上传作品');
        }
//        $uploadSize = filesize($baseUrl.$fileUrl);
        $transaction = \Yii::$app->db->beginTransaction();
        if($isUpload->status == FILE_STATUS['finish']) {
            $serverName = $isUpload->serverFileName;
            $fileUrl = '\data\zip\activity_'.$activityId.'\user_' . $userId.'\\'.$serverName;
            //判断服务器上的zip文件是否能打开
            $filePath = \Yii::$app->basePath.'\web'.$fileUrl;
            $canOpen = zip_open($filePath);
            if(!is_resource($canOpen)) {
                return Helper::formatJson(1005,'ZIP文件不完整');
            }else {
                zip_close($canOpen);
            }
            $data['workSite'] = $fileUrl;
            $data['status'] = WORK_STATUS['init'];
            //文件上传完成修改参赛状态
            $isSignUp = Signup::find()
                ->where(['userId' => $userId,'activityId' => $activityId])
                ->one();
            if($isSignUp && $isSignUp->status == SIGN_STATUS['init']) {
                Signup::updateAll(['status' => SIGN_STATUS['match']],['userId' => $userId,'activityId' => $activityId]);
            }
        }
        //保存作品
        $work = Works::saveInfo($data);
        if(!$work['result']) {
            $transaction->rollBack();
            return Helper::formatJson(1007,'参赛失败',$work['message']);
        }
        $transaction->commit();
        return Helper::formatJson(200,'参赛成功',$work['message']);
    }


    /**添加作品分类下拉框数据
     * @return array
     */
    public function actionTypeLists()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1006,'请求方式不正确');
        }
        $activityId = intval($request->get('activityId'));
        if(!$activityId) {
            return Helper::formatJson(1001,'缺少参数');
        }
        $lists = ActivityTime::find()
            ->select(['id','type'])
            ->where(['activityId' => $activityId])
            ->andWhere(['<','signStartTime',time()])
            ->andWhere(['>','signEndTime',time()])
            ->orderBy('sn ASC')
            ->all();
        return Helper::formatJson(200,'OK',$lists);
    }

    /**前台作品列表
     * @return array
     */
    public function actionLists()
    {
        $request = \Yii::$app->request;
        if($request->isGet) {
            $userId = $request->get('userId',0);
            $page = $request->get('page') ?? $this->page;
            $length = $request->get('length') ?? $this->length;
            $activityId = $request->get('activityId',0);
            $isMine = $request->get('isMine',0);
            $data = Works::getWorksLists($page,$activityId,$length,$userId,$isMine);
            return Helper::formatJson(200,'OK',$data);
        }else {
            return Helper::formatJson(1003,'请求错误');
        }
    }


    /**返回个人已提交待审核
     * @return array
     */
    public function actionGetAllUploadFiles()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $userId = $request->get('userId');
        $page = $request->get('page',$this->page);
        $length = $request->get('length',$this->length);
        if(!$userId || !$page || !$length) {
            return Helper::formatJson(1001, '缺少参数');
        }
        $query = Works::find()->alias('w')
                ->select(['w.id','w.status','f.clientFileName','f.totalSize','a.name'])
                ->leftJoin('cs_files_info as f','f.activityId = w.activityId AND f.userId = w.userId')
                ->leftJoin('cs_activity as a','a.id = w.activityId')
                ->where(['w.userId' => $userId])
                ->andWhere(['in','w.status',[2,3]]); //查询待审核和未通过的作品
        $count = $query->count();
        $index = ($page - 1) * $length;
        $lists = $query
            ->offset(intval($index))
            ->limit($length)
            ->asArray()
            ->all();
        $data = [
            'lists' => $lists,
            'count' => $count,
            'totalPage' => ceil($count / $length),
            'nowPage' => $page
        ];
        return Helper::formatJson(200,'OK',$data);
    }


    /**作品点赞
     * @return array
     */
    public function actionAddHits()
    {
        $request = \Yii::$app->request;
        if(!$request->isPost) {
            return Helper::formatJson(1003,'请求错误');
        }
        $userId = intval($request->post('userId'));
        $workId = intval($request->post('workId'));
        if (empty($workId) || empty($userId)) {
            return Helper::formatJson(1001, '缺少参数');
        }
        $result = UserHitsShare::find()
            ->where(['primaryId' => $workId, 'userId' => $userId,'type' => HITS_TYPE['work']])
            ->one();
        if($result) {
            return Helper::formatJson(1006, '不能重复点赞');
        }
        $result = Hits::find()->where(['primaryId' => $workId, 'type' => HITS_TYPE['work']])->one();
        if (!$result) {
            return Helper::formatJson(1004, '作品不存在,请确认');
        }
        $result->hits += 1;
        if ($result->save()) {
            /*记录点赞用户ID*/
            $userHits = new UserHitsShare();
            $userHits->primaryId = $result->primaryId;
            $userHits->userId = $userId;
            if($userHits->save()) {
                return Helper::formatJson(200, '点赞成功');
            }
        } else {
            return Helper::formatJson(1007, '点赞失败');
        }
    }

    /**作品分享
     * @return array
     */
    public function actionAddShare()
    {
        $request = \Yii::$app->request;
        if(!$request->isPost) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $userId = intval($request->post('userId'));
        $workId = intval($request->post('workId'));
        if (empty($workId) || empty($userId)) {
            return Helper::formatJson(1001, '缺少参数');
        }
        $result = Share::find()->where(['primaryId' => $workId, 'type' => HITS_TYPE['work']])->one();
        if (!$result) {
            return Helper::formatJson(1004, '作品不存在,请确认');
        }
        $result->share += 1;
        if ($result->save()) {
            /*记录分享用户ID*/
            $userShare = new UserHitsShare();
            $userShare->primaryId = $result->id;
            $userShare->userId = $userId;
            if($userShare->save()) {
                return Helper::formatJson(200, '分享成功');
            }
        } else {
            return Helper::formatJson(1007, '分享失败');
        }
    }


    /**作品排名
     * @return array
     */
    public function actionGetRank()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $activityId = $request->get('activityId');
        if(!$activityId) {
            return Helper::formatJson(1001,'缺少参数');
        }
        $length = $request->get('length') ?? $this->length;
        $rank = Works::getWorksRank($activityId,$length);
        return Helper::formatJson(200,'OK',$rank);
    }

    /**获取作品详情
     * @return array
     */
    public function actionGetInfo()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $workId = $request->get('workId');
        if(!$workId) {
            return Helper::formatJson(1001,'缺少参数');
        }
        $userId = $request->get('userId');
        $data = Works::getDetail($workId,$userId);
        return Helper::formatJson(200,'OK',$data);
    }

    public function actionCanMatch()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1006,'请求方式不正确');
        }
        $activityId = intval($request->get('activityId'));
        if(!$activityId) {
            return Helper::formatJson(1001,'缺少参数');
        }
        $userId = $request->get('userId',0);
        if($userId) {
            $userId = intval($userId);
           /* //判断用户
            $user = User::findOne(['id' => intval($userId)]);
            if(!$user->phone) return Helper::formatJson(1008,'该用户未绑定手机号');*/
            if(!ActivityOrganization::juadeMatch($activityId,$userId)) {
                return Helper::formatJson(1005,'该活动不属于用户所属学校');
            }
        }
        $lists = ActivityTime::find()
            ->select(['id'])
            ->where(['activityId' => $activityId])
            ->andWhere(['>','reviewStartTime',time()])
            ->all();
        if(is_array($lists) && $lists) {
            return Helper::formatJson(200,'OK');
        }else {
            return Helper::formatJson(1005,'不能参赛');
        }
    }

    /**记录作品点击率
     * @return array
     */
    public function actionRecordClick()
    {
        $request = \Yii::$app->request;
        if(!$request->isPost) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $fileId = $request->post('fileId',0);
        $file_type = $request->post('type',0);
        if(!$fileId || !$file_type) {
            return Helper::formatJson(1001,'缺少参数');
        }
        $type = 0;
        if($file_type == FILE_TYPE['pic_file']) {
            $type = CLICK_TYPE['pic_click'];
        }elseif ($file_type == FILE_TYPE['video_file']) {
            $type = CLICK_TYPE['video_click'];
        }elseif ($file_type == FILE_TYPE['other_file']) {
            $type = CLICK_TYPE['other_click'];
        }
        if(!Clicks::saveInfo($fileId,$type)) {
            return Helper::formatJson(1007,'记录点击数据失败');
        }
        return Helper::formatJson(200,'记录成功');
    }
    public function actionSso(){
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $userId = $request->get('userId');
        $accessToken = $request->get('accessToken');
        setcookie("zxjq_userId",$userId,time()+3600,"/","jqweike.com");
        setcookie("zxjq_accessToken",$accessToken,time()+3600,"/","jqweike.com");
        return Helper::formatJson(200);
    }
}