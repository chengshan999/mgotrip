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
                $params['currentSize']=count($rows);

                $re=$this->curlQuickPost(C('COUPON_ALLLIST'));
                $params['totalCoupon']=0;
                //dump($result->data->list);exit;
                if($re->status=='succ'){
                    $params['totalCoupon']=$re->data->total?$re->data->total:0;
                    $params['coupinList']=empty($re->data->list)?array():$re->data->list;
                }
            }
        }else{
            $params['orderError']=$result->msg;
        }
        $params['status']=$status;
        $this->assign($params);
        $this->display('my_order');
    }
    /*获取新的分页*/
    public function getNewPage(){
        if(IS_AJAX){
            $page=I('post.page',2);
            $status=I('post.status',0);
            $pagesize=C('PAGESIZE');
            $data=array(
                'page'=>$page,
                'page_size'=>$pagesize,
                'status'=>$status
            );
            $result=$this->curlQuickPost(C('ORDER_LIST'),$data);
            if($result->status=='succ'){
                $rows=$result->data;
                if(!empty($rows)){
                    $str='';
                    foreach($rows as $row){
                        if($status==0 || $row->status==$status){
                            if($row->status==1){
                                $str.='<div class="dn">
                                <ul class="in_items">
                                    <li class="items_t">
                                        <span class="oder_date">'.substr($row->date,0,10).'</span>
                                        <span class="oder_time">'.substr($row->date,11).'</span>
                                        <span class="oder_status fr">未完成</span>
                                    </li>

                                    <a href="'.U('Order/orderDetail',array('orderNo'=>$row->order_no,'status'=>$status)).'">
                                        <li class="items_m">
                                            <p> <strong class="c4d">'.$row->shop.'</strong>
                                                <strong class="c333">订单号：'.$row->order_no.'</strong>
                                            </p>

                                            <i><img class="fr" src="/Public/mgotrip/images/arrow_r.png" alt=""/></i>
                                        </li>
                                    </a>

                                    <li class="order_pay_btn" order_no="'.$row->order_no.'">
                                        <a href="javascript:void(0);" onclick="payOnline(\''.$row->order_no.'\');" style="margin:0 20px;background-color:#ffc300;">在线支付</a>
                                        <a href="javascript:void(0);" onclick="payCash(\''.$row->order_no.'\');" style="background-color:#4c4c4c;">现金支付</a>
                                    </li>
                                </ul>
                            </div>';
                            }else if($row->status==-1){
                                $str.='<div class="dn">
                                <ul class="in_items">
                                    <li class="items_t">
                                        <span class="oder_date">'.substr($row->date,0,10).'</span>
                                        <span class="oder_time">'.substr($row->date,11).'</span>
                                        <span class="oder_status fr" style="color:#808080;">已取消</span>
                                    </li>

                                    <a href="'.U('Order/orderDetail',array('orderNo'=>$row->order_no,'status'=>$status)).'">
                                        <li class="items_m">
                                            <p>
                                                <strong class="c4d">'.$row->shop.'</strong>
                                                <strong class="c333">订单号：'.$row->order_no.'</strong>
                                            </p>

                                            <i><img class="fr" src="/Public/mgotrip/images/arrow_r.png" alt=""/></i>
                                        </li>
                                    </a>

                                    <li class="dingdan_icon_delete">
                            <span class="delbtn" order_no="'.$row->order_no.'">
                                <img src="/Public/mgotrip/images/dingdan_icon_delete.png" alt=""/>
                            </span>
                                    </li>
                                </ul>
                            </div>';
                            }else if($row->status==2){
                                $str.='<div class="dn">
                                <ul class="in_items">
                                    <li class="items_t">
                                        <span class="oder_date">'.substr($row->date,0,10).'</span>
                                        <span class="oder_time">'.substr($row->date,11).'</span>
                                        <span class="oder_status fr" style="color:#808080;">已完成</span>
                                    </li>

                                    <a href="'.U('Order/orderDetail',array('orderNo'=>$row->order_no,'status'=>$status)).'">
                                        <li class="items_m"><!--order_details.html-->
                                            <p>
                                                <strong class="c4d">'.$row->shop.'</strong>
                                                <strong class="c333">订单号：'.$row->order_no.'</strong>
                                            </p>

                                            <i>
                                                <img class="fr" src="/Public/mgotrip/images/arrow_r.png" alt=""/>
                                            </i>
                                        </li>
                                    </a>

                                    <li class="dingdan_icon_delete">
                            <span class="delbtn" order_no="'.$row->order_no.'">
                                <img src="/Public/mgotrip/images/dingdan_icon_delete.png" alt=""/>
                            </span>
                                    </li>
                                </ul>
                            </div>';
                            }
                        }
                    }
                    echo $str;
                }
            }
        }
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