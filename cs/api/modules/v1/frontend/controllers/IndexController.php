<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/7/27
 * Time: 16:22
 */

namespace api\modules\v1\frontend\controllers;


use api\controllers\BaseAPIAuthController;
use api\models\Activity;
use api\models\News;
use api\models\Signup;
use api\models\UserScore;
use common\helpers\Helper;
use dosamigos\qrcode\QrCode;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class IndexController extends BaseAPIAuthController
{
    /**偏移量
     * @var int
     */
    private $start = 0;
    private $length = -1;
    //默认获取活动状态为正在进行和已结束
    private $activityStatus = '1,2,3';
    //默认获取活动类型为大赛和问答
    private $activityType = '1,2';
    //默认页数
    private $page = 1;

    /**活动列表
     * @return array
     */
    public function actionGetActivity()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $page = $request->get('page',$this->page);
        $length = $request->get('length',$this->length);
        $status = empty($request->get('status')) ? $this->activityStatus : intval($request->get('status'));
        $type = empty($request->get('type')) ? $this->activityType : intval($request->get('type'));
        $status = explode(',',$status);
        $type = explode(',',$type);
        $userId = $request->get('userId',0);
        $isMine = $request->get('isMine',0);
        /*首页活动数据*/
        $data = Activity::getRecentActivities($page,$length,$status,$type,$userId,$isMine);
        return Helper::formatJson(200,'OK',[
            'activities' => $data['activities'],
            'activityCount' => $data['count']]);
    }


    /**新闻列表
     * @return array
     */
    public function actionGetNews()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $start = $request->get('start') ?? $this->start;
        $length = $request->get('length') ?? $this->length;
        $newsCount = News::find()->where(['status' => NEWS_STATUS['publish']])->count();
        /*首页新闻*/
        $news = News::getRecentNews($start,$length);
        return Helper::formatJson(200,'OK',[
            'news' => $news,
            'newsCount' => $newsCount]);
    }

    /**轮播图
     * @return array
     */
    public function actionGetPictures()
    {
        $request = \Yii::$app->request;
        if(!$request->isGet) {
            return Helper::formatJson(1003,'请求方式错误');
        }
        $userId = $request->get('userId',0);
        $pictures = Activity::getRecentPictures();
        foreach ($pictures as &$picture) {
            $signStatus = SIGN_STATUS['not_sign'];
            if($userId) {
                $isSignUp = Signup::find()
                    ->where(['userId' => $userId,'activityId' => $picture['id']])
                    ->one();
                if($isSignUp) {
                    $signStatus = $isSignUp->status;
                    if($picture['type'] == ACTIVITY_TYPE['question']) {
                        $isSend = UserScore::find()->where(['userId' => $userId,'activityId' => $picture['id']])->one();
                        if($isSend) {
                            $signStatus = SIGN_STATUS['isSend'];
                        }
                    }
                }
            }
            $picture['signStatus'] = $signStatus;
        }
        return Helper::formatJson(200,'OK',$pictures);
    }


    /**
     * 导数据
     */
    /*public function actionTest()
    {
        $connection = new \yii\db\Connection([
            'dsn' => 'mysql:host=123.57.71.131;dbname=pheicourse_develop;port=3307',
            'username' => 'root',
            'password' => '123456',
        ]);
        $connection->open();
        $coursesSql = 'select id ,name from hawk_course where id = 182';
        $command = $connection->createCommand($coursesSql);
        $courses = $command->queryAll();
        //遍历所有课程ID
        foreach ($courses as $course) {
            $id = intval($course['id']);
            //获取课程名称
            $courseName = $course['name'];
            $where = 'where a.courseId=' . $id;
            $sql = 'select a.name,a.id,a.sn,a.parentId,q.videoFileSrc,
              CASE  WHEN a.intro is NULL then "无"
                when a.intro is not NULL then "已有" END as intro,a.id as sectionId,a.parentId,
                CASE  WHEN a.resourceId>0 then "已上传"
                ELSE "无" END as resourceId,
                b.id as `co`,
                q.materialFileIds as materialQuantity,
                CASE WHEN q.materialQuantity IS NULL THEN 0 ELSE q.materialQuantity END as otherFile,
               	CASE WHEN g.status IS NULL THEN 0 ELSE g.status END as introError,
               	CASE WHEN h.status IS NULL THEN 0 ELSE h.status END as knowledgeError,
               	CASE WHEN i.status IS NULL THEN 0 ELSE i.status END as resourceError,
               	CASE WHEN j.status IS NULL THEN 0 ELSE j.status END as testError,
               	CASE WHEN k.status IS NULL THEN 0 ELSE k.status END as digitalError,
                count(DISTINCT c.knowledgePointId ) as knowledgeNum
                ,count(distinct e.testQuestionId ) as testQuestionNum
                from hawk_courseplanitem as a
                LEFT JOIN hawk_resourcecategory as b on a.id=b.categoryId
                LEFT JOIN (select * from hawk_resknprelate where resType=18 ) as c on c.resourceId=a.resourceId
                LEFT JOIN (select * from hawk_resquestrelated where resourceType=18 ) as e on e.resourceId=a.resourceId
                LEFT JOIN (select * from hawk_audit where introId=1 or introId=2  ) as g on g.sectionId=a.id
                LEFT JOIN (select * from hawk_audit where introId=3  ) as h on h.sectionId=a.id
                LEFT JOIN (select * from hawk_audit where introId=4  ) as i on i.sectionId=a.id
                LEFT JOIN (select * from hawk_audit where introId=5  ) as j on j.sectionId=a.id
                LEFT JOIN (select * from hawk_audit where introId=6  ) as k on k.sectionId=a.id
                LEFT JOIN hawk_resource as q on q.id=a.resourceId
                ' . $where . '
                group by a.id order by a.sn,a.id
                ';
            $command = $connection->createCommand($sql);
            $lists = $command->queryAll();
//            var_dump($lists);exit;
            if (!empty($lists)) {
                $courseList = $this->getChild3($html, 0, $lists, 1);
//                var_dump($courseList);exit;
                $data = ['courseName' => $courseName, 'courseList' => $courseList];
                $this->actionRunWord($data);
                echo $courseName . '导出完成<br/>';
            } else {
                continue;
            }
        }
    }*/

    /*private function getChild3(&$html,$parid,$channels,$dep){
        for($i = 0;$i < count($channels); $i ++){
            if($channels[$i]['parentId'] == $parid){
                $html[] = array(
                    'id' => $channels[$i]['id'],
                    'name' => $channels[$i]['name'],
                    'sn' => $channels[$i]['sn'],
                    'parentId' => $channels[$i]['parentId'],
                    'intro' => $channels[$i]['intro'],
                    'sectionId' => $channels[$i]['sectionId'],
                    'resourceId' => $channels[$i]['resourceId'],
                    'co' => $channels[$i]['co'],
                    'videoFileSrc' => $channels[$i]['videoFileSrc'],
                    'dep' => $dep);
                $this->getChild3($html,$channels[$i]['id'],$channels,$dep+1);
            }
        }
        return $html;
    }*/

    /*private function actionRunWord($data)
    {
        $PHPWord = new PhpWord();
        $section = $PHPWord->addSection();
        $PHPWord->addFontStyle('rStyle', array('bold'=>true,'color'=>'000000','size'=>16));
        $PHPWord->addParagraphStyle('pStyle', array('align'=>'center'));
        $section->addText($data['courseName'], 'rStyle', 'pStyle');
        $section->addTextBreak(2);
        $styleTable = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>80);
        $PHPWord->addTableStyle('myOwnTableStyle', $styleTable);
        $table = $section->addTable('myOwnTableStyle');
        $fontStyle = array('bold'=>true, 'align'=>'center');
        $cellStyle = array('borderColor'=>'006699', 'borderRightSize'=>6);
        foreach ($data['courseList'] as $item) {
            $table->addRow();
            if($item['parentId'] == 0) {
                $table->addCell(3333)->addText($item['name'],$fontStyle);
                $table->addCell(3333)->addText('',$fontStyle);
                $table->addCell(3334)->addText('',$fontStyle);
            }else {
                if(substr($item['videoFileSrc'],0,1) == '/') {
                    $item['videoFileSrc'] = 'http://pheicourse.jqweike.com'.$item['videoFileSrc'];
                }
                $imageName = $this->actionQrCode($item['videoFileSrc']);
                $table->addCell(3333,$cellStyle)->addText($item['name'],$fontStyle);
                $table->addCell(3333,$cellStyle)->addText($item['videoFileSrc'],$fontStyle);
                $table->addCell(3334)->addImage($imageName, array('width'=>37, 'height'=>37,'align'=>'right'));
            }
        }
        $section->addTextBreak(2);
        $objWrite = IOFactory::createWriter($PHPWord, 'Word2007');
        $objWrite->save($data['courseName'].'.docx');
    }*/

    /*public function actionTestPhpWord()
    {
        $PHPWord = new PhpWord();
        $section = $PHPWord->addSection();
        $PHPWord->addFontStyle('rStyle', array('bold'=>true,'color'=>'000000','size'=>16));
        $PHPWord->addParagraphStyle('pStyle', array('align'=>'center'));
        $section->addText('×××公司招聘信息', 'rStyle', 'pStyle');
        $section->addTextBreak(2);

// Define table style arrays
        $styleTable = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>80);


// Add table style
        $PHPWord->addTableStyle('myOwnTableStyle', $styleTable);

// Add table
        $table = $section->addTable('myOwnTableStyle');
        $fontStyle = array('bold'=>true, 'align'=>'center');

// Add more rows / cells
        $table->addRow();
        $table->addCell(2000)->addText("单位名称",$fontStyle);
        $table->addCell(3000)->addText("",$fontStyle);
        $table->addCell(2000)->addText("详细地址",$fontStyle);
        $table->addCell(3000)->addText("",$fontStyle);

        $table->addRow();
        $table->addCell(2000)->addText("场所负责人",$fontStyle);
        $table->addCell(3000)->addText("",$fontStyle);
        $table->addCell(2000)->addText("联系电话",$fontStyle);
        $table->addCell(3000)->addText("",$fontStyle);

        $styleTable2 = array('borderColor'=>'006699', 'borderLeftSize'=>6,'borderRightSize'=>6,'cellMargin'=>80);
        $fontStyle2 = array('align'=>'center');
// Add table style
        $PHPWord->addTableStyle('myOwnTableStyle2', $styleTable2);
        for($i=1;$i<=5;$i++){
            $table2 = $section->addTable('myOwnTableStyle2');
            $table2->addRow();
            $table2->addCell(10000)->addText("服务岗位".$i,$fontStyle);
            $table3 = $section->addTable('myOwnTableStyle');
            $table3->addRow();
            $table3->addCell(2000)->addText("岗位内容",$fontStyle2);
            $table3->addCell(3000)->addText("",$fontStyle2);
            $table3->addCell(2000)->addText("需求数量",$fontStyle2);
            $table3->addCell(3000)->addText("",$fontStyle2);
            $table3->addRow();
            $table3->addCell(2000)->addText("服务时数",$fontStyle2);
            $table3->addCell(3000)->addText("",$fontStyle2);
            $table3->addCell(2000)->addText("服务周期",$fontStyle2);
            $table3->addCell(3000)->addText("",$fontStyle2);
        }
        $styleTable3 = array('borderColor'=>'006699', 'borderLeftSize'=>6, 'borderBottomSize'=>6,'borderRightSize'=>6,'cellMargin'=>80);
        $fontStyle3 = array('align'=>'center');
        $cellStyle3 = array('borderColor'=>'006699', 'borderRightSize'=>6);
// Add table style
        $PHPWord->addTableStyle('myOwnTableStyle3', $styleTable3);
        $table4 = $section->addTable('myOwnTableStyle3');
        $table4->addRow(2000);
        $table4->addCell(3333,$cellStyle3)->addText("本单位意见",$fontStyle3);
        $table4->addCell(3333,$cellStyle3)->addText("主管部门意见",$fontStyle3);
        $table4->addCell(3334)->addText("集团总部意见",$fontStyle3 );
//Two enter
        $section->addTextBreak(2);
//Add image
        $section->addImage('logo.jpg', array('width'=>100, 'height'=>100,'align'=>'right'));

        $objWrite = IOFactory::createWriter($PHPWord, 'Word2007');
        $objWrite->save('ceshi.docx');

    }*/

    /*private function actionQrCode($detail){
        $errorCorrectionLevel = 'L';  // 纠错级别：L、M、Q、H
        $matrixPointSize = 0.5; // 点的大小：1到10
        $imageName = './erweima/'.time().rand(1,9999).'.png';
        QrCode::png($detail,$imageName,$errorCorrectionLevel,$matrixPointSize,2);
        return $imageName;
    }*/

}