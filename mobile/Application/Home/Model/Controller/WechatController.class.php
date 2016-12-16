<?php
namespace Home\Controller;
use Think\Controller;
class WechatController extends Controller{
    public function syncLogin(){
        $code=I('get.code');
        if($code){
            //已授权，用code换取openid
            /*请求链接：https://api.weixin.qq.com/sns/oauth2/access_token?   WECHAT_GET_ACCESS_TOKEN
            appid=APPID&     WECHAT_APPID
            secret=SECRET&    WECHAT_APPSECRET
            code=CODE&
            grant_type=authorization_code*/
            /*返回数据：{
               "access_token":"ACCESS_TOKEN",
               "expires_in":7200,
               "refresh_token":"REFRESH_TOKEN",
               "openid":"OPENID",
               "scope":"SCOPE"
            }或者
            {"errcode":40029,"errmsg":"invalid code"}*/
            $url_get_openid=C('WECHAT_GET_ACCESS_TOKEN').'?appid='.C('WECHAT_APPID').'&secret='.C('WECHAT_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
            $result=file_get_contents($url_get_openid);
            $arr = $result?json_decode($result,true):array();
            if($arr){
                if(isset($arr['openid']) && !empty($arr['openid'])){
                    $openid=$arr['openid'];
                }
            }
            if(isset($openid)){
                //使用openid作为用户登录的依据并请求获取
                $curl=new \Think\Curl();
                $data=array(
                    'weixin_id'=>$openid,
                    'version'=>C('VERSION'),
                    'device'=>C('DEVICE')
                );
                $re=$curl->postData(C('USER_LOGIN'),$data);
                if($re->status=='succ'){
                    //用户不是第一次使用微信登录，保存登录信息
                    session('user_data.level',isset($re->data->level)?$re->data->level:'');
                    session('user_data.token',$re->data->token?$re->data->token:'');
                    session('user_data.sort',$re->data->sort?$re->data->sort:'');
                    session('user_data.weixin_id',$re->data->weixin_id?$re->data->weixin_id:'');
                    //跳转到首页
                    $index=U('Index/index');
                    header('location:'.$index);
                }else{
                    //登录失败，用户是第一次登录，让用户输入手机号和密码，加上openid再次请求接口
                    $this->assign('weixin_id',$openid);
                    $this->display();
                }
            }
        }else{
            //用户取消授权或其他异常状态，返回到首页
            $index=U('Index/index');
            header('location:'.$index);
        }
    }
    /*第一次用微信登录，绑定手机号登录*/
    public function wachatFirstLogin(){
        if(IS_AJAX){
            $mobile=I('post.mobile','');
            $vcode=I('post.vcode','');
            $weixin_id=I('post.weixin_id','');
            if($mobile && $vcode && preg_match('/^(1[3578]\d|14[57])[0-9]{8}$/',$mobile)){
                $curl=new \Think\Curl();
                //RSA_PUB_KEY
                $pubKey=openssl_pkey_get_public(C('RSA_PUB_KEY'));
                if($pubKey){
                    $encrypted='';
                    $device_no='';
                    openssl_public_encrypt($mobile.'&'.$device_no,$encrypted,$pubKey);
                    $data=array(
                        'device'=>C('DEVICE'),
                        'data'=>base64_encode($encrypted),
                        'vcode'=>$vcode,
                        'version'=>C('VERSION'),
                        'weixin_id'=>$weixin_id
                    );
                    $result=$curl->postData(C('USER_LOGIN'),$data);
                    if($result->status && $result->status=='succ'){
                        //登陆成功，保存登陆信息，并返回信息
                        session('user_data.level',isset($result->data->level)?$result->data->level:'');
                        session('user_data.token',$result->data->token?$result->data->token:'');
                        session('user_data.sort',$result->data->sort?$result->data->sort:'');
                        session('user_data.weixin_id',$result->data->weixin_id?$result->data->weixin_id:'');
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
}