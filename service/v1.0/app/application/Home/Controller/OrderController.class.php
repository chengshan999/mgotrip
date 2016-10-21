<?php 
// +----------------------------------------------------------------------
// | 摩购出行客户端系统 订单管理
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class OrderController extends Controller {
    /*
	 * 默认函数 置空
	 */
	public function index(){}
	

	/* 
	 * 订单支付状态查询接口
	 */
	public function payStatus() {
		$Curl = new \Think\Curl;

		$data = $Curl->getData();

		//根据token签名参数，与用户系统信息交互，获取user_id
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}
		\Think\Log::write(json_encode($user_info),'user_info');
		//调用模型，获取信息
		$Order = D("Order");
        $order_info = $Order->getInfo(0,$data->order_no);


		if ($order_info == false) {
			$this->ajaxReturn($Curl->failArr("E201"), "JSON");
		}
		else if ($order_info['user_id'] != $user_id) {
			$this->ajaxReturn($Curl->failArr("E202"), "JSON");
		}
		$ret_data = array(
			"amount"          => $order_info['amount'],
			"coupon_amount"   => $order_info['coupon_paid'],
			"paid_amount"     => $order_info['money_paid'],
			"pay_status"      => $order_info['pay_status'],
			"need_pay_amount" => $order_info['need_pay_amount'],
			"order_no"        => $order_info['order_no'],
		);
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
	}


	/*
	 * 订单支付打车费
	 * @access public
	 * 入参：
	 * @param int 	$amount
	 * @param string $coupon
	 * @param string $order_no
	 */
	public function PayFare(){
		$Curl = new \Think\Curl;
	
		//获取参数
		$data		= $Curl->getData();
		\Think\Log::write(json_encode($data),'input data');
		//根据token签名参数，与用户系统信息交互，获取user_id
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}

		$amount		= !empty($data->amount)	   ? intval($data->amount) : "" ;
		$coupon_id	= !empty($data->coupon_id) ? trim($data->coupon_id)   : "" ;
		$order_no	= !empty($data->order_no)  ? trim($data->order_no) : "" ;

		//核实订单是否存在及订单属主
		$Order = D("Order");
		$order_info = $Order->getInfo(0,$order_no);
		
		if (!$order_info) {
			$this->ajaxReturn($Curl->failArr("E706"), "JSON");
		}
		if ($order_info['user_id'] != $user_id) {
			$this->ajaxReturn($Curl->failArr("E707"), "JSON");
		}
		if ($order_info['order_status'] == $Order::OS_CANCELED) {
			$this->ajaxReturn($Curl->failArr("E224"), "JSON");
		}
		else if ($order_info['order_status'] == $Order::OS_COMPLETED) {
			$this->ajaxReturn($Curl->failArr("E104"), "JSON");
		}
		if ($order_info['pay_status'] == $Order::PS_PAID) {
			$this->ajaxReturn($Curl->failArr("E216"), "JSON");
		}

		//获取优惠券信息
		if ($coupon_id) {
			$UserCoupon = D("UserCoupon");
			$coupon_info = $UserCoupon->getInfo($coupon_id);
			if ($coupon_info===false || $coupon_info===null) {
				$this->ajaxReturn($Curl->failArr("E219"), "JSON");
			}
			else if ($coupon_info['min_order_amount']>intval($amount)) {
				$this->ajaxReturn($Curl->failArr("E212"), "JSON");
			}
			else if ($coupon_info['user_id']!=$user_id) {
				$this->ajaxReturn($Curl->failArr("E220"), "JSON");
			}
			else if ($coupon_info['order_id']>0) {
				$this->ajaxReturn($Curl->failArr("E233"), "JSON");
			}
			else if ($coupon_info['use_end_date']<date("Y-m-d")) {
				$this->ajaxReturn($Curl->failArr("E234"), "JSON");
			}
		}
		
		//更新订单金额、流水号及优惠券信息
		$order_update_arr = array(
			"amount"		=> $amount,
			"coupon_id"		=> isset($coupon_info['id']) ? $coupon_info['id'] : 0,
			"coupon_paid"	=> isset($coupon_info['amount']) ? $coupon_info['amount'] : 0,
		);
		if (isset($coupon_info['amount']) && $coupon_info['amount']>=$amount) 
		{
			$order_update_arr['pay_status'] = $Order::PS_PAID;
		}
		$order_update_result = $Order->update($order_info['id'],$order_update_arr);
		if(false === $order_update_result){
			$this->ajaxReturn($Curl->failArr("E979"), "JSON");//订单金额更新失败
		}

		//打车费金额小于优惠券金额 更新订单为已完成
		if (isset($coupon_info['amount']) && $coupon_info['amount']>=$amount) 
		{
			$Order->orderComplete($order_info['order_no']);

			//付款成功通知强生
			if ($order_info['shop_id']==1) {
				$QsOrder   = D("QsOrderDetail");
				$qs_detail = $QsOrder->getInfo(0, $order_info['id']);
				$qsnotice_data = array(
						"Operation"		=> "NoticePaySuccess",
						"Order_Id"  	=> $order_info['third_order_no'],
						"Transact_Id"	=> $order_info['id'],
						"Transact_Time"	=> date('YmdHis'),
						"Pay_Amount"	=> sprintf("%.2f", $amount/100),
						"Mobile"		=> $qs_detail['mobile'],
				);
				\Think\Log::write(json_encode($qsnotice_data),'qs pay notice post');
				$qs_return = $Curl->postData(C('QSYZC_INTERFACE'), $qsnotice_data);
				\Think\Log::write(json_encode($qs_return),'qs pay notice res');
			}
		}

		//修改优惠券为已使用
		if ($coupon_id) {
			$update = array(
				"order_id"  => $order_info['id'],
				"used_time" => date("Y-m-d H:i:s")
			);
			$coupon_update_result = $UserCoupon->update($coupon_id, $update);
			if(false === $coupon_update_result){
				$this->ajaxReturn($Curl->failArr("E978"), "JSON");//优惠券信息更新失败
			}
		}
		
		//构造返回数据
		$ret_data = array(
			"amount"		=> isset($coupon_info['amount']) ? ($amount-$coupon_info['amount']<0 ? 0 : $amount-$coupon_info['amount'] ) : $amount,
			"order_amount"	=> $amount,
			"order_no"		=> $order_info['order_no'],
			"order_id"		=> $order_info['id'],
		);
		
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
	}
	

	/*
	 * 判断当前用户是否有未完成订单
	 * @access public
	 * 入参：
	 * @param string $token
	 */
	public function orderUnfinish()
	{
		$Curl = new \Think\Curl;

		//获取参数
		$data = $Curl->getData();
		\Think\Log::write(json_encode($data),'input data');
		//根据token签名参数，与用户系统信息交互，获取user_id
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device" => $data->device, "token" => $data->token));
		if ($user_info == false || $user_info->status == "fail") {
			$this->ajaxReturn($Curl->failArr(!empty($user_info->msg) ? $user_info->msg : "E203"), "JSON");
		} else {
			$user_id = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}
		//核实是否有未完成订单
		$Order = D("Order");
		$unfinish_order = $Order->getUnfinish($user_id);
		\Think\Log::write(json_encode($unfinish_order),'unfinish_order');
		$ret_data = array();
		if ($unfinish_order) {
			$unfinish_order = current($unfinish_order);
			$temp = array(
					"order_no"    		=> $unfinish_order['order_no'],
					"time_remaining"	=> $unfinish_order['remaining'],
			);
			$ret_data[] = $temp;
		}
		\Think\Log::write(json_encode($ret_data),'ret_data');
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON", JSON_UNESCAPED_UNICODE);
	}
	
	
	/*
     * 订单完成
    * @access public
    * 入参：
    * @param 	order_no      string    订单编号
    */
    public function orderComplete(){
		$Curl = new \Think\Curl;
    
    	//获取参数
    	$data		= $Curl->getData();
    	$order_no	= !empty($data->order_no)	? trim($data->order_no) : "" ;

   
    	//强生订单要向支付系统提交两次支付，所以服务费的提交特别处理订单号，返回也需要恢复原样；
    	//$order_no = preg_match('/'.C('QSYZC_FARE_SUFFIX').'/', $data->order_no) ? substr($data->order_no, 0 , '-'.strlen(C('QSYZC_FARE_SUFFIX'))) : $data->order_no;
  
    	//根据token签名参数，与用户系统信息交互，获取user_id
    	$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
    	if ($user_info==false || $user_info->status=="fail") {
    		$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
    	}
    	else {
    		$user_id   = $user_info->data->user_id;
    	}

    	//核实订单是否存在及订单属主
    	$Order = D('Order'); 
    	
    	if (!$order_info = $Order->getInfo(0, $order_no)) {
    		$this->ajaxReturn($Curl->failArr("E706"), "JSON");
    	}
    	if ($order_info['user_id'] != $user_id) {
    		$this->ajaxReturn($Curl->failArr("E707"), "JSON");
    	}
    	
    	$Order	=	D('Order');
    	$orderComplete=$Order->orderComplete($order_no);
    	if ($orderComplete === false) {
			$this->ajaxReturn($Curl->failArr("E706"), "JSON");
		}
    	//构造返回数据
    	$this->ajaxReturn($Curl->succArr(), "JSON");
    	 
    }

	/*
     * 订单删除
    * @access public
    * 入参：
    * @param 	order_no      string    订单编号
    */
    public function orderDelete(){
    	$Curl = new \Think\Curl;
    
    	//获取参数
    	$data		= $Curl->getData();
		$order_no	= !empty($data->order_no)	? trim($data->order_no) : "" ;
    	//$order_no = preg_match('/'.C('QSYZC_FARE_SUFFIX').'/', $data->order_no) ? substr($data->order_no, 0 , '-'.strlen(C('QSYZC_FARE_SUFFIX'))) : $data->order_no;
    	//根据token签名参数，与用户系统信息交互，获取user_id
    	$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
    	if ($user_info==false || $user_info->status=="fail") {
    		$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
    	}
    	else {
    		$user_id   = $user_info->data->user_id;
    	}
    	//核实订单是否存在及订单属主
    	$Order = D('Order');
    	if (!$order_info = $Order->getInfo(0, $order_no)) {
    		$this->ajaxReturn($Curl->failArr("E706"), "JSON");
    	}
    	if ($order_info['user_id'] != $user_id) {
    		$this->ajaxReturn($Curl->failArr("E707"), "JSON");
    	}
    	 
    	$Order	=	D('Order');
    	$orderDelete=$Order->orderDelete($order_no);
    	if ($orderDelete == false) {
    		$this->ajaxReturn($Curl->failArr("E706"), "JSON");
    	}
    
    	//构造返回数据
    	$success = array('status' => 'succ','msg' =>''); 
    	$this->ajaxReturn($success ,'JSON');
    }

	/*
     * 订单列表
    * @access public
    * 入参：
    * @param int $user_id
	* @param int $page	
	* @param int $page_size
	* @param int $status
    */
    public function getList(){
    	$Curl = new \Think\Curl;
    
    	//获取参数
    	$data		= $Curl->getData();
    	
    	//根据token签名参数，与用户系统信息交互，获取user_id
    	$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
    	if ($user_info==false || $user_info->status=="fail") {
    		$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
    	}
    	else {
    		$user_id   = $user_info->data->user_id;
    	}

    	$page		= !empty($data->page)		? intval($data->page) : 1 ;
    	$page_size	= !empty($data->page_size)	? intval($data->page_size) : 10 ;
		$status		= !empty($data->status)		? intval($data->status) : "" ;
		$Order	=	D('Order');
		
		//入参与数据表状态值转换
		if ( intval($data->status)==-1 )
			$status = $Order::OS_CANCELED;
		else if ( intval($data->status)==0 )
			$status = '';
		else if ( intval($data->status)==1 )
			$status = $Order::OS_NEW;
		else if ( intval($data->status)==2 )
			$status = $Order::OS_COMPLETED;
	
    	$orderList=$Order->getOrderList($user_id,$page,$page_size,$status);
    	if ($orderList === false) {
			$this->ajaxReturn($Curl->failArr("E714"), "JSON");
		}
		//构造返回数据
		$ret_data=array();
		if ($orderList) {
			foreach($orderList as $k=>$v) {
				//状态值反转换
				//
				
				if ($v['order_status']==$Order::OS_NEW)
					$temp_status = 1;
				else if ($v['order_status']==$Order::OS_CANCELED)
					$temp_status = -1;
				else if ($v['order_status']==$Order::OS_COMPLETED)
					$temp_status = 2;
			
				$temp = array(
					"amount"	=> $v['amount'],
					"date"		=> $v['create_time'],
					"logo"		=> !empty($v['logo']) ? C('PIC_URL').$v['logo'] : '',
					"shop"	    => $v['merchant'],
					"order_no"	=> $v['order_no'],
					"status"	=> $temp_status,
				);
				$ret_data[] = $temp;
				unset($temp);
				unset($temp_status);
			}
		}
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

	
	/*
     * 未支付服务费，未删除，未取消订单列表
    * @access public
    * 入参：
    * @param int $user_id
	* @param int $page	
    */
    public function getUnpayserviceList(){
    	$Curl = new \Think\Curl;
    
    	//获取参数
    	$data		= $Curl->getData();
    	
    	//根据token签名参数，与用户系统信息交互，获取user_id
    	$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
    	if ($user_info==false || $user_info->status=="fail") {
    		$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
    	}
    	else {
    		$user_id   = $user_info->data->user_id;
    	}
		\Think\Log::write(json_encode($user_id),'user_id');
		$Order	=	D('Order');
		$unpayserviceList=$Order->getUnpayList($user_id);
		\Think\Log::write(json_encode($unpayserviceList),'unpayservice');
		//构造返回数据
		if ($unpayserviceList) {
				$ret_data = array(
					"amount"	=> $unpayserviceList['other_fee'],
					"order_no"	=> $unpayserviceList['order_no'],
				);
		}else{
				$ret_data = array();
		}
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }


	/*
     * 订单详情
     * @access public
     * 入参：
     * @param 	order_no      string    订单编号
     */
	public function orderInfo(){
    	
    	$Curl = new \Think\Curl;
    	
    	//获取参数
    	$data		= $Curl->getData();
    	$order_no	= !empty($data->order_no)	? trim($data->order_no) : "" ;
    	//根据token签名参数，与用户系统信息交互，获取user_id
    	$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
    	if ($user_info==false || $user_info->status=="fail") {
    		$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
    	}
    	else {
    		$user_id   = $user_info->data->user_id;
    	} 
    	//核实订单是否存在及订单属主
    	$Order = D('Order');
    	if (!$order_info = $Order->getInfo(0, $order_no)) {
    		$this->ajaxReturn($Curl->failArr("E706"), "JSON");
    	}
    	if ($order_info['user_id'] != $user_id) {
    		$this->ajaxReturn($Curl->failArr("E707"), "JSON");
    	}
    	$orderInfo=$Order->getOrderInfo($order_no);
    	
    	if ($orderInfo === false) {
			$this->ajaxReturn($Curl->failArr("E706"), "JSON");
		}
		//拼接订单详情字符串
		$desc='车牌号：'.$orderInfo["car_no"].'\r\n司机姓名：'.$orderInfo["driver_name"].'\r\n联系电话：'.$orderInfo["mdt_phone"].'\r\n出发地：'.$orderInfo["location"].'\r\n目的地：'.$orderInfo["destination"] ;
		//构造返回数据
		if ($orderInfo) {
			$ret_data = array(
				"date"			=> $orderInfo['create_time'],
				"desc"			=> $desc,
				"shop"		    => $orderInfo['merchant'],
				"order_no"		=> $order_no,
				"paid_amount"	=> $orderInfo['money_paid']+$orderInfo['other_fee'],
				"privil_amount"	=> $orderInfo['coupon_paid'],
				"status"		=> $orderInfo['order_status'],
				"total_amount"	=> $orderInfo['amount']+$orderInfo['other_fee'],
			);
		}
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }



}