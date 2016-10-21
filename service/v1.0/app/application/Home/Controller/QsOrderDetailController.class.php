<?php 
// +----------------------------------------------------------------------
// | 摩购出行客户端系统 
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class QsOrderDetailController extends Controller {
    /*
	 * 默认函数 置空
	 */
	public function index(){}

	/* 
	 * 强生录单接口 出入参见文档
	 * @access public
	 */
	public function create(){
		$Curl = new \Think\Curl;

		//获取参数
		$data = $Curl->getData();
		\Think\Log::write(json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),'input data');

		//根据token签名参数，与用户系统信息交互，获取user_id
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		\Think\Log::write(json_encode($user_info),'user_info');
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}

		$Shop = D('Shop');
		$secret_key = $Shop->getSK('', $data->shop_id);

		//验证签名 	md5(partner_no+terminal_no+token+device_no+secrect_key)
		$sign_arr = array(
			"token"       => $data->token,
			"device_no"   => $device_no,
			"secret_key"  => $secret_key,
		);
		$sign = $Curl->createSign($sign_arr);
		\Think\Log::write("POST SIGN IS ".$data->sign." CREATE SIGN IS ".$sign,'sign info');
		if ($data->sign != $sign) {
			$this->ajaxReturn($Curl->failArr("E994"), "JSON");
		}

		//准备订单数据，生成订单
		$order_info = array(
			"shop_id"     => $data->shop_id,
			"user_id"     => $user_id,
			"other_fee"   => $data->fare_fee,//强生加价费放在other_fee字段
		);
		$Order = D("Order");
		if ( $data->fare_fee == 0 ) {//服务费为0的处理
			$order_info['other_pay_status'] = $Order::OPS_PAID;
		}
		if (!$order_id = $Order->insert($order_info)) {
			$this->ajaxReturn($Curl->failArr("E991"), "JSON");
		}
		
		//乘客信息：读取或保存
		$Passenger = D('QsPassenger');
		if (!empty($data->passenger_id) && intval($data->passenger_id)>0) {
			$passenger = $Passenger->getInfo($data->passenger_id);
			$data->name   = $passenger['name'];
			$data->gender = $passenger['gender'];
			$data->mobile = $passenger['mobile'];
		}
		else {
			if ( empty($data->name) || empty($data->gender) || empty($data->mobile) ) {
				$this->ajaxReturn($Curl->failArr("E711"), "JSON");
			}
			//核实数据库是否有相同的乘客信息，如果有返回该乘客信息  
			$passenger = $Passenger->checkPasenger($user_id,$data->name,$data->mobile,$data->gender);
			if(!$passenger){
				$pass_data = array(
					"user_id"    => $user_id,
					"name"       => $data->name,
					"gender"     => $data->gender,
					"mobile"     => $data->mobile,
					"is_default" => 1,
				);
				$Passenger->add_ps($pass_data);
			}else{
				\Think\Log::write(json_encode($passenger),'user_info');
				$data->name   = $passenger['name'];
				$data->gender = $passenger['gender'];
				$data->mobile = $passenger['mobile'];
			}
		}

		//保存强生约租车订单详情
		$qsorder_info = array(
			"order_id"        => $order_id,
			"location"        => $data->location,
			"longitude"       => preg_replace('/_/', '.', $data->longitude),
			"latitude"        => preg_replace('/_/', '.', $data->latitude),
			"landmark"        => $data->aboard_landmark,
			"near"            => $data->aboard_near,
			"name"            => $data->name,
			"gender"          => $data->gender,
			"mobile"          => $data->mobile,
			"destination"     => $data->destination,
			"car_kind"        => $data->car_kind,
			"car_level"       => $data->car_level,
			"car_type"        => $data->car_type,
			"radius"          => $data->radius,
		);
		if (!empty($data->desti_longitude)) {
			$qsorder_info['desti_longitude'] = preg_replace('/_/', '.', $data->desti_longitude);
		}
		if (!empty($data->desti_latitude)) {
			$qsorder_info['desti_latitude'] = preg_replace('/_/', '.', $data->desti_latitude);
		}
		
		$QsOrder = D("QsOrderDetail");
		if (!$QsOrder->insert($qsorder_info)) {
			$this->ajaxReturn($Curl->failArr("E982"), "JSON");
		}

		//如果服务费为0，这里自动发起强生录单
		if ( $data->fare_fee == 0 ) {//服务费为0的处理
			\Think\Log::write(C('QSORDER_RECORD'),'QSORDER_RECORD');
			$data = array(
				"status" => "succ",
				"auto_call"    => 1,
				"id"           => $order_id,
				"is_other_fee" => 1,
			);
			$Curl->postData(C('QSORDER_RECORD'), $data);//$Curl->succArr($data)
		}

		//获取最新订单信息
		$order_info = $Order->getInfo($order_id);

		//构造返回数据
		$ret_data = array(
			"amount"       => $order_info['other_fee'],
			"order_no"     => $order_info['order_no'],
			"order_id"     => $order_info['id'],
		);
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
    }


}