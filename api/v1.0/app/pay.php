<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();


/* 支付接口 */
if (isset($_REQUEST['act']) && trim($_REQUEST['act'])=="jump_pay") {

	/* 数据验证 */
	if (!isset($_REQUEST['device'], $_REQUEST['token'], $_REQUEST['pay_amount'], $_REQUEST['pay_corporation_id'], $_REQUEST['order_id'])
		|| !$Check->isString($_REQUEST['token'], 32)
		)
		exit($Curl->retError("E997"));
	
	/* 调用后台接口 */
	$data = array(
		"device"			 => $_REQUEST['device'],
		"token"				 => $_REQUEST['token'],
		"is_other_fee"		 => $_REQUEST['is_other_fee'],
		"pay_amount"		 => intval($_REQUEST['pay_amount']),
		"pay_corporation_id" => intval($_REQUEST['pay_corporation_id']),
		"order_id"	         => $_REQUEST['order_id'],
		"version"			 => $_REQUEST['version'],
		);

	exit($Curl->getReturn(USER_PAY_JUMP_PAY, $data));
}

else {
	exit($Curl->retError("E996"));
}

?>