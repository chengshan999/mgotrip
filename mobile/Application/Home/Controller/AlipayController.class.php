<?php
namespace Home\Controller;
use Home\Controller;
class AlipayController extends BaseController{
    public function returnH5(){
        $orderNoParams=cookie('orderNoParams');
        //判断是否有未处理完成的付款操作
        if($orderNoParams){
            //获取支付跳转的传参
            $orderNoArr=unserialize($orderNoParams)?unserialize($orderNoParams):array();
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
                    //查看服务费支付状态
                    $result=$this->curlQuickPost(C('PAY_STATUS'),array('order_id'=>$order_id));
                    if($result->status=='succ' && isset($result->data->other_pay_status)){
                        $status_service_pay=$result->data->other_pay_status;
                        if($status_service_pay==2){
                            //服务费已支付，跳转到take_taxi页面
                            //支付成功要销毁本地订单参数
                            cookie('orderNoParams',null);
                            $takeTaxi=U('Taxi/takeTaxi');
                            header('location:'.$takeTaxi);
                        }else if($status_service_pay == 1){
                            //服务费支付中,点击刷新页面
                            $this->display('returnH5_1');
                        }else{
                            //服务费支付失败，重新支付
                            $payRe=$this->curlQuickPost(C('PAYMENT_REDIRECT'),$orderNoArr);
                            if($payRe->status=='succ'){
                                $code=json_decode($payRe->data);
                                $url=$code->url;
                                $method=$code->method;
                                if($url && strtolower($method)=='get'){
                                    //GET方式请求支付宝
                                    unset($code->url);
                                    unset($code->method);
                                    $params=http_build_query($code);
                                    $this->assign(array(
                                        'url'=>$url.$params
                                    ));
                                }
                            }
                            $this->display('returnH5_2');
                        }
                    }
                }else{
                    //支付打车费
                    //查看打车费支付状态
                    $result=$this->curlQuickPost(C('PAY_STATUS'),array('order_id'=>$order_id));
                    if($result->status=='succ' && isset($result->data->pay_status)){
                        $status_order_pay=$result->data->pay_status;
                        if($status_order_pay==2){
                            //打车费已支付，订单完成，跳转到我的订单
                            //支付成功要销毁本地订单参数
                            cookie('orderNoParams',null);
                            $myOrder=U('Order/myOrder',array('status'=>2));
                            $this->assign(array(
                                'url'=>$myOrder
                            ));
                            $this->display('returnH5_3');
                        }else if($status_order_pay == 1){
                            //打车费支付中，重载界面
                            $this->display('returnH5_1');
                        }else{
                            //打车费未支付成功，重新支付
                            $payRe=$this->curlQuickPost(C('PAYMENT_REDIRECT'),$orderNoArr);
                            if($payRe->status=='succ'){
                                $code=json_decode($payRe->data);
                                $url=$code->url;
                                $method=$code->method;
                                if($url && strtolower($method)=='get'){
                                    //GET方式请求支付宝
                                    unset($code->url);
                                    unset($code->method);
                                    $params=http_build_query($code);
                                    $this->assign(array(
                                        'url'=>$url.$params
                                    ));
                                }
                            }
                            $this->display('returnH5_4');
                        }
                    }
                }
            }else{
                //支付状态未知，重载界面
                $this->display('returnH5_1');
            }
        }else{
            //没有未处理完的付款操作，进入首页
            $ind=U('Index/index');
            header('location:'.$ind);
        }
    }
}