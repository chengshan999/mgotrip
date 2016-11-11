<?php 
header("Content-type: application/json; charset=UTF-8");
error_reporting(E_ALL);

define('DEBUG', true);//调试模式打开，涉及错误码输出，上线后关闭

/* 定义站点根 */
define('ROOT_PATH', str_replace('common/init.php', '', str_replace('\\', '/', __FILE__)));

define('COMMON_PATH', ROOT_PATH.'common/common/');

define('LOG_PATH', ROOT_PATH.'../lib/log/');//跟api用同一日志文件，输入与报错同文件，便于比对


require(ROOT_PATH.'common/curl.class.php');
require(ROOT_PATH.'common/check.class.php');


//define('APP_SITE',       'http://192.168.1.101:8088/v1.0/mgotrip/');//PHP主功能后端入口
define('APP_SITE',         'http://localhost/mgotrip/service/v1.0/app/');
define('USER_SITE',        'http://192.168.1.30:8080/');
//define('USER_SITE',        'http://qs.test.yufu365.com/');
//define('USER_SITE',      'http://192.168.1.101:8080/');//用户系统后端入口
define('MSG_SITE',         'http://112.124.103.15:8080/');//短信发送入口

define('MALL_SITE',        'http://localhost/mgotrip/service/v1.0/admin/');//商城后端入口

require(ROOT_PATH.'common/common/constant.php');//本版本常量文件
require(ROOT_PATH.'common/common/errorcode.php');//错误码文件


?>