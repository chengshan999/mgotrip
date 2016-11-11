<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller{
    public function __construct(){
        parent::__construct();
        $userData=session('user_data')?session('user_data'):array();
        if(!empty($userData['token']) && !empty($userData['mobile'])){
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

}