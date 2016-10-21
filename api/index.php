<?php 
/*
 * 根据版本号进行路径分支
 */
header("Content-type: text/html; charset=utf-8");
require("lib/init.php");
$Lib = new Lib();

/* 调试方便，记录日志 */
$Lib->log($_SERVER['REQUEST_URI']." POST data is: ".json_encode($_POST, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
$Lib->log($_SERVER['REQUEST_URI']." INPUT data is: ".file_get_contents('php://input'));


/* 若存在data参数，解密 */
if (isset($_POST['data'])) {
	$data = trim($_POST['data']);
	$decrypted = $Lib->decrypt($data);
	if ($decrypted == false) {
		exit($Lib->retError("参数解析错误！"));
	}
	$_POST['data'] = $decrypted;
}

/* 若存在qr_info参数，解密 */
if (isset($_POST['qr_info'])) {
	$data = trim($_POST['qr_info']);
	$decrypted = $Lib->ddecrypt($data);
	if ($decrypted == false) {
		exit($Lib->retError("参数解析错误！"));
	}
	$_POST['qr_info'] = preg_replace('/[^a-zA-Z0-9&]/', '', $decrypted);
}

//分支判断 路径参数暂时v1.0版本仅支持2、3、4级；太短退出，太长忽略；
$url = preg_replace('/^\/|\/$/', '', $_SERVER['REQUEST_URI']);
$param = explode('/', $url);
if (count($param) < 2)
	exit($Lib->retError("对不起，您访问的页面不存在！"));

/* v1.0版本地址 */
if (count($param)==4) {
	$v10_url = "http://".$_SERVER['HTTP_HOST']."/v1.0/".$param[0]."/".$param[1]."/".$param[2].".php?act=".$param[3];
}
else {
	$v10_url = "http://".$_SERVER['HTTP_HOST']."/v1.0/".$param[0]."/".$param[1].".php";
	if (isset($param[2])) {
		$v10_url .= "?act=".$param[2];
	}
}

/* 以下根据访问做地址整理 */
$url = "";

/* 外部通知不一定带版本参数或者容易冲突，这里一一指定路径 */
if (   preg_match('/app\/qs_order\/qs_return/', $_SERVER['REQUEST_URI']) /* 强生录单反馈 */ 
	|| preg_match('/app\/qs_order\/qs_notify/', $_SERVER['REQUEST_URI']) /* 强生查询反馈 */
	|| preg_match('/pay\/alipay\/notify/', $_SERVER['REQUEST_URI'])      /* 支付宝通知接口 */
	|| preg_match('/pay\/alipay\/callback/', $_SERVER['REQUEST_URI'])    /* 支付宝回调接口 */
	) {
	$url = $v10_url;
}
/* 一般接口，根据version参数确定路径 */
else if (isset($_POST['version']) && trim($_POST['version']) == "1.0" || trim($_POST['version']) == "1.1") {
	$url = $v10_url;
}

//用户系统的修改头像，$_FILE变量不能转换路径传递，先在这里做上传，后期想到办法再改
if (isset($_FILES['img']) && !empty($_FILES['img'])) {
	if ($img=$Lib->upload($_FILES['img'], $_SERVER['REQUEST_URI'], $_POST))
		$_POST['img'] = $img;
	else 
		exit($Lib->retError("图像上传失败！"));
}

/* 测试用 */
if (isset($_FILES) && !empty($_FILES)) {
	$Lib->log("FILE data is: ".json_encode($_FILES));
}

if (!empty($url))
	exit($Lib->postData($url, $_POST));
else
	exit($Lib->retError("对不起，您访问的页面不存在！"));

?>