<?php

namespace api\models;

use Yii;
use yii\db\Query;
use yii\data\Pagination;
use common\helpers\Helper;


/**
 * This is the model class for table "{{%activity}}".
 *
 * @property int $id
 * @property string $name 活动名称
 * @property string $intro 大赛简介
 * @property string $smallImage 活动列表图
 * @property string $bigImage 活动列表图
 * @property string $pcImage PCbanner图
 * @property string $mobileImage 手机banner图
 * @property string $reviewStandard 审查标准
 * @property string $questionAnswerDetail 问题答案详解
 * @property int $type 1:大赛 2：问卷
 * @property string $activityStartTime 活动开始时间
 * @property string $activityEndTime 活动结束时间
 * @property int $userId 创建活动的用户id
 * @property string $userName 创建活动用户
 * @property string $applicationDate 申请活动的日期
 * @property int $auditState 审核状态   0:保存状态(不能审核) 1:审核通过 2:提交审核 3:未通过审核
 * @property string $companyName 公司名称
 * @property int $status 1未开始  2进行中 3已结束
 * @property string $worksCount 作品数量
 * @property int $isDeleted 是否下架  0 上架  1 下架
 */
class Activity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activity}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'pcImage'], 'required','on'=>['addActivity','addAnswer']],
            [['type',  'userId',  'auditState', 'status', 'worksCount','activityStartTime', 'activityEndTime','applicationDate'], 'integer','on'=>['addActivity','addAnswer']],
            [['name'], 'string', 'max' => 30,'on'=>['addActivity','addAnswer']],
            [['intro', 'reviewStandard'], 'string', 'max' => 2000,'on'=>['addActivity','addAnswer']],
            [['smallImage', 'pcImage', 'mobileImage','bigImage'], 'string', 'max' => 150,'on'=>['addActivity','addAnswer']],
            [['questionAnswerDetail'], 'string', 'max' => 256,'on'=>['addAnswer']],
            [['userName'], 'string', 'max' => 20,'on'=>['addActivity','addAnswer']],
            [['companyName'], 'string', 'max' => 50,'on'=>['addActivity','addAnswer']],
            [['allowJoin','isDeleted'], 'integer', 'max' => 2, 'on' => ['addActivity', 'update']],
            [['id'], 'required','on'=>['update']],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            'addActivity' =>//添加大赛场景
                [
                'name', 'pcImage','type',  'userId',  'auditState', 'status','bigImage',
                'worksCount','activityStartTime', 'activityEndTime','applicationDate',
                'intro', 'reviewStandard','smallImage', 'pcImage', 'mobileImage','userName','companyName','allowJoin'
               ],
            'addAnswer' =>//添加问答场景
                [
                    'name', 'pcImage','mobileImage','intro','smallImage','bigImage','applicationDate','status','auditState',
                'questionAnswerDetail','type','activityStartTime','activityEndTime','companyName','userId','userName','allowJoin'
               ],
            'update' =>//更新大赛场景
                [
                    'id','name', 'pcImage','type',  'userId',  'auditState', 'status','bigImage',
                    'worksCount','activityStartTime', 'activityEndTime','applicationDate',
                    'intro', 'reviewStandard','smallImage', 'pcImage', 'mobileImage','userName','companyName','allowJoin','isDeleted'
                ],
            'updateAnswer' =>//更新问答场景
                [
                    'name', 'pcImage','mobileImage','intro','smallImage','bigImage','status','auditState',
                    'questionAnswerDetail','type','activityStartTime','activityEndTime','companyName','allowJoin'
                ],
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {

        if(parent::beforeSave($insert))
        {
            if($this->isNewRecord)
            {
                $this->applicationDate = time();
            }

            $this->activityStartTime = mb_substr( $this->getAttributes(['activityStartTime'])['activityStartTime'], 0, 10);


            $this->activityEndTime = intval(mb_substr( $this->getAttributes(['activityEndTime'])['activityEndTime'], 0, 10))+86399;

            return true;
        }
        else
            return false;
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'intro' => 'Intro',
            'smallImage' => 'Small Image',
            'bigImage' => 'Small Image',
            'pcImage' => 'Pc Image',
            'mobileImage' => 'Mobile Image',
            'reviewStandard' => 'Review Standard',
            'questionAnswerDetail' => 'Question Answer Detail',
            'type' => 'Type',
            'activityStartTime' => 'Activity Start Time',
            'activityEndTime' => 'Activity End Time',
            'userId' => 'User ID',
            'userName' => 'User Name',
            'applicationDate' => 'Application Date',
            'auditState' => 'Audit State',
            'companyName' => 'Company Name',
            'status' => 'Status',
            'worksCount' => 'Works Count',
        ];
    }

    /**
     * 活动数据统计
     * @return [type] [description]
     */
    public function getData($userId, $days, $gran, $area)
    {
        $signQuery = (new Query())->select(['activityId', 'count(userId) as user'])
            ->from(signup::tableName())
            ->groupBy('activityId');

        $query = self::find()->select(['count(a.id) as num', 'GROUP_CONCAT(id) as activeId', 'a.status', 'a.type', 'sum(c.user) as user'])->alias('a')
            ->leftJoin(['c' => $signQuery], 'c.activityId = a.id');

        if(!(new AuthItem())->userIsAdmin($userId)){
            $query->where(['a.userId' => intval($userId)]);
        }

        if($area){
            $body['area'] = $area;
            //$body['pageSize'] = 100;

            /*$body['sign'] = Helper::paramSign($body);
            $url = API_DOMAIN . API_URL['agency_page'];
            $list = Helper::curl_exec($url, 'POST', true, $body);*/
            $result = (new Agency())->getListByCondition($body, array(), 1, 100);
            $list = $result['rows'];

            $schoolId = array();
            if(isset($list->status) && $list->status == 200){
                $rows = $list->data->rows;
                foreach ($rows as $key => $value){
                    $schoolId[] = $value->id;
                }
            }

            if($schoolId){
                $activeList = ActivityOrganization::find()->select('GROUP_CONCAT(activityId) as activityIds')
                    ->where(['organizationId' => $schoolId])
                    ->asArray()
                    ->one();
                if($activeList['activityIds']){
                    $query->andWhere(['a.id' => explode(',', $activeList['activityIds'])]);
                }
            }
        }

        $num = 1;
        if($gran == 'day'){
            $num = 7;
            if($days < -6){
                $num = 30;
            }
        }

        $start_date = strtotime(date('Y-m-d', strtotime("$days day")));
        $end_date = strtotime(date('Y-m-d', strtotime("+" . ($days + $num) . " day")));

        $query->andWhere(['>=', 'applicationDate', $start_date])
            ->andWhere(['<', 'applicationDate', $end_date]);

        return $query->groupBy(['a.type', 'a.status'])->asArray()->all();
    }

    /**
     * 统计活动状态
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function countStatus($type, $userId, $days, $gran, $area)
    {
        $num = 1;
        if($gran == 'day'){
            $num = 7;
            if($days < -6){
                $num = 30;
            }
        }

        $where = array();
        $start_date = strtotime(date('Y-m-d', strtotime("$days day")));
        $end_date = strtotime(date('Y-m-d', strtotime("+" . ($days + $num) . " day")));

        if($area){
            $body['area'] = $area;
            /*$body['pageSize'] = 100;

            $body['sign'] = Helper::paramSign($body);
            $url = API_DOMAIN . API_URL['agency_page'];
            $list = Helper::curl_exec($url, 'POST', true, $body);*/
            $result = (new Agency())->getListByCondition($body, array(), 1, 100);
            $list = $result['rows'];

            $schoolId = array();
            if(isset($list->status) && $list->status == 200){
                $rows = $list->data->rows;
                foreach ($rows as $key => $value){
                    $schoolId[] = $value->id;
                }
            }

            if($schoolId){
                $activeList = ActivityOrganization::find()->select('GROUP_CONCAT(activityId) as activityIds')
                    ->where(['organizationId' => $schoolId])
                    ->asArray()
                    ->one();
                if($activeList['activityIds']){
                    $where['id'] = explode(',', $activeList['activityIds']);
                }
            }
        }

        if($type){
            $where['type'] = $type;
        }

        if(!(new AuthItem())->userIsAdmin($userId)){
            $where['userId'] = intval($userId);
        }

        return self::find()->select(['count(id) as num', 'status'])
            ->where($where)
            ->andWhere(['>=', 'applicationDate', $start_date])
            ->andWhere(['<', 'applicationDate', $end_date])
            ->groupBy(['status'])
            ->asArray()
            ->all();
    }

    /**
     * 查询活动总数
     */
    public function countActive($type)
    {
        if($type){
            return self::find()->where(['type' => $type])->count();
        }
        
        return self::find()->count();
    }

    /**
     * 按月统计活动数量
     * @return [type] [description]
     */
    public function countActivity($userId)
    {
        $data = array();
        $min = -11;
        $first_day_of_month = date('Y-m',time()) . '-01 00:00:01';
        $t = strtotime($first_day_of_month);

        for($i = $min;$i <= 0;$i++){
            $month = date('Y-m', strtotime("$i month", $t));
            $data[$month]['active'] = 0;
            $data[$month]['question'] = 0;
        }

        $andWhere = array();
        if(!(new AuthItem())->userIsAdmin($userId)){
            $andWhere['userId'] = intval($userId);
        }

        $list = self::find()->select(['type', 'activityStartTime'])
            ->where(['between', 'activityStartTime', strtotime("-11 month", $t), strtotime("+1 month", $t)])
            ->andWhere($andWhere)
            ->all();

        if($list){
            foreach($list as $key => $row){
                $month = date('Y-m', $row['activityStartTime']);
                if(!isset($data[$month])){
                    continue;
                }

                if($row['type'] == ACTIVITY_TYPE['active']){
                    $data[$month]['active']++;
                }else{
                    $data[$month]['question']++;
                }
            }
        }

        $month = array();
        $active = array();
        $question =array();
        $total = array();
        $table = array();

        foreach($data as $key => $val){
            $month[] = $key;
            $active[] = $val['active'];
            $question[] = $val['question'];
            $total[] = $val['active'] + $val['question'];

            $table[$key]['date'] = $key;
            $table[$key]['active'] = $val['active'];
            $table[$key]['question'] = $val['question'];
        }

        $chart['month'] = $month;
        $chart['active'] = $active;
        $chart['question'] = $question;
        $chart['total'] = $total;

        $result['table'] = array_values(array_values($table));
        $result['chart'] = $chart;

        return $result;
    }

    /**
     * 用户发布活动统计
     * @return [type] [description]
     */
    public function countUserPublish($orWhere, $pageSize = 20)
    {
        $query = self::find()->select(['userId', 'userName', 'companyName', 'count(id) as num', 'GROUP_CONCAT(id) as ids'])->groupBy(['userId']);
        if($orWhere){
            foreach($orWhere as $condition){
                $query->orWhere($condition);
            }
        }

        /*分页获取数据*/
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => $pageSize]);

        $list['rows'] = $query->offset($pagination->offset)
            ->limit($pageSize)
            ->asArray()
            ->all();

        $list['total'] = $count;

        if($list['rows']){
            foreach($list['rows'] as $key => $value){
                $row = Trend::find()->select(['sum(number) as number', 'sum(point) as point'])->where(['activeId' => explode(',', $value['ids'])])->one();

                $list['rows'][$key]['total'] = intval($row->number);
                $list['rows'][$key]['point'] = round($row->point/$value['num'], 2) * 100;
                unset($list['rows'][$key]['ids']);
            }
        }

        return $list;
    }

    /**
     * 用户发布的活动
     * @return [type] [description]
     */
    public function userActivity($where, $pageSize = 20)
    {
        $trendQuery = (new Query())->select(['number', 'round(point*100) as point', 'activeId'])
            ->from(Trend::tableName());

        $query = self::find()->select(['a.name', 'a.type', 'a.status', 'if(b.number, b.number, 0) as number', 'if(b.point, b.point, 0) as point'])->alias('a')
            ->where($where)
            ->leftJoin(['b' => $trendQuery], 'a.id = b.activeId');

        /*分页获取数据*/
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => $pageSize]);

        $list['rows'] = $query->offset($pagination->offset)
        ->limit($pageSize)
        ->asArray()
        ->all();

        $list['total'] = $count;

        return $list;
    }

    /**
     * 查询用户发布的活动ID
     * @param  [type] $userId [description]
     * @return [type]         [description]
     */
    public function getActiveIdsByUserId($userId)
    {
        return self::find()->select('GROUP_CONCAT(id) as ids')
            ->where(['userId' => intval($userId), 'type' => ACTIVITY_TYPE['active']])
            ->asArray()->one();
    }

    /**首页返回6张轮播图
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRecentPictures()
    {
        $query = self::find()
            ->select(['id','pcImage','mobileImage','type','status'])
            ->where(['=','auditState',ACTIVITY_AUDITSTATE['pass']])
            ->andWhere(['=','status',ACTIVITY_STATUS['started']])
            ->andWhere(['=','isDeleted',ACTIVITY_IS_DELETED['no']]);
        /*if($userId) {
            $userId = intval($userId);
            $ids = ActivityOrganization::getAgencyActivityIds($userId);
            $query->andWhere(['in','id',$ids]);
        }*/
        $pictures = $query->orderBy('applicationDate DESC')
            ->offset(0)
            ->limit(6)
            ->asArray()
            ->all();
        return $pictures;
    }


    /**获取活动数据
     * @param $start
     * @param $length
     * @param int $status
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public static function getRecentActivities($page,$length,$status,$type,$userId,$isMine)
    {
        if(!$length || !$status || !$type) {
            return null;
        }
        $start = ($page - 1) * $length;
        $query = self::find()
            ->select(['id','name','smallImage','type','status'])
            ->where(['=','auditState',ACTIVITY_AUDITSTATE['pass']])
            ->andWhere(['in','status',$status])
            ->andWhere(['in','type',$type])
            ->andWhere(['=','isDeleted',ACTIVITY_IS_DELETED['no']]);
        if($userId) {
            $userId = intval($userId);
//            $ids = ActivityOrganization::getAgencyActivityIds($userId);
            if($isMine) {
                $mineIds = Signup::getUserActivityIds($userId);
//                $ids = array_intersect($mineIds,$ids);
                $query->andWhere(['in','id',$mineIds]);
            }
        }
        $count = $query->count();
        $activities = $query->orderBy('applicationDate DESC')
                    ->offset($start)->limit($length)->all();
        return ['activities' => $activities,'count' => intval($count)];
    }

    /**
     * 统计活动相关数据
     * @param $uid
     */
    public function getActiveRelatedCount($uid, $pageSize)
    {
        $where = array();
        $where['a.type'] = 1;

        if(!(new AuthItem())->userIsAdmin($uid)){
            $where['a.userId'] = intval($uid);
        }

        $signQuery = Signup::find()->select(['count(userId) as signCount', 'activityId'])->groupBy('activityId');
        $fileQuery = FilesInfo::find()->select(['count(id) as auditCount', 'activityId'])->where('status = 1')->groupBy('activityId');
        $clickQuery = Clicks::find()->select(['count(id) as click', 'primaryId'])->where('type = 1')->groupBy('primaryId');

        $query = self::find()
            ->select(['a.id', 'a.name', 'if(b.signCount, b.signCount, 0) as signCount', 'a.worksCount',
                'if(c.auditCount, c.auditCount, 0) as auditCount', 'if(e.click, e.click, 0) as click', 'if(g.number, g.number, 0) as registerCount'])->alias('a')
            ->leftJoin(['b' => $signQuery], 'a.id = b.activityId')
            ->leftJoin(['c' => $fileQuery], 'a.id = c.activityId')
            ->leftJoin(['e' => $clickQuery], 'a.id = e.primaryId')
            ->leftJoin(ActivityRegister::tableName() . ' as g', 'a.id = g.activityId')
            ->where($where)
            ->orderBy('a.id DESC');

        $count = $query->count('a.id');
        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => $pageSize]);

        $list['total'] = $count;
        $list['rows'] = $query->offset($pagination->offset)
            ->limit($pageSize)
            ->asArray()
            ->all();

        $activeId = array();
        if($list['rows']){
            foreach($list['rows'] as $value){
                $activeId[] = $value['id'];
            }
        }

        if($activeId){
            /*查询活动下作品的点赞*/
            $hitSql = 'SELECT SUM(hits) as hits,activityId FROM (
                SELECT a.id,b.hits,a.activityId FROM '.Works::tableName().' a
                LEFT JOIN (
                    SELECT hits,primaryId FROM '.Hits::tableName().'
                ) b ON a.id = b.primaryId
                WHERE a.activityId in ('.implode(',', $activeId).')
            ) c GROUP BY activityId';
            $hitQuery = Yii::$app->db->createCommand($hitSql)->queryAll();

            /*查询活动下作品的分享*/
            $shareSql = 'SELECT SUM(share) as share,activityId FROM (
                SELECT a.id,b.share,a.activityId FROM '.Works::tableName().' a
                LEFT JOIN (
                    SELECT share,primaryId FROM '.Share::tableName().'
                ) b ON a.id = b.primaryId
                WHERE a.activityId in ('.implode(',', $activeId).')
            ) c GROUP BY activityId';
            $shareQuery = Yii::$app->db->createCommand($shareSql)->queryAll();

            foreach($list['rows'] as $key => $value){
                if($hitQuery){
                    foreach($hitQuery as $v){
                        if($value['id'] == $v['activityId']){
                            $list['rows'][$key]['hits'] = $v['hits'];
                        }
                    }
                }

                if($shareQuery){
                    foreach($shareQuery as $item){
                        if($value['id'] == $item['activityId']){
                            $list['rows'][$key]['share'] = $item['share'];
                        }
                    }
                }

                if(!isset($list['rows'][$key]['hits'])){
                    $list['rows'][$key]['hits'] = 0;
                }

                if(!isset($list['rows'][$key]['share'])){
                    $list['rows'][$key]['share'] = 0;
                }
            }
        }

        return $list;
    }

    /**
     * 查询活动列表
     * @param  [type] $where    [description]
     * @param  [type] $andWhere [description]
     * @param  [type] $pageSize [description]
     * @return [type]           [description]
     */
    public function getActivieyList($where, $andWhere, $pageSize = 20)
    {
        $query = self::find()->select(['id', 'name', 'type', 'userName', 'companyName', 'applicationDate', 'auditState', 'status']);

        if($where){
            $query->where($where);
        }

        if($andWhere){
            $query->andWhere($andWhere);
        }

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

    /**获取活动简介图片
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(IntroImage::className(),['activityId' => 'id']);
    }

    /**获取活动奖项
     * @return \yii\db\ActiveQuery
     */
    public function getPrizes()
    {
        return $this->hasMany(Prize::className(),['activityId' => 'id']);
    }

    /**获取活动流程
     * @return \yii\db\ActiveQuery
     */
    public function getActivityTime()
    {
        return $this->hasMany(ActivityTime::className(),['activityId' => 'id']);
    }
    /**获取活动简介图片
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(),['activityId' => 'id']);
    }

    /**获取活动积分
     * @return \yii\db\ActiveQuery
     */
    public function getActivityScore()
    {
        return $this->hasOne(ActivityScore::className(),['activityId' => 'id']);
    }

    /**获取活动學校
     * @return \yii\db\ActiveQuery
     */
    public function getActivityOrganization()
    {
        return $this->hasMany(ActivityOrganization::className(),['activityId' => 'id']);
    }


    /**返回活动最晚结束的报名截止日期
     * @param $activityId
     * @return mixed|null
     */
    public static function getSignTime($activityId)
    {
        if(!$activityId) {
            return null;
        }
        $activity = self::find()->where(['id' => $activityId])->one();
        if(!$activity) {
            return null;
        }
        $acTime = 0;
        //问卷倒计时
        if($activity->type == ACTIVITY_TYPE['question']) {
            //已经开始返回距离结束时间
            if($activity->status == ACTIVITY_STATUS['started']) {
                $acTime = $activity->activityEndTime - time();
            }elseif ($activity->status == ACTIVITY_STATUS['init']) {
                $acTime = $activity->activityStartTime - time();
            }
        }else {
            //活动已经开始查询距离最晚报名截止时间
            if ($activity->status == ACTIVITY_STATUS['started']) {
                $signTime = self::find()->alias('a')
                    ->select(['MAX(t.signEndTime) as endTime'])
                    ->leftJoin('cs_activity_time as t', 't.activityId = a.id')
                    ->where(['a.id' => $activityId])
                    ->asArray()
                    ->one();
                $acTime = $signTime['endTime'] - time();
            } elseif ($activity->status == ACTIVITY_STATUS['init']) {
                $signTime = self::find()->alias('a')
                    ->select(['MIN(t.signStartTime) as startTime'])
                    ->leftJoin('cs_activity_time as t', 't.activityId = a.id')
                    ->where(['a.id' => $activityId])
                    ->asArray()
                    ->one();
                $acTime = $signTime['startTime'] - time();
            }
        }
        return $acTime;
    }


    /**修改作品总数
     * @param $activityId
     * @return bool
     */
    public static function updateWorkSCount($activity,$num)
    {
        if(!$activity && !$num) {
            return false;
        }
        $num = intval($num);
        $activity->worksCount += $num;
        $activity->setScenario('update');
        if(!$activity->save()) {
            return $activity->getErrors();
        }
        return true;
    }

}
