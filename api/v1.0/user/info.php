<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();


/* 用户信息 */
if (isset($_GET['act']) && trim($_GET['act'])=="get") {

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
	exit($Curl->getReturn(USER_INFO_GET, $data));
}

/* 修改信息 */ 
if (isset($_GET['act']) && trim($_GET['act'])=="set") {

	/* 获取数据 */
	if (!isset($_POST['device'], $_POST['token'], $_POST['birthday'], $_POST['gendar'], $_POST['region_id']) 
		|| !$Check->isString($_POST['token'])
		)
		exit($Curl->retError("E997"));

	/* 调用后台接口 */
	$data = array(
		"device"    => $_POST['device'],
		"token"     => $_POST['token'],
		"birthday"  => $_POST['birthday'],
		"gendar"    => intval($_POST['gendar']),
		"region_id" => intval($_POST['region_id']),
		"ip"        => $Check->ip(),
		"version"   => $_POST['version'],
		);
	exit($Curl->getReturn(USER_INFO_SET, $data));
}

else {
	exit($Curl->retError("E996"));
}






?>