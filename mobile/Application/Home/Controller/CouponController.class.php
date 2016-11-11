<?php
namespace Home\Controller;
use Home\Controller;
class CouponController extends BaseController{
    /*我的优惠券页面*/
    public function coupon(){
        $page=I('get.page',1);
        $pagesize=C('PAGESIZE');
        //$status=I('get.status',0);
        $data0=array(
            'page'=>$page,
            'page_size'=>$pagesize,
            'status'=>0//未使用
        );
        $data1=array(
            'page'=>$page,
            'page_size'=>$pagesize,
            'status'=>1//已使用
        );
        $data2=array(
            'page'=>$page,
            'page_size'=>$pagesize,
            'status'=>-1//过期
        );
        $result0=$this->curlQuickPost(C('COUPON_LIST'),$data0);
        $result1=$this->curlQuickPost(C('COUPON_LIST'),$data1);
        $result2=$this->curlQuickPost(C('COUPON_LIST'),$data2);
        if($result0->status=='succ' && $result1->status=='succ' && $result2->status=='succ'){
            $rows0=$result0->data;
            $rows1=$result1->data;
            $rows2=$result2->data;
            if(!empty($rows0) && !empty($rows1) && !empty($rows2)){
                $params['rows0']=$rows0;
                $params['rows1']=$rows1;
                $params['rows2']=$rows2;
            }
        }else{
            $params['couponError']='优惠券信息获取失败';
        }
        //$params['status']=$status;
        $this->assign($params);
        $this->display();
    }
    /*激活优惠券*/
    public function activateCoupon(){
        if(IS_AJAX){
            $activateCode=I('post.aCode','');
            if($activateCode){
                $result=$this->curlQuickPost(C('COUPON_ACTIVATE'),array('code'=>$activateCode));
                /*$result=new \stdClass();
                $result->status='succ';*/
                if($result->status=='succ'){
                    echo 'succ';
                }else{
                    echo '激活失败:'.$result->msg?$result->msg:'未知';
                }
            }else{
                echo '激活码不能为空！';
            }
        }
    }
}