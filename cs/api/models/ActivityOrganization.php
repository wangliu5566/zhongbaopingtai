<?php

namespace api\models;

use common\helpers\Helper;
use Yii;

/**
 * This is the model class for table "{{%activity_organization}}".
 *
 * @property int $id
 * @property string $organizationName 学校名称
 * @property int $organizationId
 * @property int $activityId
 */
class ActivityOrganization extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activity_organization}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['organizationName', 'organizationId', 'activityId'], 'required'],
            [['organizationId', 'activityId'], 'integer'],
            [['organizationName'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organizationName' => 'Organization Name',
            'organizationId' => 'Organization ID',
            'activityId' => 'Activity ID',
        ];
    }

    /**判断用户是否能参赛
     * @param $activityId
     * @param $userId
     * @return bool
     */
    public static function juadeMatch($activityId,$userId)
    {
        if(!$activityId || !$userId) {
            return false;
        }
        /*先判断是否允许所有学校或用户参赛*/
        $activeInfo = Activity::findOne(['id' => intval($activityId)]);
        if($activeInfo->allowJoin == 2){
            if(User::findOne(['id' => intval($userId)])){
                return true;
            }
        }elseif($activeInfo->allowJoin == 1){
//            $userAgency = UserAgency::find()->where(['userId' => $userId])->andWhere(['!=', 'agencyId', GUEST_ID])->one();
            if(UserAgency::find()->where(['userId' => $userId])->andWhere(['!=', 'agencyId', GUEST_ID])->one()){
                return true;
            }
        }
        $agencyIds = self::find()
            ->select('organizationId as id')
            ->where(['activityId' => $activityId])
            ->asArray()
            ->all();
        if(!$agencyIds) {
            return false;
        }
        $ids = [];
        foreach ( $agencyIds as $agencyId) {
            $ids[] = $agencyId['id'];
        }
        $userAgency = UserAgency::find()->select(['agencyId'])->where(['userId' => $userId])->one();
        if(!$userAgency) {
            return false;
        }
        $agencyId = $userAgency['agencyId'];
        if(!in_array($agencyId,$ids)) {
            return false;
        }
        return true;
    }


    /**获取用户可查询活动ids
     * @param $userId
     * @return array|bool|null
     */
    public static function getAgencyActivityIds($userId)
    {
        if(!$userId) {
            return null;
        }
        $userAgency = UserAgency::find()->select(['agencyId'])->where(['userId' => $userId])->one();
        if(!$userAgency) {
            return false;
        }
        $agencyId = $userAgency['agencyId'];
        $activityIds = self::find()
            ->select('activityId as id')
            ->where(['organizationId' => $agencyId])
            ->asArray()
            ->all();
        if(!$activityIds) {
            return false;
        }
        $ids = [];
        foreach ( $activityIds as $activityId) {
            $ids[] = $activityId['id'];
        }
        return $ids;
    }

    /**
     * 根据活动id查询机构ID
     * @param $activeIds
     */
    public function getAgencyIdByActiveId($activeIds)
    {
        $row = self::find()->select('GROUP_CONCAT(organizationId) as agencyIds')
            ->where(['activityId' => $activeIds])
            ->asArray()
            ->one();

        if($row['agencyIds']){
            return $row['agencyIds'];
        }

        return false;
    }
}
