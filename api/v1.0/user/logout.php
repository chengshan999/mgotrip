<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();

/* 用户注册 */

/* 获取数据 */
if (!isset($_POST['device'], $_POST['token']) 
	|| !$Check->isString($_POST['token'])
	)
	exit($Curl->retError("E997"));


/* 调用后台接口 */
$data = array(
	"device"    => $_POST['device'],
	"token"     => $_POST['token'],
	"ip"        => $Check->ip(),
"version"   => $_POST['version'],
);
exit($Curl->getReturn(USER_LOGOUT, $data));







?>