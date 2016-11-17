<?php
namespace Home\Controller;
use Home\Controller;
class AlipayController extends BaseController{
    public function returnH5(){
        $result=$_REQUEST;
        $re=$this->curlQuickPost(C('ALIPAY_CALLBACK'),$result);
        if($re->status='succ'){
            //支付成功，判断是否是支付服务费。
            //如果是支付服务费，则跳转到等车界面。
            //如果是节打车费用，则跳转到我的订单或者首页
            //订单列表中的未完成只能是已经喊车但是未付打车费的情况，
            //不能是未付服务费，因为未付服务费是没有开始喊车的。

        }else{
            //支付失败，跳转到 接口：订单列表（未支付服务费）app/order/unpayservice
            //以便再次支付或者取消订单

        }
    }
}