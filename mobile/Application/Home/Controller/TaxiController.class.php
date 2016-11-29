<?php
namespace Home\Controller;
use Home\Controller;
class TaxiController extends BaseController{
    /*渲染等车倒计时界面*/
    public function takeTaxi(){
        $currentCity=I('get.city',C('FIRST_CITY'));
        $params['currentCity']=$currentCity;

        $result=$this->curlQuickPost(C('COUPON_ALLLIST'));
        $params['totalCoupon']=0;
        //dump($result->data->list);exit;
        if($result->status=='succ'){
            $params['totalCoupon']=$result->data->total?$result->data->total:0;
            $params['coupinList']=empty($result->data->list)?array():$result->data->list;
            /*array(2) {
                  [0] => object(stdClass)#9 (8) {
                    ["id"] => string(2) "63"
                    ["name"] => string(21) "强生测试代金券"
                    ["amount"] => string(4) "1000"
                    ["code"] => string(6) "******"
                    ["use_start_date"] => string(10) "2016-09-20"
                    ["use_end_date"] => string(10) "2016-12-31"
                    ["min_order_amount"] => string(1) "0"
                    ["pic"] => string(63) "http://pic.test.yufu365.com/mgotrip/coupon/qiangshengcoupon.png"
                  }
                  [1] => object(stdClass)#10 (8) {
                    ["id"] => string(2) "62"
                    ["name"] => string(21) "强生测试代金券"
                    ["amount"] => string(4) "1000"
                    ["code"] => string(0) ""
                    ["use_start_date"] => string(10) "2016-09-20"
                    ["use_end_date"] => string(10) "2016-12-31"
                    ["min_order_amount"] => string(1) "0"
                    ["pic"] => string(63) "http://pic.test.yufu365.com/mgotrip/coupon/qiangshengcoupon.png"
                  }
            }*/
        }

        $this->assign($params);
        $this->display('take_taxi');
    }
    /*获取等车时间*/
    public function getInfoForWaitTaxi(){//WAIT_TAXI
        if(IS_AJAX){
            $result=$this->curlQuickPost(C('WAIT_TAXI'));
            if($result->status='succ'){
                $data=$result->data;
                if($data[0]->order_no && $data[0]->time_remaining){
                    echo json_encode($data[0]);
                }
            }
        }
    }
    /*派车信息查询*/
    public function queryTaxiIsCome(){//QUERY_TAXI
        if(IS_AJAX){
            $orderNo=I('post.orderNo','');
            if($orderNo){
                $result=$this->curlQuickPost(C('QUERY_TAXI'),array('order_no'=>$orderNo));
                if($result->status=='succ' && !in_array('',$result->data)){
                    echo json_encode($result->data);
                }
            }
        }
    }
    /*订单取消*/
    public function taxiOrderCancel(){//ORDER_CANCEL
        if(IS_AJAX){
            $orderNo=I('post.orderNo','');
            if($orderNo){
                $result=$this->curlQuickPost(C('ORDER_CANCEL'),array('order_no'=>$orderNo));
                if($result->status=='succ'){
                    $data=isset($result->data)?$result->data:'';
                    if($data!==''){
                        echo json_encode($data);
                    }
                }
            }
        }
    }
    /*在线支付打车费用*/
    public function payCarFareOnLine(){
        if(IS_AJAX){
            $userAmount=I('post.userAmount');
            $payType=I('post.payType',0);
            $couponId=I('post.couponId');
            $orderNo=I('post.orderNo',0);
            if($payType && $orderNo){
                //保存打车信息 ORDER_SAVE
                $result=$this->curlQuickPost(C('ORDER_SAVE'),array(
                    'amount'=>$userAmount*100,
                    'coupon_id'=>$couponId,
                    'order_no'=>$orderNo
                ));
                if($result->status=='succ'){
                    $data=$result->data;
                    if(!empty($data->order_id)){
                        if($data->amount){
                            //跳转到支付
                            $payRe=$this->curlQuickPost(C('PAYMENT_REDIRECT'),array(
                                'is_other_fee'=>0,
                                'order_id'=>$data->order_id,
                                'pay_amount'=>$data->amount,
                                'pay_corporation_id'=>$payType
                            ));
                            if($payRe->status=='succ'){
                                $code=json_decode($payRe->data);
                                $url=$code->url;
                                $method=$code->method;
                                if($url && strtolower($method)=='get'){
                                    //GET方式请求支付宝
                                    unset($code->url);
                                    unset($code->method);
                                    $params=http_build_query($code);
                                    session('orderNoId',null);
                                    session('orderNoId',$orderNo);
                                    echo $url.$params;
                                }
                            }
                        }else{
                            //不需要支付，直接完成订单，跳转到我的订单 ORDER_COMPLETE
                            $reCom=$this->curlQuickPost(C('ORDER_COMPLETE'),array('order_no'=>$orderNo));
                            if($reCom->status=='succ'){
                                echo 'orderComplete';
                            }
                        }

                    }

                }
                
            }
        }
    }
    /*现金支付*/
    public function payWithCash(){//ORDER_COMPLETE
        if(IS_AJAX){
            $orderNo=I('post.orderNo',0);
            if($orderNo){
                $result=$this->curlQuickPost(C('ORDER_COMPLETE'),array('order_no'=>$orderNo));
                if($result->status=='succ'){
                    //订单完成执行成功，跳转到首页
                    $url=U('Index/index');
                    echo $url;
                }
            }
        }
    }
}