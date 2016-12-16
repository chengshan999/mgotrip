<?php
namespace Home\Controller;
use Home\Controller;
class MyController extends BaseController{
    /*用户中心首页*/
    public function my(){
        $result=$this->curlQuickPost(C('COUPON_ALLLIST'));
        /*object(stdClass)#7 (3) {
          ["status"]=>
          string(4) "succ"
          ["msg"]=>
          string(0) ""
          ["data"]=>
          object(stdClass)#8 (1) {
            ["total"]=>
            int(0)
          }
        }*/
        if($result->status=='succ'){
            $params['totalCoupon']=$result->data->total?$result->data->total:0;
        }
        $re_getUserInfo=$this->curlQuickPost(C('USER_INFO'));

        if($re_getUserInfo->status=='succ'){
            $mobile=$re_getUserInfo->data->mobile?$re_getUserInfo->data->mobile:'';
        }
        //呈现酒节活动
        $re_activity=$this->activity();
        $params['activity']='';
        if($re_activity != false){
            $params['activity']=$re_activity;
        }
        $params['mobile']=!empty($mobile)?preg_replace('/(\d{3})(\d{4})(\d{4})/','$1'.'****'.'$3',$mobile):'';
        $this->assign($params);
        $this->display();
    }
    /*关于我们页面*/
    public function aboutUs(){
        $this->display('about_us');
    }
}