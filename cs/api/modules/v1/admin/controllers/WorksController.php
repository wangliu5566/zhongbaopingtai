<?php

namespace api\modules\v1\admin\controllers;

use api\models\WorkFilesDetail;
use common\helpers\Office2pdf;
use common\helpers\Thumbnail;
use Yii;
use api\controllers\BaseAPIAuthController;
use api\models\Activity;
use api\models\Works;
use api\models\Prize;
use api\models\WorksFile;
use api\models\WorksPrize;
use common\helpers\Helper;
use common\helpers\Unzip;

class WorksController extends BaseAPIAuthController
{

	public function actions()
	{
		return [];
	}

	/**
	 * 查询作品类型
	 * @return [type] [description]
	 */
	public function actionWorkType()
	{	
		$activeId = Yii::$app->request->get('activeId');
		if(!$activeId){
			return Helper::formatJson(1001, '活动ID不能为空');
		}

		$list = Works::getWorksType($activeId);

		return Helper::formatJson(200, 'Ok', $list);
	}

	/**
	 * 查询参赛作品列表
	 * @return [type] [description]
	 */
	public function actionList()
	{
		$pageSize = Yii::$app->request->get('pageSize');
		$activeId = Yii::$app->request->get('activeId');
		$status = Yii::$app->request->get('status');
		$type = Yii::$app->request->get('type');
		$where = array();

		if(!$activeId){
			return Helper::formatJson(1001, '活动ID不能为空');
		}

		if($status){
			$where['status'] = $status;
		}

		if($type){
			$where['type'] = $type;
		}

		$worksModel = new Works();
		$where['activityId'] = intval($activeId);
		$list = $worksModel->getListByCondition($where, $pageSize);
		$activeInfo = Activity::find()->select(['name'])->where('id = ' . intval($activeId))->one();
		$list['title'] = $activeInfo->name;

		return Helper::formatJson(200, 'Ok', $list);
	}

	/**
	 * 查询作品详情
	 * @return [type] [description]
	 */
	public function actionInfo()
	{

		$workId = Yii::$app->request->get('workId');
		$status = Yii::$app->request->get('status');
		if(!$workId){
			return Helper::formatJson(1001, '作品ID不能为空');
		}

		$info = Works::getWorksInfo($workId);
		//$info['workFiles'] = WorksFile::getWorksFileByWorkId($workId);
        if($status == REVIEW_SKIM['skim']){
            return Helper::formatJson(200, 'Ok', $info);
        }else{
            //审核,首先检查是否解压到workFile里面
            if(!empty($workFilesData = WorksFile::find()->where(['workId'=>$workId])->all())){
                $info['workFiles'] = $workFilesData;
                //已经解压到文件夹
                return Helper::formatJson(200, 'Ok', $info);
            }else{
                //没有解压.先解压,再返回数据
				if(isset($info['status']) && $info['status'] == WORK_STATUS['init']){
					$unZip = new Unzip();

					$r = $unZip->unzip(Yii::$app->basePath.'\web'.$info['workSite']);

                    if(is_array($rArray = pathinfo($r))){
                        $fileName = substr($rArray['dirname'],strpos($rArray['dirname'],'\data\\')).'\\'.$rArray['basename'];
                    }else{
                        return Helper::formatJson(1003, '解压出错,请联系管理员', '');
                    }
					//解压完毕,读取里面文件,写入数据库,返回
					$error = $this->reviewWorks($fileName,$info['id']);
                    if($error) {
                        return Helper::formatJson(1003, '文件入库失败');
                    }
					$workFilesData = WorksFile::find()->where(['workId' => $workId])->all();
					$info['workFiles'] = $workFilesData;
					return Helper::formatJson(200, 'Ok', $info);
				}else{
					return Helper::formatJson(1003, '数据库数据有误,请联系管理员', '');
				}

            }
        }

	}


