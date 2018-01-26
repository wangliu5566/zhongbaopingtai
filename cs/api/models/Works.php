<?php

namespace api\models;

use Yii;
use yii\db\Query;
use yii\data\Pagination;

/**
 * This is the model class for table "{{%works}}".
 *
 * @property int $id
 * @property int $activityId
 * @property string $workName 作品名称
 * @property string $username 作者名字
 * @property int $tel 手机
 * @property string $departments 所在学校
 * @property string $division 所在院系
 * @property string $studentId 学号
 * @property string $workSite 作品保存路径
 * @property string $coverPath 作品封面
 * @property int $createTime 提交时间
 * @property int $type 作品类型
 * @property int $userId 作者ID
 * @property int $status 作品状态
 */
class Works extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%works}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activityId','workName', 'username','type','userId','tel','departments','studentId','division'], 'required'],
            [['activityId','userId','createTime','status'], 'integer'],
            [['workName'], 'string', 'max' => 50],
            [['username'], 'string', 'max' => 15],
            [['tel'], 'string', 'max' => 11],
            [['departments'], 'string', 'max' => 20],
            [['studentId'], 'string', 'max' => 20],
            [['workSite'], 'string', 'max' => 150],
            [['coverPath'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activityId' => 'Activity Id',
            'userId' => 'User Id',
            'workName' => 'Work Name',
            'username' => 'Username',
            'tel' => 'Tel',
            'departments' => 'Departments',
            'studentId' => 'Student ID',
            'workSite' => 'Work Site',
            'coverPath' => 'cover Path',
            'createTime' => 'Create Time',
            'type' => 'Type',
            'status' => 'Status',
        ];
    }


    /**获取作品排名
     * @param $activityId
     * @param $length
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public static function getWorksRank($activityId,$length)
    {
        if(!$activityId) {
            return null;
        }
        $activityId = intval($activityId);
        $length = intval($length);
        $rank = self::find()->alias('w')
            ->select(['w.id','w.workName','w.username','w.coverPath','(h.hits + s.share) as total'])
            ->leftJoin('cs_hits as h','h.primaryId = w.id')
            ->leftJoin('cs_share as s','s.primaryId = w.id')
            ->where(['w.activityId' => $activityId])
            ->andWhere(['w.status' => WORK_STATUS['pass']])
            ->andWhere(['h.type' => HITS_TYPE['work']])
            ->andWhere(['s.type' => HITS_TYPE['work']])
            ->orderBy('total desc')
            ->limit($length)
        /*$sql = $rank->createCommand()->getRawSql();
        return $sql;*/
            ->asArray()
            ->all();
        return $rank;
    }

    /**获取上传作品信息
     * @param $activityId
     * @param $userId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getWorkInfo($activityId,$userId)
    {
        if(!$activityId || !$userId) {
            return null;
        }
        $activityId = intval($activityId);
        $userId = intval($userId);
        $formItem = self::find()
                ->select(['id','activityId','userId','username','workName','tel','departments','division','studentId','type'])
                ->where(['activityId' => $activityId,'userId' => $userId])
                ->asArray()
                ->one();
        if(!$formItem) {
            $formItem['departments'] = Agency::getUserAgency($userId);
        }
        $companyName = Activity::find()
                ->select(['companyName'])
                ->where(['id' => $activityId])
                ->one()->companyName;
        $isUpload = FilesInfo::find()
                    ->where([
                    'activityId' => $activityId,
                    'userId' => $userId,
                    'status' => FILE_STATUS['finish']
                        ])
                    ->one();
        //判断用户是否绑定手机
        $bindPhone = TRUE;
        $user = User::findOne(['id' => $userId]);
        if(!$user->phone) {
            $bindPhone = false;
        }else {
            if(!$formItem) {
                $formItem['tel'] = $user->phone;
            }
        }
        $uploadStatus = FILE_STATUS['fail'];
        $signStatus = SIGN_STATUS['not_sign'];
        $data = ['companyName' => $companyName,'uploadStatus' => $uploadStatus,'formItem' => $formItem,'bindPhone' => $bindPhone];
        /*如果有用户ID查询该用户是否报名*/
        $isSignUp = Signup::find()
            ->where(['userId' => $userId,'activityId' => $activityId])
            ->one();
        if($isSignUp) {
            $signStatus = $isSignUp->status;
        }
        $data['signStatus'] = $signStatus;
        if(!empty($isUpload)) {
            $data['uploadStatus'] = FILE_STATUS['finish'];
            $data['fileInfo'] = $isUpload;
        }
        return $data;
    }

    /**获取作品详情
     * @param $workId
     * @param int $userId
     * @return array|null
     */
    public static function getDetail($workId,$userId = 0)
    {
        if(!$workId) {
            return null;
        }
        $workId = intval($workId);
        $info = self::find()->alias('w')
            ->select(['w.id','w.workName','w.username','h.hits','s.share','a.id as activityId','a.type','a.status as activityStatus'])
            ->leftJoin('cs_hits as h','h.primaryId = w.id')
            ->leftJoin('cs_share as s','s.primaryId = w.id')
            ->leftJoin('cs_activity as a','a.id = w.activityId')
            ->where(['w.id' => $workId])
            ->andWhere(['w.status' => WORK_STATUS['pass']])
            ->andWhere(['h.type' => HITS_TYPE['work']])
            ->andWhere(['s.type' => HITS_TYPE['work']])
            ->asArray()
            ->one();
        $isHits = false;
        if(!empty($userId)) {
            $userId = intval($userId);
            $res = UserHitsShare::findOne(['primaryId' => $workId,'type' => HITS_TYPE['work'],'userId' => $userId]);
            if($res) {
                $isHits = true;
            }
        }
        $info['isHits'] = $isHits;
        $files = WorksFile::find()
            ->select(['id as fileId','detail as introImage','type','workId','pdfImage'])
            ->where(['workId' => $workId])
            ->asArray()->all();
        $data = ['info' => $info,'files' => $files];
        return $data;
    }

    /**获取作品列表并关联点击数和分享数
     * @param $page
     * @param $activityId
     * @param $length
     * @param int $userId
     * @return array|null
     */
    public static function getWorksLists($page,$activityId,$length ,$userId,$isMine)
    {
        if(empty($page) || empty($length)) {
                return null;
        }
        $page = intval($page);
        $length = intval($length);
        $activityId = intval($activityId);
        $userId = intval($userId);
        $data = [];
        $query = self::find()->alias('w')
            ->select(['w.id','w.workName','w.username','w.coverPath','h.hits','s.share','a.id as activityId','a.type','a.status as activityStatus'])
            ->leftJoin('cs_hits as h','h.primaryId = w.id')
            ->leftJoin('cs_share as s','s.primaryId = w.id')
            ->leftJoin('cs_activity as a','a.id = w.activityId')
            ->where(['w.status' => WORK_STATUS['pass']])
            ->andWhere(['h.type' => HITS_TYPE['work']])
            ->andWhere(['s.type' => HITS_TYPE['work']]);
        if($isMine) {
            $query->andWhere(['w.userId' => $userId]);
        }elseif ($activityId){
            $query->andWhere(['w.activityId' => $activityId]);
        }
//        echo $query->createCommand()->getRawSql();exit;
        $count = $query->count();
//        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => $pageSize]);
        $index = ($page - 1) * $length;
        $lists = $query
            ->offset(intval($index))
            ->limit($length)
            ->asArray()
            ->all();
        /*判断作品是否被点赞*/
            foreach ($lists as &$list) {
                //如果作品为设置封面,默认取解压后的第一张图片作为封面
                if(empty($list['coverPath'])) {
                    $coverPath = WorksFile::find()
                        ->select(['detail'])
                        ->where(['workId' => $list['id'],'type' => 1])
                        ->limit(1)
                        ->one();
                    $list['coverPath'] = $coverPath['detail'];
                }
                $prizeNames = Prize::find()->alias('p')
//                        ->select(['GROUP_CONCAT(p.prizeName) AS prizeNames'])
                        ->select(['p.prizeName AS prizeName'])
                        ->leftJoin('cs_works_prize as wp','wp.prizeId = p.id')
                        ->leftJoin('cs_works as w','w.id = wp.workId')
                        ->where(['w.id' => $list['id']])
//                        ->one();
                        ->asArray()
                        ->all();
                $list['prizeNames'] = $prizeNames;
                //不是查我的作品的时候
//                if(!$isMine) {
                    if (empty($userId)) {
                        $list['isHits'] = false;
                        continue;
                    }
                    $res = UserHitsShare::findOne(['primaryId' => $list['id'], 'type' => HITS_TYPE['work'], 'userId' => $userId]);
                    if ($res) {
                        $list['isHits'] = true;
                    } else {
                        $list['isHits'] = false;
                    }
//                }
            }
//        echo $query->createCommand()->sql;exit;
//        $data['totalPage'] = $pagination->getPageCount();
        $count = intval($count);
        $data['count'] = $count;
        $data['totalPage'] = ceil($count / $length);
        $data['nowPage'] = $page;
        $data['lists'] = $lists;
        return $data;

    }   

    /**
     * 通过条件查询作品列表
     * @param  [type] $activeIds [description]
     * @return [type]            [description]
     */
    public function getListByCondition($where, $pageSize = 20)
    {   
        $query = self::find()->select(['id', 'activityId', 'workName', 'username', 'status', 'type', 'FROM_UNIXTIME(createTime,"%Y-%m-%d") as date'])
            ->where($where)->andWhere(['!=', 'status', 0]);

        /*分页获取数据*/
        $count = $query->count('id');
        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => $pageSize]);

        $list['rows'] = $query->offset($pagination->offset)
        ->limit($pageSize)
        ->asArray()
        ->all();

        $list['total'] = $count;
        return $list;
    }

    /**
     * 查询作品类型
     * @return [type] [description]
     */
    public static function getWorksType($activeId)
    {
        return self::find()->select(['type'])->where('activityId = ' . intval($activeId))->groupBy('type')->all();
    }

    /**
     * 查询作品详情
     * @param  [type] $workId [description]
     * @return [type]         [description]
     */
    public static function getWorksInfo($workId)
    {
        return self::find()->where('id = ' . intval($workId))->asArray()->one();
    }

    /**
     * 查询作品奖项
     * @param  [type] $where   [description]
     * @param  [type] $orWhere [description]
     * @param  [type] $sort    [description]
     * @return [type]          [description]
     */
    public function getWorkList($sort, $where, $orWhere = array(), $pageSize = 20)
    {
        $query = self::find()->alias('a')
            ->select(['a.id', 'a.activityId', 'a.username', 'a.workName', 'a.type', 'b.hits', 'c.share'])
            ->leftJoin(Hits::tableName() . ' as b', 'a.id = b.primaryId')
            ->leftJoin(Share::tableName() . ' as c', 'a.id = c.primaryId');

        if($orWhere){
            foreach($orWhere as $key => $value){
                $query->orWhere($value);
            }
        }

        if($where){
            if($orWhere){
                $query->andWhere($where);
            }else{
                $query->where($where);
            }
        }

        $query->orderBy($sort . ' DESC');

        /*分页获取数据*/
        $count = $query->count('a.id');
        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => $pageSize]);

        $list['rows'] = $query->offset($pagination->offset)
            ->limit($pageSize)
            ->asArray()
            ->all();

        $list['total'] = $count;
        $works = $query->select(['GROUP_CONCAT(a.id) as workIds'])->one();

        if(!$list['rows']){
            return $list;
        }

        /*查询奖项*/
        $prizes = array();
        $workPrize = new WorksPrize();
        $prizeList = $workPrize->getPrizeByWorkIds(explode(',', $works['workIds']));

        if($prizeList){
            foreach($prizeList as $key => $prize){
                if(!isset($prizes[$prize['workId']])){
                    $prizes[$prize['workId']] = '';
                }

                $prizes[$prize['workId']] .= $prize['prizeName'] . '、';
            }
        }

        foreach($list['rows'] as $key => $value){
            $list['rows'][$key]['hits'] = intval($value['hits']);
            $list['rows'][$key]['share'] = intval($value['share']);
            $list['rows'][$key]['prize'] = '';

            if(isset($prizes[$value['id']])){
                $list['rows'][$key]['prize'] = rtrim($prizes[$value['id']], '、');
            }
        }

        return $list;
    }

    /**保存作品
     * @param $data
     * @return Works|array|null|\yii\db\ActiveRecord
     */
    public static function saveInfo($data)
    {
        if(empty($data)) {
            return ['result' => false,'message' => '数据不全'];
        }
        extract($data);
        //查找是否有文件信息
        $work = self::find()
            ->where([
                'activityId' => $activityId,
                'userId' => $userId,
                ])
            ->one();
        if(!$work) {
            $work = new Works();
            $data['createTime'] = time();
        }
        if($work->status == WORK_STATUS['init']) {
            return ['result' => false,'message' => '已有完整作品上传'];
        }
        $work->load($data,'');
        if($work->validate()) {
            $work->save();
        }else {
            return ['result' => false,'message' => $work->getErrors()];
        }
        //判断作品是否创建有点赞和分享表
        $hit = Hits::find()
            ->where(['primaryId' => $work->id,'type' => HITS_TYPE['work']])
            ->one();
        if(!$hit) {
            $hit = new Hits();
            $hit->primaryId = $work->id;
            if(!$hit->save()) {
                return ['result' => false,'message' => $hit->getErrors()];
            }
        }
        $share = Share::find()
            ->where(['primaryId' => $work->id,'type' => HITS_TYPE['work']])
            ->one();
        if(!$share) {
            $share = new Share();
            $share->primaryId = $work->id;
            if(!$share->save()) {
                return ['result' => false,'message' => $share->getErrors()];
            }
        }
        $isUpload = FilesInfo::find()->where([
            'activityId' => $activityId,
            'userId' => $userId,
            'status' => FILE_STATUS['finish']
        ])->one();
        $uploadStatus = FILE_STATUS['fail'];
        if($isUpload) {
            $uploadStatus = FILE_STATUS['finish'];
        }
        return ['result' => true,'message' => $uploadStatus];
    }

    /**
     * 作品分析
     * @param $startTime
     * @param $endTime
     */
    public function analysisWorks($type, $startTime, $endTime)
    {
        $data = array(
            'table' => array(
                array(
                    'title' => '数量',
                    'image' => 0,
                    'video' => 0,
                    'other' => 0,
                    'total' => 0
                ),
                array(
                    'title' => '容量 (Kb)',
                    'image' => 0,
                    'video' => 0,
                    'other' => 0,
                    'total' => 0
                )
            ),
            'point' => array(
                'worksCount' => 0,
                'imagePoint' => 0,
                'videoPoint' => 0,
                'otherPoint' => 0
            )
        );

        $works = self::find()
            ->select(['id'])
            ->where(['status' => WORK_STATUS['pass']])
            ->andWhere(['>=', 'createTime', $startTime])
            ->andWhere(['<', 'createTime', $endTime])
            ->asArray()
            ->all();

        if(!$works){
            return $data;
        }

        $ids = array();
        foreach($works as $key => $work){
            $ids[] = $work['id'];
        }

        $data = array();
        $workCount = $this->analysisWorkCount($ids);
        $fileSize = $this->analysisSize($ids);
        $data['table'][] = $workCount['number'];
        $data['table'][] = $fileSize['size'];

        if($type == 1){
            $data['point'] = $workCount['point'];
        }elseif($type == 2){
            $data['point'] = $fileSize['point'];
        }

        return $data;
    }

    /**
     * 作品数量分析
     * @param $ids
     */
    public function analysisWorkCount($ids)
    {
        $files = WorksFile::find()->select(['count(id) as num', 'type'])
            ->where(['workId' => $ids])->groupBy('type')->asArray()->all();

        $total = 0;
        $images = 0;
        $videos = 0;
        $others = 0;

        if($files){
            foreach($files as $file){
                if($file['type'] == FILE_TYPE['pic_file']){
                    $images = $file['num'];
                }elseif($file['type'] == FILE_TYPE['video_file']){
                    $videos = $file['num'];
                }elseif($file['type'] == FILE_TYPE['other_file']){
                    $others = $file['num'];
                }
                $total += $file['num'];
            }
        }

        $data['point']['worksCount'] = count($ids);
        $data['point']['imagePoint'] = 0;
        $data['point']['videoPoint'] = 0;
        $data['point']['otherPoint'] = 0;

        if($total){
            $data['point']['imagePoint'] = $images;
            $data['point']['videoPoint'] = $videos;
            $data['point']['otherPoint'] = $others;
        }

        $data['number']['title'] = '数量';
        $data['number']['image'] = $images;
        $data['number']['video'] = $videos;
        $data['number']['other'] = $others;
        $data['number']['total'] = $total;

        return $data;
    }

    /**
     * 作品容量分析
     * @param $startTime
     * @param $endTime
     */
    public function analysisSize($ids)
    {
        $size = WorkFilesDetail::find()
            ->select(['sum(picSize) as picSize', 'sum(videoSize) as videoSize', 'sum(otherSize) as otherSize'])
            ->where(['workId' => $ids])
            ->asArray()
            ->one();

        $imgSize = intval($size['picSize']);
        $videoSize = intval($size['videoSize']);
        $otherSize = intval($size['otherSize']);
        $totalSize = $imgSize + $videoSize + $otherSize;

        $data['point']['worksCount'] = count($ids);
        $data['point']['imagePoint'] = 0;
        $data['point']['videoPoint'] = 0;
        $data['point']['otherPoint'] = 0;

        if($totalSize){
            $data['point']['imagePoint'] = round($imgSize/1024, 2);
            $data['point']['videoPoint'] = round($videoSize/1024, 2);
            $data['point']['otherPoint'] = round($otherSize/1024, 2);
        }

        $data['size']['title'] = '容量 (Kb)';
        $data['size']['image'] = round($imgSize/1024, 2);
        $data['size']['video'] = round($videoSize/1024, 2);
        $data['size']['other'] = round($otherSize/1024, 2);
        $data['size']['total'] = round($totalSize/1024, 2);

        return $data;
    }

    /**
     * 通过活动ID查询参加活动的学校
     * @param $activityId
     */
    public function getJoinSchoolListByActivity($activityId, $pageSize = 20)
    {
        $list = array(
            'rows' => array(),
            'total' => 0
        );

        $query = self::find()->select(['activityId', 'departments as name', 'count(userId) as number'])
            ->where(['activityId' => intval($activityId), 'status' => WORK_STATUS['pass']])
            ->groupBy('departments');

        $count = $query->count('*');
        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => $pageSize]);
        $list['rows'] = $query->offset($pagination->offset)
            ->limit($pageSize)
            ->asArray()
            ->all();

        $list['total'] = $count;

        return $list;
    }

    /**
     * 查询参加活动的用户
     * @param $name
     * @param $activityId
     * @param $pageSize
     */
    public function getJoinActivityUser($name, $activityId, $pageSize = 20)
    {
        $query = self::find()->select(['a.username', 'a.tel', 'a.studentId', 'IFNULL(b.share, 0) share', 'IFNULL(c.hits, 0) hits', '(b.share + c.hits) as num', '(0) as rank'])->alias('a')
            ->leftJoin(Share::tableName() . ' as b', 'b.primaryId = a.id')
            ->leftJoin(Hits::tableName() . ' as c', 'c.primaryId = a.id')
            ->where(['a.activityId' => intval($activityId), 'a.departments' => $name, 'a.status' => WORK_STATUS['pass']])
            ->orderBy('num DESC');

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => $pageSize]);

        $list['rows'] = $query->offset($pagination->offset)
            ->limit($pageSize)
            ->asArray()
            ->all();

        if($count){
            $i = 0;
            foreach($list['rows'] as $key => &$value){
                $value['rank'] = $pagination->offset * $pageSize + 1 + $i;
                $i ++;
            }
        }

        $list['total'] = $count;
        return $list;
    }

}
