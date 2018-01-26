<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property int $isDeled 用户是否删除
 * @property string $access_token
 * @property string $email
 * @property string $bigAvatar 大头像
 * @property string $middleAvatar
 * @property string $smallAvatar
 * @property string $credit 信用
 * @property string $balance 余额
 * @property int $regTime
 * @property string $realName
 * @property string $sex
 * @property string $city
 * @property string $province
 * @property string $address
 * @property string $birthday
 * @property string $qq
 * @property string $domain
 * @property string $nickName 昵称
 * @property string $cardNumber
 * @property string $roles
 * @property int $level 等级
 * @property string $integration 积分
 * @property string $phone
 * @property string $detailAddress
 * @property string $zipcode 邮编
 * @property int $isCheckedEmail
 * @property int $isSigned
 * @property int $isTeacher 是否老师（0：不是;    1：是）
 * @property int $studentId 学号
 * @property string $department 院系
 * @property string $weChat 微信号
 * @property string $briefIntro
 * @property string $registerDomain
 * @property string $weibo 微博
 * @property int $status
 * @property string $subject 学科
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
            [['username', 'password', 'access_token'], 'required'],
            [['isDeled', 'regTime', 'level', 'integration', 'isCheckedEmail', 'isSigned', 'isTeacher', 'studentId', 'status'], 'integer'],
            [['balance'], 'number'],
            [['sex'], 'string'],
            [['username', 'password', 'nickName'], 'string', 'max' => 64],
            [['access_token', 'address', 'birthday', 'detailAddress'], 'string', 'max' => 100],
            [['email', 'weChat', 'weibo'], 'string', 'max' => 128],
            [['bigAvatar', 'middleAvatar', 'smallAvatar', 'department', 'registerDomain'], 'string', 'max' => 256],
            [['credit'], 'string', 'max' => 32],
            [['realName', 'qq'], 'string', 'max' => 16],
            [['city', 'province', 'cardNumber', 'roles', 'phone', 'zipcode'], 'string', 'max' => 20],
            [['domain'], 'string', 'max' => 10],
            [['briefIntro'], 'string', 'max' => 1024],
            [['subject'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'isDeled' => 'Is Deled',
            'access_token' => 'Access Token',
            'email' => 'Email',
            'bigAvatar' => 'Big Avatar',
            'middleAvatar' => 'Middle Avatar',
            'smallAvatar' => 'Small Avatar',
            'credit' => 'Credit',
            'balance' => 'Balance',
            'regTime' => 'Reg Time',
            'realName' => 'Real Name',
            'sex' => 'Sex',
            'city' => 'City',
            'province' => 'Province',
            'address' => 'Address',
            'birthday' => 'Birthday',
            'qq' => 'Qq',
            'domain' => 'Domain',
            'nickName' => 'Nick Name',
            'cardNumber' => 'Card Number',
            'roles' => 'Roles',
            'level' => 'Level',
            'integration' => 'Integration',
            'phone' => 'Phone',
            'detailAddress' => 'Detail Address',
            'zipcode' => 'Zipcode',
            'isCheckedEmail' => 'Is Checked Email',
            'isSigned' => 'Is Signed',
            'isTeacher' => 'Is Teacher',
            'studentId' => 'Student ID',
            'department' => 'Department',
            'weChat' => 'We Chat',
            'briefIntro' => 'Brief Intro',
            'registerDomain' => 'Register Domain',
            'weibo' => 'Weibo',
            'status' => 'Status',
            'subject' => 'Subject',
        ];
    }

    /**根据token获取用户id
     * @param $access_token
     * @return mixed|null
     */
    public static function getUserId($access_token)
    {
        if(!$access_token) {
            return null;
        }
        $user = self::find()->select(['id'])->where(['access_token' => $access_token])->one();
        if(!$user) {
            return null;
        }
        return $user->id;
    }

}
