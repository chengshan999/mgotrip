<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller{
    public function __construct(){
        parent::__construct();
        $userData=session('user_data')?session('user_data'):array();
        if(!empty($userData['token'])){
            $this->assign('user_data',$userData);
        }else{
            session('user_data',null);
            redirect('Index/index');
        }
    }
    /*curl快捷使用方法*/
    public function curlQuickPost($url,$arr=array()){
        $curl=new \Think\Curl();
        $data=array_merge($arr,array(
            'device'=>C('DEVICE'),
            'token'=>session('user_data.token'),
            'version'=>C('VERSION')
        ));
        return $curl->postData($url,$data);
    }
    /*推出临时活动，随活动的更改而更改，
    写在基类中，失败返回false，成功返回带HTML标签的字串
    用到时直接调用并把返回的字串输出到模板
    
    当前活动：2016酒节活动
    */
    public function activity(){//ACTIVITY_DISPLAY
        /*object(stdClass)#14 (3) {
          ["status"] => string(4) "succ"
          ["msg"] => string(0) ""
          ["data"] => object(stdClass)#15 (5) {
            ["desc"] => string(84) "摩购出行，凡2016-12-01到2017-01-10 乘坐摩购出租车的，好礼赢不停"
            ["img"] => string(61) "http://pic.test.yufu365.com/mgotrip/activity/winefest2016.png"
            ["link"] => string(20) "http://m.yufu365.com"
            ["logo"] => string(0) ""
            ["title"] => string(31) "庆双“旦”,打车赢好礼"
          }
        }*/
        $re=$this->curlQuickPost(C('ACTIVITY_DISPLAY'));
        if(!empty($re->data)){
            $link=!empty($re->data->link)?$re->data->link:'';
            $img=!empty($re->data->img)?$re->data->img:'';
            if($link && $img){
                $str='<div id="activity_div" style="width:100%;position:fixed;bottom:0px;left:0px;z-index:999"><img onclick="window.location.href=\''.$link.'\';" width="100%" src="'.$img.'"/><span style="display:block;font:16px Microsoft YaHei;color:#fff;position:absolute;top:0px;right:0px;" onclick="document.getElementById(\'activity_div\').style.display=\'none\';">关闭</span></div>';
                return $str;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}