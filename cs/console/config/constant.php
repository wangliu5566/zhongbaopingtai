<?php

/**
 * 公共接口平台地址
 */
define('API_DOMAIN', 'http://share.jqweike.com');

/**
 * 验证签名secret
 */
define('SECRET', '111111');

/**
 * 活动状态
 * 1 未开始
 * 2 进行中
 * 3 已结束
 */
define('ACTIVE_STATUS', [
	'init' => 1,
	'started' => 2,
	'end' => 3
]);

/**
 * 活动审核状态
 */
define('ACTIVITY_AUDITSTATE',[
    'pass' => 1,
    'wait' => 2,
    'fail' => 3,
]);
