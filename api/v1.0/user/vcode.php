<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();

/* 发送手机验证码 */
if (isset($_GET['act']) && trim($_GET['act'])=="send") {
exit('vcode/send');
	/* 获取数据 */
	if (!isset($_POST['device'], $_POST['token'], $_POST['type'], $_POST['mobile']) 
		|| !$Check->isString($_POST['mobile'], 11)
		)
		exit($Curl->retError("E997"));

	/* 调用后台接口 */
	$data = array(
		"device"    => $_POST['device'],
		"token"     => $_POST['token'],
		"mobile"    => $_POST['mobile'],
		"type"      => $_POST['type'],
		"ip"        => $Check->ip(),
		"version"   => $_POST['version'],
		);
	exit($Curl->getReturn(USER_VCODE_SEND, $data));
}
else {
	exit($Curl->retError("E996"));
}






?>