    /**将解压文件入库
     * @param $workSite
     * @param $workId
     * @return array
     */
    public function reviewWorks($workSite,$workId){
        $workId = intval($workId);
        $zipFileDetail  = Yii::$app->basePath.'\\web'.$workSite;
        $file = pathinfo($zipFileDetail);
        $baseUrl = $file['dirname'].'\\'.$file['filename'];

        if(!file_exists($zipFileDetail)){
            return Helper::formatJson(1004,'文件不存在');
        }
        // $zipFileDetail = $file['dirname'].'\\'.$file['filename'];
        //遍历文件夹下面的文件
        //$result = $this->read_all_dir($zipFileDetail);
        $fileDetail = pathinfo($workSite);
        $result = $this->scan_dir($file['dirname'].'\\'.$file['filename']);
        $returnData = [];
        $picNum = 0;
        $picSize = 0;
        $videoNum = 0;
        $videoSize = 0;
        $otherNum = 0;
        $otherSize = 0;
        $transaction = \Yii::$app->db->beginTransaction();
        $error = [];
            foreach($result as $res){
                if( $fileType = Helper::getFileType($res)){
                    $input_url = $baseUrl.'\\'.$res;
                    $worksFile = new WorksFile();
                    switch ($fileType) {
                        case FILE_TYPE['pic_file']:
                            //图片文件
                            $picNum += 1;
                            $picSize += filesize($input_url);
                            break;
                        case FILE_TYPE['video_file']:
                            //视频文件
                            $videoNum += 1;
                            $videoSize += filesize($input_url);
                            break;
                        case FILE_TYPE['other_file']:
                            $otherNum += 1;
                            $otherSize += filesize($input_url);
                            //如果文件是office文件,转成pdf保存在数据库
                            $pdf = new Office2pdf();
                            $res = substr($res,0,strrpos($res,'.')).'.pdf';
                            $output_url = $baseUrl.'\\'.$res;
                            $msg = $pdf->run($input_url,$output_url);
                            if(!$msg) $error[] = '文件'.$res.'转PDF失败.';
                            //生成pdf首页缩略图
                            $pdfImage = Helper::pdf2png($output_url,$baseUrl);
                            if($pdfImage) {
                                $worksFile->pdfImage = iconv( "GB2312","UTF-8", $fileDetail['dirname'].'\\'.$fileDetail['filename'].'\\'.$pdfImage);
                            }else {
                                $error[] = '文件'.$res.'生成缩略图失败.';
                            }
                            break;
                        case 4:
                            //本身是pdf文件
                            $fileType = FILE_TYPE['other_file'];
                            $otherNum += 1;
                            $otherSize += filesize($input_url);
                            $pdfImage = Helper::pdf2png($input_url,$baseUrl);
                            if($pdfImage) {
                                $worksFile->pdfImage = iconv( "GB2312","UTF-8", $fileDetail['dirname'].'\\'.$fileDetail['filename'].'\\'.$pdfImage);
                            }else {
                                $error[] = '文件'.$res.'生成缩略图失败.';
                            }
                            break;
                    }
                    $worksFile->detail = iconv( "GB2312","UTF-8", $fileDetail['dirname'].'\\'.$fileDetail['filename'].'\\'.$res);
                    $worksFile->workId = $workId;
                    $worksFile->type = $fileType;
                    $returnData[] = $worksFile->detail;
                    $msg = $worksFile->save();
                    if(!$msg) $error[] = '文件'.$res.'入库失败.';
                }
            }
            //记录压缩文件各文件信息
            $workFileDetail = new WorkFilesDetail();
            $workFileDetail->workId = $workId;
            $workFileDetail->picNum = $picNum;
            $workFileDetail->picSize = $picSize;
            $workFileDetail->videoNum = $videoNum;
            $workFileDetail->videoSize = $videoSize;
            $workFileDetail->otherNum = $otherNum;
            $workFileDetail->otherSize = $otherSize;
            $workFileDetail->totalNum = $picNum + $videoNum + $otherNum;
            $workFileDetail->totalSize = $picSize + $videoSize + $otherSize;
            $msg = $workFileDetail->save();
            if(!$msg) $error[] = '文件汇总失败.';
            if(!$error) {
                $transaction->commit();
            }else {
                $transaction->rollBack();
            }
        return $error;
    }

	public function scan_dir($dir, $filter = array()){
		if(!file_exists($dir))return false;
		$file = pathinfo($dir);

		$dir = $file['dirname'].'\\'.$file['filename'];
		$files = array_diff(scandir($dir), array('.', '..'));

		if(is_array($files)){
			foreach($files as $key=>$value){
				if(is_dir($dir . '/' . $value)){
					$data = $this->scan_dir($dir . '/' . $value, $filter);

					foreach($data as $da){
						$files[] = $value.'/'.$da;
					}
					unset($files[$key]);
					continue;
				}
				$pathinfo = pathinfo($dir . '/' . $value);

				$extension = array_key_exists('extension', $pathinfo) ? $pathinfo['extension'] : '';

				if(!empty($filter) && !in_array($extension, $filter)){
					unset($files[$key]);
				}
			}
		}
		unset($key, $value);
		return $files;
	}

