<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    /*默认首页*/
    public function index(){
        //获取当前城市
        $currentCity=I('get.city','上海');
        //$lng=I('get.lng/f',0);
        //$lat=I('get.lat/f',0);
        $currentAddr=I('get.currentAddr');
        /*$originSearchVlue=I('get.originSearchVlue','');
        if(empty($originSearchVlue)){
            $this->error('出错了，请重新操作！','/Index/index',3);
        }*/
        //保存起始站历史信息
//        cookie('histroyList',null);
/*        echo '<pre/>';
var_dump(unserialize(cookie('histroyList')));
        exit;*/

        $params['currentCity']=$currentCity;
        //$params['lng']=$lng;
        //$params['lat']=$lat;
        $params['currentAddr']=$currentAddr;
        //$params['originSearchVlue']=$originSearchVlue;

        $this->assign($params);
        $this->display();
    }

    /*保存搜索历史 需要ajax请求*/
    public function saveAddr(){
        if(IS_AJAX){
            $addr=I('post.addr/s');
            $lng=I('post.lng/f');
            $lat=I('post.lat/f');
            if($addr && $lng && $lat){
                $histroyList=unserialize(cookie('histroyList'));
                if(!empty($histroyList) && is_array($histroyList)){
                    if(!in_array($addr,$histroyList))
                        $histroyList[]=array(
                                'addr'=>$addr,
                                'lng'=>$lng,
                                'lat'=>$lat
                            );
                }else{
                    $histroyList=array();
                    $histroyList[]=array(
                        'addr'=>$addr,
                        'lng'=>$lng,
                        'lat'=>$lat
                    );;
                }

                cookie('histroyList',serialize($histroyList));
            }
        }else{
            return '非法请求！';
        }
    }

    /*获取历史纪录 需要ajax请求*/
    public function getAddr(){
        if(IS_AJAX){
            $histroyList=unserialize(cookie('histroyList'));
            $str='';
            if(!empty($histroyList) && is_array($histroyList)){
                foreach($histroyList as $v){
                    $str.='<li class="origin_histroyList_li" lng="'.$v['lng'].'"  lat="'.$v['lat'].'">'.$v['addr'].'</li>';
                }
                //echo json_encode($histroyList);
            }
            echo   $str;
        }
    }


    /*首页目的地输入栏*/
    public function dumpCookie(){
        dump(unserialize(cookie('histroyList')));
    }
    public function unsetCookie(){
        cookie('histroyList',null);
    }

}