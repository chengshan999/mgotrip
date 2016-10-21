<?php
require("../common/init.php"); //初始化文件
$Curl  = new Curl();
$Check = new Check();

/* 强生录单 */
if (isset($_REQUEST['act']) && trim($_REQUEST['act'])=="create") {

    /* 数据验证 */
    if (!isset($_REQUEST['device'], $_REQUEST['token'], $_REQUEST['aboard_landmark'], $_REQUEST['aboard_near'], $_REQUEST['car_kind'], $_REQUEST['car_level'], $_REQUEST['car_type'], $_REQUEST['desti_latitude'], $_REQUEST['desti_longitude'], $_REQUEST['destination'], $_REQUEST['fare_fee'], $_REQUEST['gender'], $_REQUEST['latitude'], $_REQUEST['location'], $_REQUEST['longitude'], $_REQUEST['mobile'], $_REQUEST['name'], $_REQUEST['order_no'], $_REQUEST['passenger_id'], $_REQUEST['radius'], $_REQUEST['shop_id'], $_REQUEST['sign'])
        || !$Check->isString($_REQUEST['token'], 32)
    )
        exit($Curl->retError("E997"));


    /* 调用后台接口 */
    $data = array(
        "device"          => $_REQUEST['device'],
        "token"           => $_REQUEST['token'],
        "aboard_landmark" => $_REQUEST['aboard_landmark'],
        "aboard_near"     => $_REQUEST['aboard_near'],
        "car_kind"        => $_REQUEST['car_kind'],
        "car_level"       => $_REQUEST['car_level'],
        "car_type"        => $_REQUEST['car_type'],
        "desti_latitude"  => $_REQUEST['desti_latitude'],
        "desti_longitude" => $_REQUEST['desti_longitude'],
        "destination"     => $_REQUEST['destination'],
        "fare_fee"        => $_REQUEST['fare_fee'],
        "gender"          => $_REQUEST['gender'],
        "latitude"        => $_REQUEST['latitude'],
        "location"        => $_REQUEST['location'],
        "longitude"       => $_REQUEST['longitude'],
        "mobile"          => $_REQUEST['mobile'],
        "name"            => $_REQUEST['name'],
        "order_no"        => $_REQUEST['order_no'],
        "passenger_id"    => $_REQUEST['passenger_id'],
        "radius"          => $_REQUEST['radius'],
		"shop_id"         => $_REQUEST['shop_id'],
        "sign"            => $_REQUEST['sign'],
    );
    exit($Curl->getReturn(QSORDER_CREATE, $data));
}

/* 查询强生派车 */
if (isset($_REQUEST['act']) && trim($_REQUEST['act'])=="query") {

    /* 数据验证 */
    if (!isset($_REQUEST['device'], $_REQUEST['token'], $_REQUEST['order_no'])
        || !$Check->isString($_REQUEST['token'], 32)
    )
        exit($Curl->retError("E997"));


    /* 调用后台接口 */
    $data = array(
        "device"     => $_REQUEST['device'],
        "token"      => $_REQUEST['token'],
        "order_no"   => trim($_REQUEST['order_no']),
    );
    exit($Curl->getReturn(QSORDER_QUERY, $data));
}

/* 强生录单反馈 */
if (isset($_REQUEST['act']) && trim($_REQUEST['act'])=="qs_return") {
    exit($Curl->postData(QSORDER_RETURN, $_REQUEST));
}

/* 强生派车反馈 */
if (isset($_REQUEST['act']) && trim($_REQUEST['act'])=="qs_notify") {
    exit($Curl->postData(QSORDER_NOTIFY, $_REQUEST));
}

/* 查询订单支付信息 */
if (isset($_REQUEST['act']) && trim($_REQUEST['act'])=="pay_info") {

    /* 数据验证 */
    if (!isset($_REQUEST['device'], $_REQUEST['token'], $_REQUEST['order_no'])
        || !$Check->isString($_REQUEST['token'], 32)
    )
        exit($Curl->retError("E997"));


    /* 调用后台接口 */
    $data = array(
        "device"     => $_REQUEST['device'],
        "token"      => $_REQUEST['token'],
        "order_no"   => trim($_REQUEST['order_no']),
    );
    exit($Curl->getReturn(ORDER_PAY_INFO, $data));
}

/* 订单取消 */
if (isset($_GET['act']) && trim($_GET['act'])=="cancel") {

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
	exit($Curl->getReturn(QSORDER_CANCEL, $data));
}

else
    exit($Curl->retError("E996"));

?>