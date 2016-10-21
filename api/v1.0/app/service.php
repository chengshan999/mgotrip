<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();

/* 获取强生约租车平台服务费信息 */
if (isset($_GET['act']) && trim($_GET['act'])=="qs_info") {
	
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
	exit($Curl->getReturn(QS_SERVICE, $data));
}
else
	exit($Curl->retError("E996"));





?>