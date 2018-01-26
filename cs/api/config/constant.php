<?php

define('ACTIVITYIMAGE',__FILE__.'/../../../data/image/activityImage');/*活动图片*/

define('ACTIVITYSMALLIMAGE',__FILE__.'/../../../data/image/activitySmallImage');/*活动缩率图片*/

define('TEMPIMAGE',__FILE__.'/../../../data/image/tempImage');/*活动临时图片*/

define('ACTIVITYVIDEO',__FILE__.'/../../../data/activityVideo');/*活动视频*/

define('TEMPVIDEO',__FILE__.'/../../../data/tempVideo');//活动临时视频

/**
 * 百度统计接口
 */
define('TONGJI', [
	'username' => 'kingchannels',
	'password' => 'kh(*)2016',
	'site_id' => 11192873,
	'token' => '39feaa803335cdfcab5c7db1c28b9734',
	'uuid' => '48-4D-7E-AB-F3-3E',
	'account_type' => 1
]);

/**
 * 活动审核状态
 */
define('ACTIVITY_AUDITSTATE',[
    'pass' => 1,
    'wait' => 2,
    'fail' => 3,
    'unable' => 4,
]);

/**
 * 活动状态
 */
define('ACTIVITY_STATUS', [
	'init' => 1, //未开始
	'started' => 2, // 正在进行
	'end' => 3 //已结束
]);

/**
 * 新闻状态
 */
define('NEWS_STATUS',[
    'publish' => 1, //已发布
    'save' => 2, //仅保存
]);
/**
 * 活动类型
 * 1 大赛
 * 2 问卷
 */
define('ACTIVITY_TYPE', [
	'active' => 1, //大赛
	'question' => 2 //问答
]);

/**
 * 点赞和分享类型
 */
define('HITS_TYPE',[
    'work' => 1, //作品
    'activity' => 2 //活动
]);

/**
 * 客户端类型
 */
define('CLIENT_TYPE',[
    'pc' => 1, //PC端
    'mobile' => 2 //手机端
]);

/**
 * 作品状态
 */
define('WORK_STATUS',[
    'null' => 0,  //未完全上传作品
    'pass' => 1, //已通过
    'init' => 2, //待审核
    'fail' => 3, //未通过
]);

/**
 * 用户参赛状态
 */
define('SIGN_STATUS',[
    'not_sign' => 0, //未报名
    'init' => 1, //已报名
    'match' => 2, //已参赛
    'isSend' => 3, //已经抽奖
]);

/**
 * 游客机构ID
 */
define('GUEST_ID', 527);

/**
 * 用户参赛状态
 */
define('SCORE_FLOAT',50);

/**
 * 文件上传状态
 */
define('FILE_STATUS',[
    'fail' => 0,  //未完成
    'finish' => 1, //已完成
]);

/**
 * CRM平台
 */
define('API_DOMAIN', 'http://123.57.71.131:8001');
define('AGENCY_GUEST_ID', 527);

define('API_URL', [
    'create' => '/Customer/Insert',
    'update' => '/Customer/Update',
    'delete' => '/Customer/Delete',
]);

/**
 * GET请求无参数时使用
 */
define('GET_PARAMS', [
    'app_id' => '07892567',
    'app_key' => '274ea9812bee85eaf95e5452bdf44b0eb6d60dfe'
]);

define('SECRET', '111111');

/**
 * 文件上传状态
 */
define('REVIEW_SKIM',[
	'review' => 2,  //审核
	'skim' => 1, //浏览
]);

/**
 * 侧边栏显示
 */
