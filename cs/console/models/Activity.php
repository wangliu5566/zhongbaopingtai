<?php

namespace console\models;

use Yii;

/**
 * This is the model class for table "{{%activity}}".
 *
 * @property int $id
 * @property string $name
 * @property string $intro 大赛简介
 * @property string $bigImage
 * @property string $reviewStandard
 * @property string $questionAnswerDetail
 * @property int $type 1:活动 2：大赛
 * @property int $activityStartTime
 * @property int $activityEndTime
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
     * 已结束活动
     * @return [type] [description]
     */
    public function queryEndActive($notInIds)
    {
        $query = self::find()->select(['id', 'type', 'userId'])
            ->where(['status' => ACTIVE_STATUS['end']]);

        if($notInIds){
            $query->andWhere(['not in', 'id', $notInIds]);
        }

        return $query->all();
    }

    /**
     * 根据时间查询未开始活动
     * @return [type] [description]
     */
    public function getInitActive()
    {
        return self::find()->select('GROUP_CONCAT(id) as ids')
            ->where(['<=', 'activityStartTime', time()])
            ->andWhere(['auditState' => ACTIVITY_AUDITSTATE['pass']])
            ->andWhere(['status' => ACTIVE_STATUS['init']])
            ->asArray()
            ->one();
    }

    /**
     * 根据时间查询已开始的活动
     * @return [type] [description]
     */
    public function getStartedActive()
    {
        return self::find()->select('GROUP_CONCAT(id) as ids')
            ->where(['<=', 'activityEndTime', time()])
            ->andWhere(['status' => ACTIVE_STATUS['started']])
            ->asArray()
            ->one();
    }

}
