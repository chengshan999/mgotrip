<?php
return array(
    'DEVICE'=>'H5',//请求接口device传值
    'VERSION'=>'1.0',//请求接口version传值
    'PAGESIZE'=>10,//分页每页条数
    'CAR_TYPE_UNLIMITED'=>0,//车型 不限
    'CAR_TYPE_SANGTANA'=>2,//车型 桑塔纳
    'CAR_TYPE_TUAN'=>5,//车型 途观
    'SECRECT_KEY'=>'a1b2c3d4e5hahahaha',//生成订单sign参数盐值
    'SHOP_ID_QS'=>1,//强生公司代号
    'SEX_MAN'=>1,//男士代号
    'SEX_LADY'=>2,//女士代号


    'USER_VCODE_SEND'=>API_SITE.'user/vcode/send',//发送短信验证码
    'USER_LOGIN'=>API_SITE.'user/login',//登陆
    'USER_OUTLOGIN'=>API_SITE.'user/logout',//登陆

    'ORDER_LIST'=>API_SITE.'/app/order/list',//订单列表
    'ORDER_DELETE'=>API_SITE.'/app/order/delete',//删除订单
    'ORDER_DETAIL'=>API_SITE.'/app/order/info',//订单详情

    'COUPON_ALLLIST'=>API_SITE.'app/coupon/all_list',//（未使用的）优惠券列表
    'COUPON_LIST'=>API_SITE.'app/coupon/list',//（所有类型的）优惠券列表
    'COUPON_ACTIVATE'=>API_SITE.'app/coupon/active',//优惠券激活

    'PASSENGER_LIST'=>API_SITE.'app/passenger/list',//优惠券激活
);