define('SIDER',[
	[
	'title'=>'系统管理',
    'value'=>'system',
    'checked'=>false,
	'expand'=>false,
	'children'=>[
			[
				'title'=>'账号管理',
                'value'=>'user',
                'checked'=>false,
				'expand'=>false,
				'children'=>[],
			],
			[
				'title'=>'角色管理',
                'value'=>'permission',
                'checked'=>false,
				'expand'=>false,
				'children'=>[],
			]
		]
	],

	[
		'title'=>'内容管理',
		'value'=>'content',
        'checked'=>false,
		'expand'=>false,
		'children'=>[
			[
				'title'=>'新闻管理',
                'value'=>'news',
                'checked'=>false,
				'expand'=>false,
				'children'=>[],
			],
		]
	],

	[
		'title'=>'活动管理',
		'value'=>'management',
        'checked'=>false,
		'expand'=>false,
		'children'=>[
			[
				'title'=>'我的活动',
                'value'=>'works',
                'checked'=>false,
				'expand'=>false,
				'children'=>[],
			],
			[
				'title'=>'活动权限',
				'value'=>'activity',
				'checked'=>false,
				'expand'=>false,
				'children'=>[],
			],
		]
	],

	[
		'title'=>'数据管理',
		'value'=>'wrap',
        'checked'=>false,
		'expand'=>false,
		'children'=>[
			[
				'title'=>'流量管理',
                'value'=>'flow',
                'checked'=>false,
				'expand'=>false,
				'children'=>[],
			],
			[
				'title'=>'活动数据',
                'value'=>'data',
                'checked'=>false,
				'expand'=>false,
				'children'=>[],
			],
			[
				'title'=>'作品数据',
				'value'=>'analysis',
				'checked'=>false,
				'expand'=>false,
				'children'=>[],
			],
			[
				'title'=>'发起者列表',
                'value'=>'initiator',
                'checked'=>false,
				'expand'=>false,
				'children'=>[],
			]
		]
	],
]);


define('BAR',[
   'user'=>'system',
   'permission'=>'system',
   'news'=>'content',
   'works'=>'management',
   'activity'=>'management',
   'flow'=>'wrap',
   'data'=>'wrap',
   'initiator'=>'wrap',
]);


/**
 * 定义允许解压到文件夹的内容
 */
define('ZIPALLOW',[
		'jpg'=>1,
		'JPG'=>1,
		'jpeg'=>1,
		'JPEG'=>1,
		'png'=>1,
		'PNG'=>1,
		'gif'=>1,
		'GIF'=>1,
		'svg'=>1,
		'SVG'=>1,
		'dxf'=>1,
		'DXF'=>1,
		'mp4'=>2,
		'MP4'=>2,
		'rm'=>2,
		'rmvb'=>2,
		'RMVB'=>2,
		'wmv'=>2,
		'WMV'=>2,
		'avi'=>2,
		'AVI'=>2,
		'3gp'=>2,
		'mkv'=>2,
        'doc' => 3,
        'docx' => 3,
        'xls' => 3,
        'xlsx' => 3,
        'ppt' => 3,
        'pptx' => 3,
        'pdf' => 4,
        /*'xlsm' => 3,
        'xltx' => 3,
        'xltm' => 3,
        'xlt' => 3,
        'csv' => 3,*/

]);


define('SUPERADMIN','超级管理员');

/**
 * 点击率类型
 */
define('CLICK_TYPE',[
    'act_click' => 1, //活动点击
    'pic_click' => 2, //图片
    'video_click' => 3, //视频
    'other_click' => 4, //其他
]);

/**
 * 文件类型
 */
define('FILE_TYPE',[
    'pic_file' => 1,
    'video_file' => 2,
    'other_file' => 3,
]);

/**
 * 活动是否下架
 */
define('ACTIVITY_IS_DELETED',[
    'no' => 0,
    'yes' => 1,
]);

/**
 * 积分系统API
 */

define('POINT_API',[
    'addUser' => 'http://api.toolsets.cn/Qiao/user/add', //积分系统添加用户
    'checkUser' => 'http://api.toolsets.cn/vip/score/getScoreGrade', //验证用户并获取积分
    'setPoint' => 'http://api.toolsets.cn/vip/user/score/set', //录入积分
]);

