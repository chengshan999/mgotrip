<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();

/* 用户注册 */

/* 获取数据 */
if (!isset($_POST['data'], $_POST['device'], $_POST['vcode']))
	exit($Curl->retError("E997"));

$data_arr = $Check->splitData(trim($_POST['data']));

/* 调用后台接口 */
$data = array(
	"device"    => $_POST['device'],
	"vcode"     => $_POST['vcode'],
	"ip"        => $Check->ip(),
	"mobile"    => $Check->filterSpace($data_arr[0]),
	"device_no" => $Check->filterSpace($data_arr[1]),
	"version"   => $_POST['version'],
	);
exit($Curl->getReturn(USER_LOGIN, $data));


?>