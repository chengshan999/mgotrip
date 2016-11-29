<?php
namespace Home\Controller;
use Home\Controller;
class AlipayController extends BaseController{
    public function returnH5(){
        $orderNoId=session('orderNoId');//$orderNo
        //session('orderNoId',null);
        //判断是否有未处理完成的付款操作
        if($orderNoId){
            $req=I('get.');
            /*array(14) {
              ["is_success"] => string(1) "T"
              ["notify_id"] => string(72) "RqPnCoPT3K9%2Fvwbh3InZc2LFy9u1kxsCorMrCIQKlXLmqQQwiZVVa3X6NUQkUW3N%2BNXa"
              ["notify_time"] => string(19) "2016-11-29 11:00:13"
              ["notify_type"] => string(17) "trade_status_sync"
              ["out_trade_no"] => string(17) "20161129105948957"
              ["payment_type"] => string(1) "1"
              ["seller_id"] => string(16) "2088521113448825"
              ["service"] => string(36) "alipay.wap.create.direct.pay.by.user"
              ["subject"] => string(18) "强生打车订单"
              ["total_fee"] => string(4) "0.01"
              ["trade_no"] => string(28) "2016112921001004890209534429"
              ["trade_status"] => string(13) "TRADE_SUCCESS"
              ["sign"] => string(32) "c3c6388340157d4d7c5ed2b167769caa"
              ["sign_type"] => string(3) "MD5"
            }*/
            $re=$this->curlQuickPost(C('ALIPAY_CALLBACK'),array('result'=>json_encode($req)));
            /*object(stdClass)#7 (3) {
              ["result"] => int(1)//-1：支付失败；0：不确定；1：支付成功
              ["is_other_fee"] => int(0)
              ["order_id"] => int(1129)
            }*/
            if(isset($re->is_other_fee) && $re->order_id){//$re->result==1 &&
                $is_service_pay=$re->is_other_fee;
                $order_id=$re->order_id;
                if($is_service_pay){
                    //支付服务费
                    $takeTaxi=U('Taxi/takeTaxi');
                    header('location:'.$takeTaxi);

                    /*$result=$this->curlQuickPost(C('PAY_STATUS'),array('order_id'=>$order_id));
                    if($result->status=='succ' && isset($result->data->other_pay_status)){
                        $status_service_pay=$result->data->other_pay_status;
                        if($status_service_pay==2){
                            //服务费已支付，跳转到take_taxi页面
                            $takeTaxi=U('Taxi/takeTaxi');
                            header('location:'.$takeTaxi);
                        }else if($status_service_pay == 1){
                            //服务费支付中,点击刷新页面
                            $this->assign(array(
                                'msg'=>'支付处理中，点击再次查看'
                            ));
                            $this->display();
                        }else{
                            //服务费未支付
                            $this->assign(array(
                                'msg'=>'支付失败，重新支付',
                                'url'=>U
                            ));
                            $this->display();
                        }
                    }*/
                }else{
                    //支付打车费
                    $myOrder=U('Order/myOrder');
                    $this->assign(array(
                        'msg'=>'支付成功，前往订单列表？',
                        'url'=>$myOrder
                    ));
                    $this->display();
                    /*$result=$this->curlQuickPost(C('PAY_STATUS'),array('order_id'=>$order_id));
                    if($result->status=='succ' && isset($result->data->pay_status)){
                        $status_order_pay=$result->data->pay_status;

                        if($status_order_pay==2){
                            //打车费已支付

                        }else if($status_order_pay == 1){
                            //打车费支付中

                        }else{
                            //打车费未支付

                        }
                    }*/
                }
            }else{
                //cookie('alipayCallBackResult',serialize($req));
                //弹出数据处理中，请稍后。然后倒计时5秒重载一下界面
                $ind=U('Index/index');
                $this->assign(array(
                    'msg'=>'支付失败，请重新下单',
                    'url'=>$ind
                ));
                $this->display();
            }
        }else{
            //没有未处理完的付款操作，进入首页
            $ind=U('Index/index');
            header('location:'.$ind);
        }
    }
    /*网络不稳，连接失败，再试一次入口*/
    public function returnH5Again(){
        if(IS_AJAX){
            $alipayCallBackResult=cookie('alipayCallBackResult','');
            if($alipayCallBackResult){
                $re=$this->curlQuickPost(C('ALIPAY_CALLBACK'),array('result'=>unserialize($alipayCallBackResult)));
                if($re->status='succ'){

                }
            }
        }
    }
}