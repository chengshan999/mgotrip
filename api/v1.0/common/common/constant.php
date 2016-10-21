<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）系统 前端封装 常量文件
// +----------------------------------------------------------------------

/* 用户系统接口 */
define('USER_VCODE_VERIFY',         USER_SITE.'qs/vcode/verify');//验证手机验证码
define('USER_VCODE_SEND',           USER_SITE.'qs/vcode/send');//发送手机验证码
define('USER_LOGIN',                USER_SITE.'qs/login');//登录
define('USER_LOGOUT',               USER_SITE.'qs/logout');//退出登录
define('USER_INFO_GET',             USER_SITE.'qs/info/get');//用户信息
define('USER_INFO_SET',             USER_SITE.'qs/info/set');//修改信息
define('USER_PAY_JUMP_PAY',         USER_SITE.'qs/jump/pay');//支付接口

/* APP端接口 */
define('PAY_STATUS',                APP_SITE.'index.php?m=home&c=order&a=payStatus');//支付状态
define('PAY_BACK_NOTIFY',           APP_SITE.'index.php?m=home&c=QsOrder&a=backNotify');//支付后台通知（强生出租车）

define('COUPON_ALLLIST',            APP_SITE.'index.php?m=home&c=Coupon&a=couponAllList');//用户已激活的券码全列表（不分页）
define('COUPON_LIST',               APP_SITE.'index.php?m=home&c=coupon&a=couponAll');//用户已激活的券码列表
define('COUPON_ACTIVE',             APP_SITE.'index.php?m=home&c=coupon&a=active');//券码激活

define('QSORDER_CREATE',            APP_SITE.'index.php?m=home&c=QsOrderDetail&a=create');//强生录单
define('QSORDER_QUERY',             APP_SITE.'index.php?m=home&c=QsOrder&a=qsQuery');//查询强生派车
define('QSORDER_RETURN',            APP_SITE.'index.php?m=home&c=QsOrder&a=qsReturn');//强生录单反馈
define('QSORDER_NOTIFY',            APP_SITE.'index.php?m=home&c=QsOrder&a=qsNotify');//强生派车反馈
define('QSORDER_CANCEL',            APP_SITE.'index.php?m=home&c=QsOrder&a=cancel');//强生订单取消
define('QS_SERVICE',	            APP_SITE.'index.php?m=home&c=shop&a=qsService');//强生服务费信息

define('ORDER_PAY_FARE',            APP_SITE.'index.php?m=home&c=Order&a=PayFare');//订单支付打车费
define('ORDER_COMPLETE',            APP_SITE.'index.php?m=home&c=order&a=orderComplete');//订单完成
define('ORDER_INFO',	            APP_SITE.'index.php?m=home&c=order&a=orderInfo');//订单详情
define('ORDER_LIST',	            APP_SITE.'index.php?m=home&c=order&a=getList');//订单列表
define('ORDER_DELETE',	            APP_SITE.'index.php?m=home&c=order&a=orderDelete');//订单删除
define('ORDER_UNFINISH',            APP_SITE.'index.php?m=home&c=Order&a=orderUnfinish');//未完成订单
define('ORDER_PAY_INFO',            APP_SITE.'index.php?m=home&c=QsOrder&a=orderPayInfo');//订单支付信息
define('ORDER_UNPAYSERVICELIST',	APP_SITE.'index.php?m=home&c=order&a=getUnpayserviceList');//未支付服务费订单列表

define('PASSENGER_LIST',            APP_SITE.'index.php?m=home&c=passenger&a=passengerList');//乘客信息列表
define('TRANS_SUMMARY',             APP_SITE.'index.php?m=home&c=transaction&a=transSummary');//交易总计
define('TRANS_LIST',                APP_SITE.'index.php?m=home&c=transaction&a=recordQuery');//交易记录


?>