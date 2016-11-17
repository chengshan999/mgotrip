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
        $currentCity=I('get.city',C('FIRST_CITY'));
        $currentAddr=I('get.currentAddr');

        $params['currentCity']=$currentCity;
        $params['currentAddr']=$currentAddr;
        $params['carTypeUnlimited']=C('CAR_TYPE_UNLIMITED');
        $params['carTypeSangtana']=C('CAR_TYPE_SANGTANA');
        $params['carTypeTuan']=C('CAR_TYPE_TUAN');
        $params['sexMan']=C('SEX_MAN');
        $params['sexLady']=C('SEX_LADY');
        $params['payTypeZhifubao']=C('PAY_TYPE_ZHIFUBAO');

        $this->assign($params);
        $this->display();
    }
    /*生成表单*/
    public function createOrder(){
        if(IS_AJAX){
            $car_type=I('post.car_type/d');
            $aboard_near=I('post.aboard_near/s','');
            $desti_latitude=I('post.desti_latitude/f','');
            $desti_longitude=I('post.desti_longitude/f','');
            $destination=I('post.destination/s','');
            $fare_fee=I('post.fare_fee/d',0);
            $latitude=I('post.latitude/f');
            $longitude=I('post.longitude/f');
            $location=I('post.location/s');
            $mobile=I('post.mobile/s','');
            $name=I('post.name/s','');
            $gender=I('post.gender/d','');
            $order_no=I('post.order_no/s','');
            $passenger_id=I('post.passenger_id/d','');
            $pay_type=I('post.pay_type/d',0);
            if($latitude && $longitude && $location){
                if($passenger_id || ($mobile && $name && $gender)){
                    $params=array(
                            'aboard_landmark'=>'',
                            'radius'=>'',
                            'car_kind'=>1,
                            'car_level'=>0,
                            'aboard_near'=>$aboard_near,
                            'car_type'=>$car_type,
                            'desti_latitude'=>$desti_latitude,
                            'desti_longitude'=>$desti_longitude,
                            'destination'=>$destination,
                            'fare_fee'=>$fare_fee,
                            'latitude'=>$latitude,
                            'longitude'=>$longitude,
                            'location'=>$location,
                            'mobile'=>$mobile,
                            'name'=>$name,
                            'gender'=>$gender,
                            'order_no'=>$order_no,
                            'passenger_id'=>$passenger_id,
                            'shop_id'=>C('SHOP_ID_QS'),
                            'sign'=>md5(session('user_data.token').C('SECRECT_KEY'))
                        );
                    $result=$this->curlQuickPost(C('ORDER_CREATE'),$params);
                    if($result->status=='succ'){
                        $data=$result->data;
                        $amount=$data->amount?$data->amount:0;
                        $orderNo=$data->order_no?$data->order_no:'';
                        $orderId=$data->order_id?$data->order_id:'';
                        if($amount){
                            if($pay_type && $orderId){
                                //PAYMENT_REDIRECT
                                $re_pay_service=$this->curlQuickPost(C('PAYMENT_REDIRECT'),array(
                                    'is_other_fee'=>1,
                                    'order_id'=>$orderId,
                                    'pay_amount'=>$amount,
                                    'pay_corporation_id'=>$pay_type
                                ));
                                if($re_pay_service->status=='succ'){
                                    $code=json_decode($re_pay_service->data);
                                    $url=$code->url;
                                    $method=$code->method;
                                    if($url && strtolower($method)=='get'){
                                        //GET方式请求支付宝
                                        //保存打车起始和终止位置的经纬度,供以后的页面使用
                                        $this->saveStartAndEndPositionToCookie($longitude,$latitude,$desti_longitude,$desti_latitude);
                                        unset($code->url);
                                        unset($code->method);
                                        $params=http_build_query($code);
                                        echo $url.$params;
                                    }
                                }
                            }
                        }else{
                            //不需要支付，直接返回等车页面URL，让前端跳转，进入等车页面
                            $this->saveStartAndEndPositionToCookie($longitude,$latitude,$desti_longitude,$desti_latitude);
                            $url=U('Taxi/takeTaxi');
                            echo $url;
                        }
                    }
                }
            }
        }
    }
    /*把当前生成的订单起始和终止位置保存到cookie*/
    public function saveStartAndEndPositionToCookie($sLng,$sLat,$eLng,$eLat){
        $arr=array(
            'sLng'=>$sLng,
            'sLat'=>$sLat,
            'eLng'=>$eLng,
            'eLat'=>$eLat
        );
        cookie('startToEndPosition',null);
        cookie('startToEndPosition',json_encode($arr));
    }
    /*获取过往乘客信息列表*/
    public function getPassengerList(){
        if(IS_AJAX){
            $result=$this->curlQuickPost(C('PASSENGER_LIST'));
            /*object(stdClass)#7 (3) {
              ["status"] => string(4) "succ"
              ["msg"] => string(0) ""
              ["data"] => array(1) {
                [0] => object(stdClass)#8 (4) {
                  ["id"] => string(2) "35"
                  ["name"] => string(4) "lynn"
                  ["gender"] => string(1) "1"
                  ["mobile"] => string(11) "15900586059"
                }
              }
            }*/
            if($result->status=='succ'){
                $list=$result->data;
                if(!empty($list) && is_array($list)){
                    $str='';
                    foreach($list as $k=>$v){
                        if($k==0){
                            $sex=$v->gender==C("SEX_LADY")?"女士":"男士";
                            $str.='<li class="select" passenger_id="'.$v->id.'" name="'.$v->name.'" gender="'.$v->gender.'" mobile="'.$v->mobile.'">
				               <span class="grays">乘客：</span>
				               <span class="n_names">'.$v->name.'（'.$sex.'）</span>
				               <span class="tel">'.$v->mobile.'</span> <i class="arrow_right"></i>
			                   </li>';
                        }else{
                            $sex=$v->gender==C("SEX_LADY")?"女士":"男士";
                            $str.='<li passenger_id="'.$v->id.'" name="'.$v->name.'" gender="'.$v->gender.'" mobile="'.$v->mobile.'">
				               <span class="grays">乘客：</span>
				               <span class="n_names">'.$v->name.'（'.$sex.'）</span>
				               <span class="tel">'.$v->mobile.'</span> <i class="arrow_right"></i>
			                   </li>';
                        }
                    }
                    echo $str;
                }
            }
        }
    }
    /*获取服务费信息*/
    public function getServiceCharge(){
        if(IS_AJAX){
            $result=$this->curlQuickPost(C('SERVICE_CHARGE'));
            /*object(stdClass)#7 (3) {
              ["status"] => string(4) "succ"
              ["msg"] => string(0) ""
              ["data"] => array(5) {
                [0] => object(stdClass)#8 (2) {
                  ["amount"] => string(1) "0"
                  ["state"] => string(18) "要的就是免费"
                }
                [1] => object(stdClass)#9 (2) {
                  ["amount"] => string(1) "1"
                  ["state"] => string(13) "1分也是爱"
                }
                [2] => object(stdClass)#10 (2) {
                  ["amount"] => string(3) "100"
                  ["state"] => string(21) "爷，大方如你！"
                }
                [3] => object(stdClass)#11 (2) {
                  ["amount"] => string(4) "1000"
                  ["state"] => string(30) "这么豪爽，车马上到！"
                }
                [4] => object(stdClass)#12 (2) {
                  ["amount"] => string(4) "2000"
                  ["state"] => string(30) "土豪，我们做朋友吧！"
                }
              }
            }*/
            if($result->status=='succ'){
                $rows=$result->data;
                if(!empty($rows) && is_array($rows)){
                    $str='<dd></dd><dd></dd>';
                    foreach($rows as $k=>$v){
                        if($k==0){
                            $str.='<dd>
                                    <div class="circle_round">'.$v->amount*0.01.'</div>
                                    <p class="info_text">'.$v->state.'</p>
                                </dd>';
                        }else{
                            $str.='<dd>
                                        <div>'.$v->amount*0.01.'</div>
                                        <p class="info_text" style="display:none;">'.$v->state.'</p>
                                    </dd>';
                        }
                    }
                    $str.='<dd></dd><dd></dd>';
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