<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();

/* 用户已激活的券码列表 */
if (isset($_GET['act']) && trim($_GET['act'])=="list") {
	
	/* 数据验证 */
	if (!isset($_POST['device'], $_POST['token'],$_POST['status'], $_POST['page'], $_POST['page_size']) 
		|| !$Check->isString($_POST['token'], 32)
		)
		exit($Curl->retError("E997"));


	/* 调用后台接口 */
	$data = array(
		"device"     => $_POST['device'],
		"token"      => $_POST['token'],
		"status"	 =>	$_POST['status'],
		"page"        => $_POST['page'],
		"page_size"   => $_POST['page_size'],
		);
	exit($Curl->getReturn(COUPON_LIST, $data));
}

/* 用户已激活的券码全列表（不分页） */
if (isset($_GET['act']) && trim($_GET['act'])=="all_list") {
	
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
	exit($Curl->getReturn(COUPON_ALLLIST, $data));
}

/* 券码激活 */
if (isset($_GET['act']) && trim($_GET['act'])=="active") {
	
	/* 数据验证 */
	if (!isset($_POST['device'], $_POST['token'], $_POST['code']) 
		|| !$Check->isString($_POST['token'], 32)
		)
		exit($Curl->retError("E997"));


	/* 调用后台接口 */
	$data = array(
		"device"     => $_POST['device'],
		"token"      => $_POST['token'],
		"code"       => $_POST['code'],
		);
	exit($Curl->getReturn(COUPON_ACTIVE, $data));
}
else 
	exit($Curl->retError("E996"));

?>