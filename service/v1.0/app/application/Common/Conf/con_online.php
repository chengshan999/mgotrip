<?php
/*
 * 摩购外出（强生）项目支付系统配置文件
 */
return array(
    /* 路径设置 */
    'PIC_URL'               =>  '', //图片路径
    'SITE_URL'              =>  '',//网站路径

    /* 与用户系统交互接口 */
    'USER_GET_ID'           => 'http://192.168.1.103:8080/qs/getuid',//根据token获取用户id接口
	'USER_PAY_JUMP_PAY'     => 'http://192.168.1.103:8080/qs/jump/pay',//支付宝支付跳转接口

	/* 强生约租车 测试环境入口 */
	'QSYZC_INTERFACE'       => '',//强生电调系统入口
	'QSYZC_RETURN_URL'      => 'http://???.com/qs_order/qs_return.php',//强生录单结果反馈地址
	'QSYZC_NOTIFY_URL'      => 'http://???.com/qs_order/qs_notify.php',//强生查单结果反馈地址



);

?>