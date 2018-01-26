<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "hawk_agency".
 *
 * @property string $id
 * @property string $name
 * @property string $depart 部门
 * @property string $city 城市
 * @property string $area 地区
 * @property string $fromUserName 所属销售
 * @property string $startTime 激活时间
 * @property int $endTime 截止时间
 * @property int $isDeleted 机构是否删除
 * @property int $userNum 学校用户数
 * @property string $ipScope IP段
 * @property string $src 机构地址
 * @property int $otherAgencyId 其他数据库机构id
 */
class Agency extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hawk_agency';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['startTime', 'endTime', 'isDeleted', 'userNum', 'otherAgencyId'], 'integer'],
            [['name', 'depart', 'city', 'area', 'fromUserName', 'src'], 'string', 'max' => 20],
            [['ipScope'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'depart' => 'Depart',
            'city' => 'City',
            'area' => 'Area',
            'fromUserName' => 'From User Name',
            'startTime' => 'Start Time',
            'endTime' => 'End Time',
            'isDeleted' => 'Is Deleted',
            'userNum' => 'User Num',
            'ipScope' => 'Ip Scope',
            'src' => 'Src',
            'otherAgencyId' => 'Other Agency ID',
        ];
    }

    public static function getUserAgency($userId)
    {
        if(!$userId) {
            return null;
        }
        $agency = self::find()
                ->alias('a')
                ->select(['a.id','a.name','u.phone'])
                ->leftJoin(UserAgency::tableName().'as ua','ua.agencyId = a.id')
                ->leftJoin(User::tableName().'as u','u.id = ua.userId')
                ->where(['u.id' => $userId])
                ->one();
//        var_dump($agency);exit;
        if(!is_null($agency)) {
            return $agency->name;
        }else {
            return null;
        }

    }

    /**
     * 查询所有的机构
     * @return [type] [description]
     */
    public function getList()
    {
        return self::find()->select(['id', 'name'])->all();
    }

    /**
     * 查询机构分页列表
     * @return [type] [description]
     */
    public function getListByCondition($where, $andWhere, $page = 1, $pageSize = 20)
    {
        $query = self::find()->select(['id', 'name', 'area', 'city', 'userNum']);

        if($where){
            $query->where($where);
        }

        if($andWhere){
            foreach($andWhere as $condition){
                $query->andWhere($condition);
            }
        }

        $query->andWhere(['!=', 'id', AGENCY_GUEST_ID]);

        $count = $query->count('id');
        $offset = ($page - 1 ) * $pageSize;

        $list['rows'] = $query->offset($offset)
            ->orderBy('id DESC')
            ->limit($pageSize)
            ->all();

        $list['total'] = $count;
        return $list;
    }

    /**
     * 查询机构详情
     * @param $schoolId
     */
    public function getSchoolInfo($schoolId)
    {
        $row = self::find()
            ->select(['id', 'name', 'area', 'city', 'depart', 'fromUserName', 'userNum', '`startTime`*1000 as startTime', '`endTime`*1000 as endTime'])
            ->where(['id' => $schoolId])
            ->asArray()
            ->one();

        if($row){
            $ips = AgencyIp::find()->select(['startingIP as startIp', 'endingIp as endIp'])->where(['agencyId' => $schoolId])->asArray()->all();
            $row['ips'] = $ips;
        }

        return $row;
    }
}
