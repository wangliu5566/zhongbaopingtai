<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%clicks}}".
 *
 * @property string $id
 * @property string $primaryId 关联表ID
 * @property int $type 类型: 1 活动 2 图片 3 视频 4 其他
 * @property string $clickTime 点击时间
 */
class Clicks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%clicks}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['primaryId', 'type', 'clickTime'], 'required'],
            [['primaryId', 'type', 'clickTime'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'primaryId' => 'Primary ID',
            'type' => 'Type',
            'clickTime' => 'Click Time',
        ];
    }

    /**保存点击数据
     * @param $primaryId
     * @param $type
     * @return bool
     */
    public static function saveInfo($primaryId,$type)
    {
        $clickModel = new self();
        $clickModel->primaryId = intval($primaryId);
        $clickModel->type = intval($type);
        $clickModel->clickTime = time();
        return $clickModel->save();
    }

    /**
     * 作品点击量统计
     * @param $gran
     * @param $startTime
     * @param $endTime
     */
    public function getWorksClick($gran, $startTime, $endTime)
    {
        $timeArr = array();
        if($gran == 'today' || $gran == 'yesterday'){
            $format = '%H:00';
            for($i = $startTime; $i < $endTime; $i += 3600){
                $timeArr[] = date('H:i', $i);
            }
        }elseif($gran == 'week' || $gran == 'month'){
            $format = '%Y-%m-%d';
            for($i = $startTime; $i < $endTime; $i += 86400){
                $timeArr[] = date('Y-m-d', $i);
            }
        }

        $list = self::find()->select(['count(id) as click', 'type', 'FROM_UNIXTIME(clickTime, "' . $format . '") as time'])
            ->where(['>=', 'clickTime', $startTime])
            ->andWhere(['<', 'clickTime', $endTime])
            ->groupBy(['FROM_UNIXTIME(clickTime, "' . $format . '")', 'type'])
            ->asArray()
            ->all();

        $rows = array();
        $total = 0;
        $rowsIndex = array();
        if($list){
            $arr = array();
            foreach($list as $v){
                $arr[$v['time']][] = $v;
            }

            foreach($arr as $key => $value){
                $tr = array();
                $tr['total'] = 0;
                $tr['time'] = $key;
                $tr['image'] = 0;
                $tr['video'] = 0;
                $tr['other'] = 0;

                foreach($value as $val){
                    if($val['type'] == CLICK_TYPE['pic_click']){
                        $tr['image'] = intval($val['click']);
                    }

                    if($val['type'] == CLICK_TYPE['video_click']){
                        $tr['video'] = intval($val['click']);
                    }

                    if($val['type'] == CLICK_TYPE['other_click']){
                        $tr['other'] = intval($val['click']);
                    }

                    if($val['type'] != CLICK_TYPE['act_click']){
                        $tr['total'] += intval($val['click']);
                        $total += intval($val['click']);
                    }
                }
                $rows[$key] = $tr;
            }

            $rowsIndex = array_keys($rows);
        }

        foreach($timeArr as $item){
            if(!in_array($item, $rowsIndex)){
                $data = array();
                $data['total'] = 0;
                $data['time'] = $item;
                $data['image'] = 0;
                $data['video'] = 0;
                $data['other'] = 0;
                $rows[$item] = $data;
            }
        }
        krsort($rows);
        $table = array_values($rows);

        $x = array();
        $yImage = array();
        $yVideo = array();
        $yOther = array();

        foreach($table as $v){
           $x[] = $v['time'];
           $yImage[] = $v['image'];
           $yVideo[] = $v['video'];
           $yOther[] = $v['other'];
        }

        return array('total' => $total, 'table' => $table, 'rows' => array('x' => $x, 'yImage' => $yImage, 'yVideo' => $yVideo, 'yOther' => $yOther));
    }
}