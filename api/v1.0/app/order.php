<?php 
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();
/* 订单支付打车费 */
if (isset($_GET['act']) && trim($_GET['act'])=="pay_fare") {

	/* 数据验证 */
	if (!isset($_POST['device'], $_POST['token'])
		|| !$Check->isString($_POST['token'], 32)
		)
		exit($Curl->retError("E997"));

	/* 调用后台接口 */
	$data = array(
		"device"     => $_POST['device'],
		"token"      => $_POST['token'],
		"order_no"	 =>	$_POST['order_no'],
		"coupon_id"  => $_POST['coupon_id'],
		"amount"     => $_POST['amount'],
	);
	exit($Curl->getReturn(ORDER_PAY_FARE, $data));
}

/* 订单完成 */
if (isset($_GET['act']) && trim($_GET['act'])=="complete") { 
	
	/* 数据验证 */
	if (!isset($_POST['device'], $_POST['token']) 
		|| !$Check->isString($_POST['token'], 32)
		)
		exit($Curl->retError("E997"));


	/* 调用后台接口 */
	$data = array(
		"device"     => $_POST['device'],
		"token"      => $_POST['token'],
		"order_no"	 =>	$_POST['order_no'],
		);
	exit($Curl->getReturn(ORDER_COMPLETE, $data));
}

/* 订单详情 */
if (isset($_GET['act']) && trim($_GET['act'])=="info") {

	/* 数据验证 */
	if (!isset($_POST['device'], $_POST['token'])
	|| !$Check->isString($_POST['token'], 32)
	)
		exit($Curl->retError("E997"));


	/* 调用后台接口 */
	$data = array(
			"device"     => $_POST['device'],
			"token"      => $_POST['token'],
			"order_no"	 =>	$_POST['order_no'],
	);
	exit($Curl->getReturn(ORDER_INFO, $data));
}

/* 订单列表 */
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
			"page"	 	 =>	$_POST['page'],
			"page_size"	 =>	$_POST['page_size'],
			"status"	 =>	$_POST['status'],
	);
	exit($Curl->getReturn(ORDER_LIST, $data));
}


/* 未支付服务费、未删除、未删除列表 */
if (isset($_GET['act']) && trim($_GET['act'])=="unpayservice") {

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
	exit($Curl->getReturn(ORDER_UNPAYSERVICELIST, $data));
}

/* 订单删除 */
if (isset($_GET['act']) && trim($_GET['act'])=="delete") {

	/* 数据验证 */
	if (!isset($_POST['device'], $_POST['token'])
	|| !$Check->isString($_POST['token'], 32)
	)
		exit($Curl->retError("E997"));


	/* 调用后台接口 */
	$data = array(
			"device"     => $_POST['device'],
			"token"      => $_POST['token'],
			"order_no"	 =>	$_POST['order_no'],
	);
	exit($Curl->getReturn(ORDER_DELETE, $data));
}


/*未完成订单 */
if (isset($_REQUEST['act']) && trim($_REQUEST['act'])=="unfinish") {

    /* 数据验证 */
    if (!isset($_REQUEST['device'], $_REQUEST['token'])
        || !$Check->isString($_REQUEST['token'], 32)
    )
        exit($Curl->retError("E997"));


    /* 调用后台接口 */
    $data = array(
        "device"     => $_REQUEST['device'],
        "token"      => $_REQUEST['token'],
    );
    exit($Curl->getReturn(ORDER_UNFINISH, $data));
}
else
    exit($Curl->retError("E996"));


?>