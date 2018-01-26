<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property int $id
 * @property string $title 新闻标题
 * @property string $username 发布新闻的人
 * @property int $userId 发布新闻者的id
 * @property int $activityId 所属活动的id
 * @property int $publishTime 发布新闻的时间
 * @property string $newsSummary 新闻概要
 * @property string $coverPath 封面路径
 * @property string $mainBody 正文
 * @property int $status 1:发布的新闻 2:保存的新闻(未发布)
 * @property int $headImage 1:发布的新闻 2:保存的新闻(未发布)
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'username', 'userId'], 'required'],
            [['userId', 'activityId', 'publishTime','status'], 'integer'],
            [['mainBody'], 'string'],
            [['title', 'coverPath'], 'string', 'max' => 100],
            [['username'], 'string', 'max' => 15],
            [['newsSummary'], 'string', 'max' => 255],
            [['id'], 'required', 'on' => ['delete','modify']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'username' => 'Username',
            'userId' => 'User ID',
            'activityId' => 'Activity ID',
            'publishTime' => 'Publish Time',
            'newsSummary' => 'News Summary',
            'coverPath' => 'Cover Path',
            'mainBody' => 'Main Body',
            'status' => 'Status',
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            'update'=>['id','title', 'username', 'userId', 'activityId','newsSummary','coverPath','mainBody','status','headImage'],//在该场景下的属性进行验证，其他场景和没有on的都不会验证
            'create'=>['title', 'username', 'userId', 'activityId','newsSummary','coverPath','mainBody','status','headImage'],
            'delete'=>['id'],
            'modify'=>['id'],
        ];
    }

    /**
     *  关联activity表
     */
    public function getActivity()
    {
        // hasOne要求返回两个参数 第一个参数是关联表的类名 第二个参数是两张表的关联关系
        // 这里uid是auth表关联id, 关联user表的uid id是当前模型的主键id
        return $this->hasOne(Activity::className(), ['id' => 'activityId']);
    }

    /**获取新闻数据
     * @param $start
     * @param $length
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public static function getRecentNews($start,$length)
    {
        if(!$length) {
            return null;
        }
        $news = self::find()
            ->select(['id','title','coverPath','newsSummary','publishTime'])
            ->where(['status' => NEWS_STATUS['publish']])
            ->orderBy('publishTime DESC')
            ->offset($start)->limit($length)->all();
        return $news;
    }

    /**获取新闻详情
     * @param $id
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public static function getDetail($id)
    {
        if(empty($id)) {
            return null ;
        }
        $id = intval($id);
        $data = self::find()
            ->alias('n')
            ->select(['n.*','a.pcImage','a.mobileImage'])
            ->leftJoin(Activity::tableName().'as a','a.id = n.activityId')
            ->where(['n.id' => $id])
            ->asArray()
            ->all();
        /*$query = clone $data;
        $sql = $query->createCommand()->getRawSql();*/
        return $data;
    }

}
