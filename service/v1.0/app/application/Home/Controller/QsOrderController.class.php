<?php 
// +----------------------------------------------------------------------
// | 摩购出行客户端系统 强生侧功能管理
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class QsOrderController extends Controller {
    /*
	 * 默认函数 置空
	 */
	public function index(){}

	/* 
	 * 接收服务费支付成功通知，发起强生电调系统订单录入
	 * @access public
	 */
	public function backNotify(){
		$Curl = new \Think\Curl;

		$data = $Curl->getData();
		\Think\Log::write(json_encode($data),'input data');

		if (!empty($data->auto_call) && $data->auto_call==1) {//这组调用参数来自创建订单时服务费为0时，代码位于QsOrderDetailController.php wjn 2016/9/22 星期四
			$order_id     = $data->id;
			$is_other_fee = $data->is_other_fee;
		}
		else {//调用java 支付宝回参处理接口并接收 再后续处理
			\Think\Log::write(file_get_contents('php://input'),'php://input');

			$post_data = array(
				"device"             => "APP", //按地址区分吧，该接口只传APP
				"data"               => json_decode(file_get_contents('php://input'), true),
				"order_id"           => "",
				"pay_corporation_id" => 1,//1支付宝
			);
			\Think\Log::write(json_encode($post_data),'post_data');
			$data = $Curl->getReturn(C('ALIPAY_NOTIFY'), $post_data);
			\Think\Log::write(json_encode($data),'ret data');

			$order_id = $data->data->id;
			$is_other_fee     = $data->data->is_other_fee;
		}

		if ($data->status=="succ") {
			//核实订单是否存在
			$Order = D('Order');
			if (!$order_info = $Order->getInfo($order_id)) {
				\Think\Log::write("订单不存在",'Error');
			}
			else {
				\Think\Log::write(json_encode($order_info),'order_info');
			}
			
			if ($is_other_fee == 1) {//仅是支付服务费的时候，发起订单录入操作，需要做标记，以防重复发起

				if ($order_info['other_pay_status']==2 && $order_info['reserved']==0 ) {//服务费完全支付且未发起过通知录入时
					$QsOrder   = D("QsOrderDetail");
					$qs_detail = $QsOrder->getInfo(0, $order_info['id']);
					$Shop      = D('Shop');
					$shop      = $Shop->getInfo('', $order_info['shop_id']);
		
					//数据切分  2016/1/26  暂时切不准确，待改正------------
					if (preg_match('/^(.+路)/', $qs_detail['location'], $matches)) 
						$Get_On_Road = $matches[1];
					if (preg_match('/路(.+)弄/', $qs_detail['location'], $matches)) 
						$Lane        = $matches[1];
					if (preg_match('/[路弄](.+)号/', $qs_detail['location'], $matches)) 
						$Road_No     = $matches[1];
		
					$url = C('QSYZC_INTERFACE');
					$post_data = array(
						"Operation"     => "NeedCar",
						"Transact_Id"   => $order_info['id'],
						"Transact_Time" => date('YmdHis'),
						"Transact_From" => $shop['partner_no'],
						"Get_On_Road"   => '',//isset($Get_On_Road) ? $Get_On_Road : '',//待切分传值
						"Lane"          => '',//isset($Lane) ? $Lane : '',//待切分传值
						"Road_No"       => '',//isset($Road_No) ? $Road_No : '',//待切分传值
						"Near"          => $qs_detail['near'],
						"Land_Mark"     => '',
						"Pick_Up"       => $qs_detail['location'],
						"Longitude"     => $qs_detail['longitude'],
						"Latitude"      => $qs_detail['latitude'],
						"Customer_Name" => $qs_detail['name'],
						"Gender"        => $qs_detail['gender']-1,//性别值做转换
						"Call_Back"     => $qs_detail['mobile'],
						"Destination"   => $qs_detail['destination'],
						"Fare_Fee"      => sprintf('%.2f', ($order_info['other_fee']-$shop['service_fee']) ),
						"Car_No"        => "",
						"Service_No"    => "",
						"Mobile"        => "",
						"Return_Url"    => C('QSYZC_RETURN_URL'),
						"Notify_Url"    => C('QSYZC_NOTIFY_URL'),
						"Get_Off_Lo"    => "",
						"Get_Off_La"    => "",
						//"Car_Kind"      => $qs_detail['car_kind'],
						//"Car_Level"     => $qs_detail['car_level'],
						"Car_Type"      => $qs_detail['car_type'],
						"radius"        => "999999",//空，默认值
					);
					\Think\Log::write($url,'qs order url');
					\Think\Log::write(json_encode($post_data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),'qs order post');
					$return = $Curl->postData($url, $post_data);
					\Think\Log::write(json_encode($return),'qs order res');
					
					//标记此单已经发起录入，防止重复发起
					$update = array(
						"reserved" 	=> 1,
					);
					$Order->update($order_info['id'],$update);
				}
			}
			else //支付打车费 标记订单已完成
			{
				$Order->orderComplete($order_info['order_no']);

				//付款成功通知强生
				$QsOrder   = D("QsOrderDetail");
				$qs_detail = $QsOrder->getInfo(0, $order_info['id']);
				$qsnotice_data = array(
						"Operation"		=> "NoticePaySuccess",
						"Order_Id"  	=> $order_info['third_order_no'],
						"Transact_Id"	=> $order_info['id'],
						"Transact_Time"	=> date('YmdHis'),
						"Pay_Amount"	=> sprintf("%.2f", $order_info['amount']/100),
						"Mobile"		=> $qs_detail['mobile'],
				);
				\Think\Log::write(json_encode($qsnotice_data),'qs pay notice post');
				$qs_return = $Curl->postData(C('QSYZC_INTERFACE'), $qsnotice_data);
				\Think\Log::write(json_encode($qs_return),'qs pay notice res');
			}
		}
	}
	
	
	/* 
	 * 接收强生电调系统订单录入结果反馈
	 * @access public
	 * 范例结果返回：
	 * {"Transact_Time":"20160126065825","Operation":"SendResult","Transact_Id":"89","Order_Id":"2834","Transact_Status":"NeedCar"}
	 */
	public function qsReturn(){

		\Think\Log::write(json_encode(I('post.')),'post data');

		if ( I('post.Operation')=='SendResult' && I('post.Transact_Status')=='NeedCar') {
			//保存查询结果到订单
			$Order   = D("Order");
			$update = array(
				"third_order_no"  => I('post.Order_Id'),
				"transact_status" => json_encode(I('post.')),
			);

			$Order->update(I('post.Transact_Id'), $update);
			$Curl = new \Think\Curl;
			$order_info = $Order->getInfo(I('post.Transact_Id'),'');
			\Think\Log::write(json_encode($order_info),'order info');
			
			//在这里判断如果订单状态为已取消，发起强生调度系统订单取消
			if($order_info['order_status']==$Order::OS_CANCELED){
				$post_data = array(
					"Operation"	=> "HTTPCancelOrder",
					"Order_Id"  => $order_info['third_order_no'],
					"Reason"	=> '0',
				);
				\Think\Log::write(json_encode($post_data),'post_data');
				$cancel_return = $Curl->postData(C('QSYZC_INTERFACE'), $post_data);
				\Think\Log::write(json_encode($cancel_return),'cancel_return');
			}
		}
		
	}

	/* 
	 * 发起强生电调系统订单查询，并将（前一）查询结果返回调用端
	 * @access public
	 */
	public function qsQuery(){
		$Curl = new \Think\Curl;

		//获取参数
		$data = $Curl->getData();
		\Think\Log::write(json_encode($data),'input datas');
		//根据token签名参数，与用户系统信息交互，获取user_id
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}

		//核实订单是否存在及订单属主
		$Order = D('Order');
		if (!$order_info = $Order->getInfo(0, $data->order_no)) {
			$this->ajaxReturn($Curl->failArr("E706"), "JSON");
		}
		if ($order_info['user_id'] != $user_id) {
			$this->ajaxReturn($Curl->failArr("E707"), "JSON");
		}
		if($order_info['third_order_no']!=null){
			$Shop = D('Shop');
			$shop = $Shop->getInfo('', $order_info['shop_id']);

			$url = C('QSYZC_INTERFACE');
			$post_data = array(
					"Operation"     => "Inquire",
					"Transact_Id"   => $order_info['id'],
					"Transact_Time" => date('YmdHis'),
					"Transact_From" => $shop['partner_no'],
					"Order_Id"      => $order_info['third_order_no'],
					"Notify_Url"    => C('QSYZC_NOTIFY_URL'),
			);
			\Think\Log::write(json_encode($post_data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),'post_data');
			$return = $Curl->postData($url, $post_data);
			\Think\Log::write(json_encode($return),'return');
		}
		//构造返回数据
		$ret_data = array(
			"car_no"      => $order_info['car_no'],
			"current_la"  => $order_info['current_la'],
			"current_lo"  => $order_info['current_lo'],
			"driver_name" => $order_info['driver_name'],
			"mdt_phone"   => $order_info['mdt_phone'],
			"result"      => $order_info['result'],
		);
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
	}

	/* 
	 * 接收强生电调系统订单查询结果反馈
	 * @access public
	 * {"Result":"1","Transact_Time":"20160126065931","Operation":"Notify","Order_Lo":"121.455","Order_La":"31.2315","Driver_Name":"","Transact_Id":"89","Mdt_Phone":"","Car_No":"\u65e0","Current_La":"0","Order_Id":"2834","Current_Lo":"0"}
	 */
	public function qsNotify(){
		
		\Think\Log::write(json_encode(I('post.')),'input data');
		if ( I('post.Operation')=='Notify') {
			//保存查询结果到订单
			$Order   = D("Order");
			if(I('post.Result')==$Order::RESULT_END){
				$update = array(
					"result"      => I('post.Result'),
					"car_no"      => I('post.Car_No'),
					"driver_name" => I('post.Driver_Name'),
					"mdt_phone"   => I('post.Mdt_Phone'),
					"current_lo"  => I('post.Current_Lo'),
					"current_la"  => I('post.Current_La'),
					"driver_no"  => I('post.Driver_No'),
					"is_result_end"  => 1,
				);
			}elseif (I('post.Result')==$Order::RESULT_CANCEL){
				$update = array(
					"result"      => I('post.Result'),
				);
			}
			\Think\Log::write(json_encode($update),'update data');
			$Order->update(I('post.Transact_Id'), $update);
		}
	}


	/*
	 * 强生订单取消接口  兼容定时取消功能
	 * @access public
	 * 入参：
	 * @param order_no     string 订单号
	 * 返参：
	 */
	public function cancel(){
		$Curl = new \Think\Curl;
		
		//获取参数
		$data		= $Curl->getData();
		$order_no 	= $data->order_no;
		//根据token签名参数，与用户系统信息交互，获取user_id
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}
		$Order = D('Order');
		$order_info = $Order->getInfo(0,$order_no);

		//核实订单是否存在及订单属主
		if (!$order_info) {
			$this->ajaxReturn($Curl->failArr("E706"), "JSON");
		}
		if ($order_info['user_id'] != $user_id) {
			$this->ajaxReturn($Curl->failArr("E707"), "JSON");
		}
		
		//未调派或正在调派，需要服务费退款
		if ($order_info['result'] != $Order::RESULT_END) {
			//解决方案：生成退款记录，后续人工操作 暂行 2016/9/20 星期二
			$refund_data = array(
				"user_id"      => $user_id,
				"order_id"     => $order_info['id'],
				"order_no"     => $order_info['order_no'],
				"time_apply"   => date("Y-m-d H:i:s"),
				"amount_apply" => $order_info['other_fee'],
				"remark_apply" => "",
			);
			$Refund = D('refund');
			$Refund->insert($refund_data);
		} 
		//订单取消操作
		if ( !$Order->cancel($order_info['id']) ) {
			$this->ajaxReturn($Curl->failArr("E731"), "JSON");
		}
		
		//如果强生录单已反馈，发起强生调度系统订单取消，否则就在录单反馈接口判断，再进行取消
		if(!empty($order_info['third_order_no'])){
			$post_data = array(
				"Operation"	=> "HTTPCancelOrder",
				"Order_Id"  => $order_info['third_order_no'],
				"Reason"	=> '0',
			);
			\Think\Log::write(json_encode($post_data),'post_data');
			$cancel_return = $Curl->postData(C('QSYZC_INTERFACE'), $post_data);
			\Think\Log::write(json_encode($cancel_return),'cancel_return');
		}

		//构造返回数据
		$ret_data = array(
			"is_refunded"	=> $order_info['result']==$Order::RESULT_END ? 0 : 1 ,
		);
		//构造返回数据
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
	
	}


	
	
	



}