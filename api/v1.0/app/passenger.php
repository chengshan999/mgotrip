<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();

/* 乘客信息列表 */
if (isset($_GET['act']) && trim($_GET['act'])=="list") {
	
	/* 数据验证 */
	if (!isset($_POST['device'], $_POST['token']) 
		|| !$Check->isString($_POST['token'], 32)
		)
		exit($Curl->retError("E997"));


	/* 调用后台接口 */
	$data = array(
		"device"     => $_POST['device'],
		"token"      => $_POST['token'],
		);
	exit($Curl->getReturn(PASSENGER_LIST, $data));
}
else
	exit($Curl->retError("E996"));





?>