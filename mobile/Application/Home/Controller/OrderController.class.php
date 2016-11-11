<?php
namespace Home\Controller;
use Home\Controller;
class OrderController extends BaseController{
    /*我的订单页面*/
    public function myOrder(){
        $page=I('get.page',1);
        $pagesize=C('PAGESIZE');
        $status=I('get.status',0);
        $data=array(
            'page'=>$page,
            'page_size'=>$pagesize,
            'status'=>$status
        );
        $result=$this->curlQuickPost(C('ORDER_LIST'),$data);
        /*object(stdClass)#7 (3) {
          ["status"] => string(4) "succ"
          ["msg"] => string(0) ""
          ["data"] => array(2) {
            [0] => object(stdClass)#8 (6) {
              ["amount"] => string(3) "900"
              ["date"] => string(19) "2016-11-01 13:50:05"
              ["logo"] => string(0) ""
              ["shop"] => string(6) "强生"
              ["order_no"] => string(13) "2016110113821"
              ["status"] => int(2)
            }
            [1] => object(stdClass)#9 (6) {
              ["amount"] => string(1) "0"
              ["date"] => string(19) "2016-10-31 10:35:23"
              ["logo"] => string(0) ""
              ["shop"] => string(6) "强生"
              ["order_no"] => string(13) "2016103178146"
              ["status"] => int(-1)
            }
          }
        }*/
        if($result->status=='succ'){
            $rows=$result->data;
            if(!empty($rows)){
                $params['rows']=$rows;
            }
        }else{
            $params['orderError']=$result->msg;
        }
        $params['status']=$status;
        $this->assign($params);
        $this->display('my_order');
    }
    /*订单删除*/
    public function orderDelete(){
        if(IS_AJAX){
            $orderNo=I('post.orderNo','');
            if($orderNo){
                $result=$this->curlQuickPost(C('ORDER_DELETE'),array('order_no'=>$orderNo));
                if($result->status=='succ'){
                    echo 'succ';
                }else{
                    echo $result->msg;
                }
            }else{
                echo '订单号为空';
            }
        }
    }
    /*订单详情*/
    public function orderDetail(){
        $orderNo=I('get.orderNo','');
        $status=I('get.status',0);
        $params['status']=$status;
        if($orderNo){
            $result=$this->curlQuickPost(C('ORDER_DETAIL'),array('order_no'=>$orderNo));
            /*object(stdClass)#7 (3) {
              ["status"] => string(4) "succ"
              ["msg"] => string(0) ""
              ["data"] => object(stdClass)#8 (9) {
                ["date"] => string(19) "2016-11-01 13:50:05"
                ["desc"] => string(155) "车牌号：\r\n
                                        司机姓名：\r\n
                                        联系电话：\r\n
                                        出发地：徐汇区番禺路1028号\r\n
                                        目的地：上海市闵行区绿莲路/莲花南路(路口)"
                ["shop"] => string(6) "强生"
                ["order_no"] => string(13) "2016110113821"
                ["paid_amount"] => int(0)
                ["paid_detail"] => object(stdClass)#9 (4) {
                  ["alipay"] => int(0)
                  ["wechat"] => int(0)
                  ["cash"] => string(3) "900"
                  ["fare_fee"] => string(1) "0"
                }
                ["privil_amount"] => string(1) "0"
                ["status"] => string(1) "3"
                ["total_amount"] => int(900)
              }
            }*/
            if($result->status=='succ'){
                $orderDetail=$result->data;
                if($orderDetail){
                    $params['orderDetail']=$result->data;
                    /*$desc=$result->data->desc;
                    $descArr=array();
                    if($desc && is_string($desc)){
                        $arr=explode('\r\n',$desc);
                        foreach($arr as $v){
                            if(strpos($v,'车牌号：')!==false){
                                $descArr['car_no']=str_replace('车牌号：','',$v);
                            }else if(strpos($v,'司机姓名：')!==false){
                                $descArr['driver_name']=str_replace('司机姓名：','',$v);
                            }else if(strpos($v,'联系电话：')!==false){
                                $descArr['phone_num']=str_replace('联系电话：','',$v);
                            }else if(strpos($v,'出发地：')!==false){
                                $descArr['origin']=str_replace('出发地：','',$v);
                            }else if(strpos($v,'目的地：')!==false){
                                $descArr['destination']=str_replace('目的地：','',$v);
                            }
                        }
                    }
                    $params['descArr']=$descArr;*/
                }
            }else{
                $params['errorMsg']='订单信息获取失败：'.$result->msg;
            }
        }else{
            $params['errorMsg']='订单号为空';
        }
        $this->assign($params);
        $this->display('order_details');
    }
}