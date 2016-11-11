<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    /*登陆入口*/
    public function login(){
        if(IS_AJAX){
            $mobile=I('post.mobile','');
            $vcode=I('post.vcode','');
            if($mobile && $vcode && preg_match('/^(1[3578]\d|14[57])[0-9]{8}$/',$mobile)){
                $curl=new \Think\Curl();
                //RSA_PUB_KEY
                $pubKey=openssl_pkey_get_public(C('RSA_PUB_KEY'));
                if($pubKey){
                    $encrypted='';
                    $device_no='';
                    openssl_public_encrypt($mobile.'&'.$device_no,$encrypted,$pubKey);
                    $data=array(
                        'device'=>'H5',
                        'data'=>base64_encode($encrypted),
                        'vcode'=>$vcode,
                        'version'=>'1.0'
                    );
                    $result=$curl->postData(C('USER_LOGIN'),$data);
                    /*object(stdClass)#7 (2)
                        {
                        ["status"]=> string(4) "succ"
                        ["data"]=>
                            object(stdClass)#8 (3) {
                            ["level"]=> int(0)
                            ["token"]=> string(32) "f6ef4d35acc64597102176d190c95ac8"
                            ["sort"]=> int(1) }
                        }*/
                    if($result->status && $result->status=='succ'){
                        //登陆成功，保存登陆信息，并返回信息
                        session('user_data.mobile',$mobile);
                        session('user_data.level',$result->data->level?$result->data->level:'');
                        session('user_data.token',$result->data->token?$result->data->token:'');
                        session('user_data.sort',$result->data->sort?$result->data->sort:'');
                        echo 'succ';
                    }else{
                        echo $result->msg?$result->msg:'登陆失败';
                    }
                }else{
                    echo '内部加密错误！';
                }
            }else{
                echo '请核对输入的内容！';
            }
        }
    }
    /*发送登陆验证码*/
    public function sendValidateCode(){
        if(IS_AJAX){
            $mobile=I('post.mobile','');
            if($mobile){
                if(preg_match('/^(1[3578]\d|14[57])[0-9]{8}$/',$mobile)){
                    $curl=new \Think\Curl();
                    $data=array(
                        'device'=>C('DEVICE'),
                        'mobile'=>$mobile,
                        'token'=>'',
                        'type'=>1,
                        'version'=>C('VERSION')
                    );
                    $result=$curl->postData(C('USER_VCODE_SEND'),$data);
                    if($result->status=='succ'){
                        echo '短信发送成功';
                    }
                }else{
                    echo '不存在的手机号！';
                }
            }else{
                echo '请输入手机号！';
            }
        }
    }
    /*取消登录*/
    public function outLogin(){
        if(IS_AJAX){
            session('user_data',null);
            $curl=new \Think\Curl();
            $data=array(
                'device'=>C('DEVICE'),
                'token'=>session('user_data.token'),
                'version'=>C('VERSION')
            );
            return $curl->postData(C('USER_OUTLOGIN'),$data);
        }
    }
}