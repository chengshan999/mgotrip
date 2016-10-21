<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();


/* 支付宝回调接口 */
if (isset($_GET['act']) && trim($_GET['act'])=="callback") {
	exit;//什么也不用做
}

/* 支付宝通知接口 */
else if (isset($_REQUEST['act']) && trim($_REQUEST['act'])=="notify") {
	exit($Curl->getReturn(PAY_BACK_NOTIFY, $_POST));
}
else
	exit($Curl->retError("E996"));

?>