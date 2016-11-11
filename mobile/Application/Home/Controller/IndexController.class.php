<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function _initialize(){
        if(!empty(session('user_data.token')) && !empty(session('user_data.mobile'))){
            $this->assign('user_data',session('user_data'));
        }else{
            session('user_data',null);
        }
    }
    /*默认首页*/
    public function index(){
        //获取当前城市
        $currentCity=I('get.city','上海');
        $currentAddr=I('get.currentAddr');

        $params['currentCity']=$currentCity;
        $params['currentAddr']=$currentAddr;
        $params['carTypeUnlimited']=C('CAR_TYPE_UNLIMITED');
        $params['carTypeSangtana']=C('CAR_TYPE_SANGTANA');
        $params['carTypeTuan']=C('CAR_TYPE_TUAN');

        $this->assign($params);
        $this->display();
    }
    /*获取过往乘客信息列表*/
    public function getPassengerList(){
        if(IS_AJAX){
            $result=$this->curlQuickPost(C('PASSENGER_LIST'));
            if($result->status=='succ'){
                $list=$result->data;
                if(!empty($list) && is_object($list)){
                    $str='';
                    foreach($list as $k=>$v){
                        $str.='<li '.$k==0?'class="select"':''.'>
				               <span class="grays">乘客：</span>
				               <span class="n_names">'.$v->name.'（'.$v->gender==C("SEX_LADY")?"女士":"男士".'）</span>
				               <span class="tel">'.$v->mobile.'</span> <i class="arrow_right"></i>
			                   </li>';
                    }
                    echo $str;
                }
            }
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
    /*保存搜索历史 需要ajax请求*/
    public function saveAddr(){
        if(IS_AJAX){
            $addr=I('post.addr/s');
            $lng=I('post.lng/f');
            $lat=I('post.lat/f');
            $act=I('post.act/s','');
            //判断是存储起始位置还是目的地位置
            if($act && $act=='org'){
                if($addr && $lng && $lat){
                    $orgHistroyList=unserialize(cookie('orgHistroyList'));
                    if(!empty($orgHistroyList) && is_array($orgHistroyList)){
                        if(!in_array($addr,$orgHistroyList))
                            $orgHistroyList[]=array(
                                'addr'=>$addr,
                                'lng'=>$lng,
                                'lat'=>$lat
                            );
                    }else{
                        $orgHistroyList=array();
                        $orgHistroyList[]=array(
                            'addr'=>$addr,
                            'lng'=>$lng,
                            'lat'=>$lat
                        );;
                    }

                    cookie('orgHistroyList',serialize($orgHistroyList));
                }
            }else if($act && $act=='des'){
                if($addr && $lng && $lat){
                    $desHistroyList=unserialize(cookie('desHistroyList'));
                    if(!empty($desHistroyList) && is_array($desHistroyList)){
                        if(!in_array($addr,$desHistroyList))
                            $desHistroyList[]=array(
                                'addr'=>$addr,
                                'lng'=>$lng,
                                'lat'=>$lat
                            );
                    }else{
                        $desHistroyList=array();
                        $desHistroyList[]=array(
                            'addr'=>$addr,
                            'lng'=>$lng,
                            'lat'=>$lat
                        );;
                    }

                    cookie('desHistroyList',serialize($desHistroyList));
                }
            }
        }
    }

    /*获取历史纪录 需要ajax请求*/
    public function getAddr(){
        if(IS_AJAX){
            $act=I('get.act/s','');
            if($act && $act=='org'){
                $orgHistroyList=unserialize(cookie('orgHistroyList'));
                $str='';
                if(!empty($orgHistroyList) && is_array($orgHistroyList)){
                    foreach($orgHistroyList as $v){
                        $str.='<li class="origin_histroyList_li" lng="'.$v['lng'].'"  lat="'.$v['lat'].'">'.$v['addr'].'</li>';
                    }
                }
                echo   $str;
            }else if($act && $act=='des'){
                $desHistroyList=unserialize(cookie('desHistroyList'));
                $str='';
                if(!empty($desHistroyList) && is_array($desHistroyList)){
                    foreach($desHistroyList as $v){
                        $str.='<li class="des_histroyList_li" lng="'.$v['lng'].'"  lat="'.$v['lat'].'">'.$v['addr'].'</li>';
                    }
                }
                echo   $str;
            }

        }
    }
    /*清空历史纪录，AJAX请求*/
    public function unsetHistroyListCookie(){
        if(IS_AJAX){
            $act=I('get.act/s','');
            if($act && $act=='org'){
                cookie('orgHistroyList',null);
            }else if($act && $act=='des'){
                cookie('desHistroyList',null);
            }
        }
    }

}