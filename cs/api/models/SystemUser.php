<?php

namespace api\models;

use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "{{%system_user}}".
 *
 * @property int $id
 * @property string $password
 * @property string $username
 * @property string $realName
 * @property int $agencyId
 * @property string $agency
 * @property string $department 部门
 * @property int $signTime 注册时间
 * @property string $bigImage 大头像
 * @property string $smallImage 小头像
 */
class SystemUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agencyId', 'signTime'], 'integer'],
            [['password'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 30],
            [['realName'], 'string', 'max' => 20],
            [['agency', 'bigImage', 'smallImage'], 'string', 'max' => 60],
            [['department'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'password' => 'Password',
            'username' => 'Username',
            'realName' => 'Real Name',
            'agencyId' => 'Agency ID',
            'agency' => 'Agency',
            'department' => 'Department',
            'signTime' => 'Sign Time',
            'bigImage' => 'Big Image',
            'smallImage' => 'Small Image',
        ];
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * 根据条件查询用户
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function getUserList($where, $orWhere, $orderBy, $pageSize = 20)
    {
        $query = self::find()->alias('a')
            ->select(['a.*', 'FROM_UNIXTIME(signTime,"%Y-%m-%d") as signTime', 'b.item_name'])
            ->leftJoin(AuthAssignment::tableName() . ' as b', 'b.user_id = a.id');

        if($orWhere){
            foreach($orWhere as $condition){
                $query->orWhere($condition);
            }
        }

        if($where){
            $query->andWhere($where);
        }

        if($orderBy){
            $query->orderBy($orderBy);
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

    /**
     * 查询用户详情
     * @param  [type] $userId [description]
     * @return [type]         [description]
     */
    public static function getUserInfo($userId)
    {
        return self::find()->alias('a')
            ->select(['a.*', 'b.item_name'])
            ->leftJoin(AuthAssignment::tableName() . ' as b', 'b.user_id = a.id')
            ->where('a.id = ' . intval($userId))
            ->asArray()
            ->one();
    }

    /**
     *  关联用户角色表
     */
    public function getAuthAssignment()
    {
        // hasOne要求返回两个参数 第一个参数是关联表的类名 第二个参数是两张表的关联关系
        // 这里uid是auth表关联id, 关联user表的uid id是当前模型的主键id
        return $this->hasOne(AuthAssignment::className(), ['user_id' => 'id']);
    }

}
