<?php
namespace Home\Controller;
use Home\Controller;
class TaxiController extends BaseController{
    /*渲染等车倒计时界面*/
    public function takeTaxi(){
        $currentCity=I('get.city',C('FIRST_CITY'));
        $params['currentCity']=$currentCity;
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
}