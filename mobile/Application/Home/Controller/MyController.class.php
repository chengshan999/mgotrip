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
        $params['mobile']=preg_replace('/(\d{3})(\d{4})(\d{4})/','$1'.'****'.'$3',session('user_data.mobile'));
        $this->assign($params);
        $this->display();
    }
    /*关于我们页面*/
    public function aboutUs(){
        $this->display('about_us');
    }
}