	/**
	 * 审核作品
	 * @return [type] [description]
	 */
	public function actionAudit()
	{
	    $request = Yii::$app->request;
	    if(!$request->isPost) {
            Helper::formatJson(1003, '请求方式错误');
        }
		$workIds = $request->post('workId',[]);
        $status = $request->post('status',0);
        $activityId = $request->post('activityId',0);
		if(!$workIds && !$status && !$activityId){
			return Helper::formatJson(1001, '参数不正确');
		}
        $activity = Activity::find()->where(['id' => intval($activityId),'status' => ACTIVITY_STATUS['started']])->one();
        if(!$activity) {
            return Helper::formatJson(1008, '活动已结束');
        }
		if(!is_array($workIds)){
			Helper::formatJson(1005, '作品ID格式错误');
		}
        //查询到所有活动id
        $allWoksId = WorksFile::find()->select('workId')->distinct()->asArray()->all();
        $ids = array_column($allWoksId, 'workId');
        //拿到已经解压的活动文件id
        $interData = array_intersect($ids,$workIds);
        //拿到传进来的文件id与已经解压的文件id的差集
        $diffData = array_diff($workIds,$interData);
        if(empty($diffData)){
            $res = Yii::$app->db->createCommand()->update(Works::tableName(), ['status' => $status], 'id in (' . implode(',', $workIds) . ')')->execute();
            $result = Activity::updateWorkSCount($activity,count($workIds));
            if($res && $result) {
                return Helper::formatJson(200, 'Ok');
            }else {
                return Helper::formatJson(1007, '保存失败');
            }
        }else{
            $count = count($diffData);
            if($interData) {
                $res = Yii::$app->db->createCommand()->update(Works::tableName(), ['status' => $status], 'id in (' . implode(',', $interData) . ')')->execute();
                $result = Activity::updateWorkSCount($activity,count($interData));
                if($res && $result) {
                    return Helper::formatJson(1004, '还有'.$count.'条未成功,原因为未查看或者审核',$diffData);
                }else {
                    return Helper::formatJson(1007, '保存失败');
                }
            }
            return Helper::formatJson(1004, '还有'.$count.'条未成功,原因为未查看或者审核',$diffData);
        }


	}

	/**
	 * 奖项管理
	 * @return [type] [description]
	 */
	public function actionPrizeList()
	{
		$activeId = Yii::$app->request->get('activeId');
		$prize = Yii::$app->request->get('prize');
		$type = Yii::$app->request->get('type');
		$sort = Yii::$app->request->get('sort', 'hits');
		$keyword = Yii::$app->request->get('keyword');
		$pageSize = Yii::$app->request->get('pageSize');

		$where = array();
		$orWhere = array();

		if($prize){
			$where['prize'] = $prize;
		}

		if($type){
			$where['a.type'] = $type;
		}

		if(isset($keyword)){
			$orWhere = array(
				array('like', 'workName', $keyword), 
				array('like', 'username', $keyword)
			);
		}

		if(!$activeId){
			return Helper::formatJson(1001, '活动ID不能为空');
		}

		$worksModel = new Works();
		$where['activityId'] = $activeId;
		$where['status'] = WORK_STATUS['pass'];
		$list = $worksModel->getWorkList($sort, $where, $orWhere, $pageSize);

		return Helper::formatJson(200, 'Ok', $list);
	}

	/**
	 * 获取活动奖项
	 * @return [type] [description]
	 */
	public function actionActivePrize()
	{
		$activeId = Yii::$app->request->get('activeId');
		$workId = Yii::$app->request->get('workId');
		if(!$workId){
			return Helper::formatJson(1001, '作品ID不能为空');
		}

		if(!$activeId){
			return Helper::formatJson(1001, '活动ID不能为空');
		}

		$setPrizeIds = array();
		$worksPrize = WorksPrize::getPrizeList($workId);
		if($worksPrize){
			foreach($worksPrize as $key => $prize){
				$setPrizeIds[] = $prize['prizeId'];
			}
		}

		$workInfo = Works::findOne(['id' => $workId]);
		$list = Prize::getListByActiveId($activeId, $workInfo->type);
		if($list){
			foreach($list as $key => $info){
				if(in_array($info['id'], $setPrizeIds)){
					$list[$key]['isset'] = 1;
				}else{
					$list[$key]['isset'] = 0;
				}
			}
		}

		return Helper::formatJson(200, 'Ok', $list);
	}

