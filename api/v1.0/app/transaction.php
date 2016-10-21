<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();

/* 交易总计 */
if (isset($_GET['act']) && trim($_GET['act'])=="summary") {
	
	/* 数据验证 */
	if (!isset($_POST['device'], $_POST['token']) 
		|| !$Check->isString($_POST['token'], 32))
		exit($Curl->retError("E997"));

	/* 调用后台接口 */
	$data = array(
		"device" => $_POST['device'],
		"token"  => $_POST['token'],
		);

		if (isset($_POST['year']))
			$data['year'] = $_POST['year'];
		if (isset($_POST['month']))
			$data['month'] = $_POST['month'];

	exit($Curl->getReturn(TRANS_SUMMARY, $data)); 
}

/* 交易记录 */
else if (isset($_GET['act']) && trim($_GET['act'])=="list") {	
	/* 数据验证 */
	if (!isset($_POST['device'], $_POST['token'], $_POST['page'], $_POST['page_size']) 
		|| !$Check->isString($_POST['token'], 32)
		)
		exit($Curl->retError("E997"));

	/* 调用后台接口 */
	$data = array(
		"device" => $_POST['device'],
		"token"  => $_POST['token'],
		"page"   => $_POST['page'],
		"page_size"   => $_POST['page_size'],
		);
	if (isset($_POST['year']))
		$data['year'] = $_POST['year'];
	if (isset($_POST['month']))
		$data['month'] = $_POST['month'];
	exit($Curl->getReturn(TRANS_LIST, $data));
}

else 
	exit($Curl->retError("E996"));






?>