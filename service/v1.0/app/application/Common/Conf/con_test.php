<?php
/*
 * 摩购外出（强生）项目支付系统配置文件
 */
return array(
    /* 路径设置 */
    'PIC_URL'               =>  'http://pic.test.yufu365.com/mgotrip/', //图片路径
    'SITE_URL'              =>  'http://qs.test.yufu365.com/',//网站路径

    /* 与用户系统交互接口 */
    'USER_GET_ID'           => 'http://192.168.1.101:8080/qs/getuid',//根据token获取用户id接口
	'USER_PAY_JUMP_PAY'     => 'http://192.168.1.101:8080/qs/jump/pay',//支付宝支付跳转接口

	//支付宝通知 JAVA
	'ALIPAY_NOTIFY'         => 'http://192.168.1.101:8080/qs/jump/notify',

	//强生订单录入地址
	'QSORDER_RECORD'        => 'http://qs.test.yufu365.com/pay/alipay/notify',

	/* 强生约租车 测试环境入口 */
	//'QSYZC_INTERFACE'     => 'http://www.qstaxi.net:13333/qsOrder',//强生电调系统正式环境入口
	//'QSYZC_INTERFACE'     => 'http://www.qstaxi.net:23333/qsOrder',//强生电调系统测试环境入口
	'QSYZC_INTERFACE'       => 'http://test.yufu365.com/qs/qs_test.php',//测试用 2016-02-17
	'QSYZC_RETURN_URL'      => 'http://qs.test.yufu365.com/app/qs_order/qs_return',//强生录单结果反馈地址
	'QSYZC_NOTIFY_URL'      => 'http://qs.test.yufu365.com/app/qs_order/qs_notify',//强生查单结果反馈地址


);

?>