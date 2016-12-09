<?php
return array(
    'WECHAT_APP_LOGIN'=>'https://open.weixin.qq.com/connect/oauth2/authorize',//微信登录链接,获取code,用来换取access_toke和openid
    'WECHAT_GET_ACCESS_TOKEN'=>'https://api.weixin.qq.com/sns/oauth2/access_token',//用code换取access_token和openid
    'WECHAT_APPID'=>'wxce65b257773cd56b',//微信公众号的唯一标识appid
    'WECHAT_APPSECRET'=>'0e272cfc48bb274be5cc310f697d0419',//微信公众号的appsecret
    'WECHAT_REDIRECT_URI'=>'http://m.test.mgocx.com/index.php?m=Home&c=Wechat&a=syncLogin',//发起微信登录请求后回调地址redirect_uri

    'DEVICE'=>'H5',//请求接口device传值
    'VERSION'=>'1.0',//请求接口version传值
    'PAGESIZE'=>10,//分页每页条数
    'BAIDUMAP_AK'=>'UQRkijgp4GWEFa1g2cRQpytGox5nf5dy',
    'CAR_TYPE_UNLIMITED'=>0,//车型 不限
    'CAR_TYPE_SANGTANA'=>2,//车型 桑塔纳
    'CAR_TYPE_TUAN'=>5,//车型 途观
    'SECRECT_KEY'=>'a1b2c3d4e5hahahaha',//生成订单sign参数盐值
    'SHOP_ID_QS'=>1,//强生公司代号
    'SEX_MAN'=>1,//男士代号
    'SEX_LADY'=>2,//女士代号
    'PAY_TYPE_ZHIFUBAO'=>1,//支付方式 支付宝代号
    'FIRST_CITY'=>'上海',//默认定位的城市

    'USER_VCODE_SEND'=>API_SITE.'user/vcode/send',//发送短信验证码
    'USER_LOGIN'=>API_SITE.'user/login',//登陆
    'USER_OUTLOGIN'=>API_SITE.'user/logout',//登陆
    'USER_INFO'=>API_SITE.'/user/info/get',//获取用户信息

    'ORDER_LIST'=>API_SITE.'/app/order/list',//订单列表
    'ORDER_DELETE'=>API_SITE.'/app/order/delete',//删除订单
    'ORDER_DETAIL'=>API_SITE.'/app/order/info',//订单详情
    'ORDER_CREATE'=>API_SITE.'app/qs_order/create',//生成订单
    'ORDER_SAVE'=>API_SITE.'/app/order/pay_fare',//保存打车信息
    'ORDER_COMPLETE'=>API_SITE.'/app/order/complete',//订单完成

    'COUPON_ALLLIST'=>API_SITE.'app/coupon/all_list',//（未使用的）优惠券列表
    'COUPON_LIST'=>API_SITE.'app/coupon/list',//（所有类型的）优惠券列表
    'COUPON_ACTIVATE'=>API_SITE.'app/coupon/active',//优惠券激活
    'COUPON_RETURN'=>API_SITE.'app/order/unpay_deal',//退还优惠券（支付失败）

    'PASSENGER_LIST'=>API_SITE.'app/passenger/list',//客户信息列表
    'SERVICE_CHARGE'=>API_SITE.'app/service/qs_info',//服务费信息

    'PAYMENT_REDIRECT'=>API_SITE.'app/pay/jump_pay',//支付跳转
    'ALIPAY_CALLBACK'=>API_SITE.'app/pay/alipay_h5',//支付宝回调地址
    'PAY_STATUS'=>API_SITE.'app/pay/status',//支付状态

    'WAIT_TAXI'=>API_SITE.'/app/order/unfinish',//正在派车，倒计时阶段调用
    'QUERY_TAXI'=>API_SITE.'app/qs_order/query',//派车信息查询
    'ORDER_CANCEL'=>API_SITE.'/app/qs_order/cancel',//订单取消

    'ACTIVITY_DISPLAY'=>API_SITE.'app/activity/display',//2016酒节活动启用，提示APP端的是否有活动等内容
);