	/**
	 * 给作品设置奖项
	 * @return [type] [description]
	 */
	public function actionSetPrize()
	{
		$workId = Yii::$app->request->post('workId');
		$prizeId = Yii::$app->request->post('prizeId');

		if(!$workId){
			return Helper::formatJson(1001, '作品ID不能为空');
		}

		if(!$prizeId){
			return Helper::formatJson(1001, '奖品ID不能为空');
		}

		$workInfo = Works::findOne(['id' => $workId]);
		$prizeInfo = Prize::findOne(['id' => $prizeId]);

		if(!WorksPrize::findOne(['workId' => intval($workId), 'prizeId' => intval($prizeId)])){
			if($prizeInfo->winners + 1 > $prizeInfo->totalPeople){
				return Helper::formatJson(1005, '该奖项名额已满');
			}
		}

		/*设置奖项*/
		$worksPrize = new WorksPrize();
		if($worksPrize->setPrize($workId, $prizeId)){
			/*更新作品设置奖项状态*/
			if($worksPrize->isSetPrize($workId)){
				$workInfo->prize = 2;
			}else{
				$workInfo->prize = 1;
			}
			$workInfo->save();

			/*更新奖项表*/
			$count = $worksPrize->getCountWorks($prizeId);
			$prizeInfo->winners = $count;
			$prizeInfo->save();
		}

		return Helper::formatJson(200, 'Ok');
	}







    public function actionUploadCoverImage(){
        if(Yii::$app->request->isPost){
            $image = Yii::$app->request->post('coverPath') ?? '';
            $workId = Yii::$app->request->post('workId') ?? '';
            $fileInfo = pathinfo($image);
            if(!empty($workId) && !empty($image)){
                $thumbnail = new Thumbnail('280','190');
                $image = iconv( "UTF-8","GB2312", $image);
                $thumbImage =  $thumbnail->makeThumb(\Yii::$app->basePath.'\web'.$image,\Yii::$app->basePath.'\web'.$fileInfo['dirname']);
                //缩率图生成完成,保存到作品表
                $thumbImage = iconv( "GB2312","UTF-8", $thumbImage);
                $work = Works::findOne($workId);
                $work->coverPath = $fileInfo['dirname'].'\\'.$thumbImage;
                $res = $work->save();
                if($res){
                    return Helper::formatJson(200);
                }else{
                    return Helper::formatJson(1002);
                }
            }else{
                return Helper::formatJson(1001,'缺少数据');
            }


        }else{
            return Helper::formatJson(1003);
        }


    }

	public function actionTest(){
		global $i;
		$linenums =  $this->actionCodeLineNum('D:\phpStudy\WWW\ceshi\micro_course_school\code\webroot' ,$i);
		echo '代码总行数为：' . $linenums;
	}

	public function actionCodeLineNum($path, $i) {
		if (!is_dir($path)) {
			return false;
		}
		$files = glob($path . '/*');
		if ($files) {
			foreach ($files as $file) {
				if (is_dir($file)) {
					$this->actionCodeLineNum($file, $i);
				}
				$buffer = '';
				$handle = @fopen($file, 'r');
				if ($handle) {
					while(!feof($handle)) {
						$buffer = fgets($handle,4096);
						$buffer = trim($buffer);	//同等于==$buffer = str_replace("\r\n", '', $buffer);
						if (!empty($buffer)) {
							$comments = array();
							$comments[0]  = '';
							$comments[0] .= preg_match('/\/\//i', $buffer) ? '####' : '';
							$comments[0] .= preg_match('/\/\*\*/i', $buffer) ? '####' : '';
							$comments[0] .= preg_match('/\*\s/i', $buffer) ? '####' : '';
							$comments[0] .= preg_match('/\*\//i', $buffer) ? '####' : '';
							if (empty($comments[0])) {
								global $i;
								$i++;
							}
						}
					}
					fclose($handle);
				}
			}
		}
		return $i;
	}

}