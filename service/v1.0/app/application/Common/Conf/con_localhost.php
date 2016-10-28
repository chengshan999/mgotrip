<?php
/*
 * 摩购外出（强生）项目支付系统配置文件
 */
return array(
    /* 路径设置 */
    'PIC_URL'               =>  'http://pic.test.yufu365.com/mgotrip/', //图片路径
    'SITE_URL'              =>  'http://qs.test.yufu365.com/',//网站路径

    /* 与用户系统交互接口 */
    'USER_GET_ID'           => 'http://localhost/mgo_interface.php?act=user_getuid',//根据token获取用户id接口
	'USER_PAY_JUMP_PAY'     => 'http://192.168.1.30:8080/qs/jump/pay',//支付宝支付跳转接口

	//强生订单录入地址
	'QSORDER_RECORD'        => 'http://api.mgolocal.com/pay/alipay/notify',


);

?>