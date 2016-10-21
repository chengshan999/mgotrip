<?php 
// +----------------------------------------------------------------------
// | 摩购出行客户端系统 优惠券管理
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class CouponController extends Controller {
    /*
	 * 默认函数 置空
	 */
	public function index(){}


	/* 获取针对订单商户和金额的可用优惠券列表 */
	public function getCouponList(){
		$Curl = new \Think\Curl;
		$user_id    = isset($data->user_id) ? intval($data->user_id) : 0;
		$shop_id    = isset($data->shop_id) ? intval($data->shop_id) : 0;
		$min_amount = isset($data->min_amount) ? intval($data->min_amount) : 0;

		$Coupon      = D('Coupon');
		$coupon_list = $Coupon->getUnusedList($user_id, $shop_id, $min_amount);
		if ($coupon_list === false) {
			$this->ajaxReturn($Curl->failArr("E701"), "JSON");
		}

		//构造返回数据
		$ret_data   = array();
		if ($coupon_list) {
			foreach($coupon_list as $k=>$v) {
				$temp = array(
					"id"          => $v['id'],
					"name"        => $v['name'],
					"code"        => $v['code'],
					"amount"      => $v['amount'],
					"min_amount"  => $v['min_amount'],
				);
				$ret_data[] = $temp;
				unset($temp);
			}
		}
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON", JSON_UNESCAPED_UNICODE);
	}

	/* 获取某用户的全部券列表（分页） */
	public function couponAll() {
		$Curl = new \Think\Curl;

		//获取参数
		$data = $Curl->getData();
		$status	= !empty($data->status)	? trim($data->status) : "" ;
		$page   = !empty($data->page)  ?  trim($data->page) : 1;
		$page_size = !empty($data->page_size)  ? trim($data->page_size) :10;
		//根据token签名参数，与用户系统信息交互，获取user_id，device_no
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}
		//获取代金券列表数据
		$Coupon = D("UserCoupon");
		$ret_data = array();
		if ($coupon_list = $Coupon->getList($user_id, $page, $page_size, $status)) {
			if ($coupon_list['data_list']) {
				foreach ($coupon_list['data_list'] as $coupon) {
					$temp['code']           = $coupon['code'];
					$temp['name']           = $coupon['name'];
					$temp['shop_name']      = $coupon['shop_name'];
					$temp['amount']         = $coupon['amount'];
					$temp['use_start_date'] = $coupon['use_start_date'];
					$temp['use_end_date']   = $coupon['use_end_date'];
					$temp['min_order_amount'] = $coupon['min_order_amount'];
					$temp['pic']            = !empty($coupon['pic']) ? C('PIC_URL').$coupon['pic'] :'';
					$ret_data[] = $temp;
					unset($temp);
				}
			}
		}
		else {
			$this->ajaxReturn($Curl->failArr("E701"), "JSON");
		}


		$ret_data = array(
			"total" => !empty($coupon_list['total']) ? $coupon_list['total'] : 0,
			"coupon_list" => $ret_data,
		);
		//print_r($Curl->succArr($ret_data));
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
	}

	
	/* 券激活 */
	public function active() {
		$Curl = new \Think\Curl;

		//获取参数
		$data = $Curl->getData();
		
		//根据token签名参数，与用户系统信息交互，获取user_id，device_no
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}
		$ExchangeCode = D("ExchangeCoupon");
		//获取激活码数据
		if ( !$codeInfo = $ExchangeCode->getInfo($data->code) ) {
			$this->ajaxReturn($Curl->failArr("E703"), "JSON");//无法获取信息
		}
		if ( $codeInfo['user_id']>0 && $codeInfo['user_id'] == $user_id  ) {
			$this->ajaxReturn($Curl->failArr("E705"), "JSON");//其他用户已激活
		}
		else if ( $codeInfo['user_id']>0 && $codeInfo['user_id'] != $user_id ) {
			$this->ajaxReturn($Curl->failArr("E704"), "JSON");//当前用户已激活
		}
		else if ( $codeInfo['deadline'] < date("Y-m-d H:i:s") ) {
			$this->ajaxReturn($Curl->failArr("E751"), "JSON");//已过期
		}
		else if ( $codeInfo['status']==$ExchangeCode::S_CANCELED ) {
			$this->ajaxReturn($Curl->failArr("E752"), "JSON");//已作废
		}
		if ( $ExchangeCode->checkUser($user_id, $codeInfo['batch']) ) {
			$this->ajaxReturn($Curl->failArr("E747"), "JSON");//仅可激活一次
		}
		//更新兑换码状态
		if ( $ExchangeCode->used($codeInfo['id'], $user_id) ) {

			//读取代金券类型数据
			$CouponType = D("CouponType");
			if ( !$type_list = $CouponType->getList($codeInfo['batch']) ) {
				$this->ajaxReturn($Curl->failArr("E746"), "JSON");
			}
			$Coupon     = D("UserCoupon");
			//发放代金券
			foreach ($type_list as $c_type) {
				$temp = array();
				$temp['type_id'] = $c_type['id'];
				$temp['user_id'] = $user_id;
				if ( !$Coupon->insert($temp) ) {
					$this->ajaxReturn($Curl->failArr("E745"), "JSON");
				}
				unset($temp);
			}
		}
		else {
			$this->ajaxReturn($Curl->failArr("E744"), "JSON");
		}
		$ret_data = array();
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON");

	}

	/* 获取某用户的全部可用券列表（不分页）支付打车费确认页面调用 */
	public function couponAllList() {
		$Curl = new \Think\Curl;

		//获取参数
		$data = $Curl->getData();

		//根据token签名参数，与用户系统信息交互，获取user_id，device_no
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}

		$Coupon      = D('UserCoupon');
		$coupon_list = $Coupon->getAllList($user_id,5);
		if ($coupon_list === false) {
			$this->ajaxReturn($Curl->failArr("E701"), "JSON");
		}

		//构造返回数据
		$ret_data = array();
		$ret_data['total'] = count($coupon_list);
		if ($coupon_list !== null) {
			foreach($coupon_list as $k=>$v) {
				$temp = array(
					"id"               => $v['id'],
					"name"             => $v['name'],
					"amount"           => $v['amount'],
					"code"             => $v['code'],
					"use_start_date"   => $v['use_start_date'],
					"use_end_date"     => $v['use_end_date'],
					"min_order_amount" => $v['min_order_amount'],
					"pic"              => !empty($v['pic']) ? C('PIC_URL').$v['pic'] : '',
				);
				$ret_data['list'][] = $temp;
				unset($temp);
			}
		}
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON", JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
	}
}