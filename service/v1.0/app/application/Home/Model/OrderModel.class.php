<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 订单模型
 */
class OrderModel extends Model {

	/* 字段order_status */
	/* 字段order_status */
	const OS_NEW       = 0;  //初始状态
	const OS_CANCELED  = -1; //取消
	const OS_COMPLETED = 3;  //订单完成

	/* 字段pay_status */
	const PS_NEW      = 0; //初始状态
	const PS_PAYING   = 1; //支付中
	const PS_PAID     = 2; //已支付

	/* 字段other_pay_status */
	const OPS_NEW      = 0;  //初始状态
	const OPS_PAYING   = 1;  //支付中
	const OPS_PAID     = 2;  //已支付
	const OPS_RETURN   = -1; //已退还

	/* 字段is_delete */
	const ID_NEW		= 0;//初始状态
	const ID_DELETED	= 1;//已删除
	
	/* 字段result */
	const RESULT_NEW	= 0;//初始状态
	const RESULT_ING	= 1;//正在调派
	const RESULT_END	= 9;//派车结束
	const RESULT_CANCEL	= 2;//取消派车

	/*
	 * 生成支付订单号
	 * @access protected
	 */
	protected function createNo() {
		/* 选择一个随机的方案 */
		mt_srand((double) microtime() * 1000000);
		$order_no = "";
		do {
			$order_no = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
		} while(parent::where('order_no='.$order_no)->find());
		return $order_no;
	}

	/*
	 * 生成订单
	 * @access public
	 */
	public function insert($data=array(), $type='') {
		if (empty($data) || !is_array($data))
			return false;
		//生成订单号
		if (empty($data['order_no']))
			$data['order_no'] = $this->createNo();
		
		//状态初始值
		if (empty($data['pay_status']))
			$data['pay_status'] = self::PS_NEW;

		//$data['ip'] = get_client_ip();

		return parent::data($data)->add();
	}

	/*
	 * 返回订单id
	 * 考虑该模型多函数用id作为主键，主键查询快，这里给出id
	 * @access public
	 * @param string $order_no
	 * @param string $third_order_no
	 */
	public function getId($order_no) {
		$order_no = trim($order_no);

		if (!empty($order_no))
			return parent::where('order_no='.$order_no)->getField('id');
		else 
			return false;
	}

	/*
	 * 获取订单信息
	 * @access public
	 * @param int $id
	 * @param string $order_no
	 */
	public function getInfo($id=0,$order_no='') {
		$id = intval($id);
		$order_no = trim($order_no);
		
		if ($id>0) {
			$condition = array("id"=>$id);
		}
		else if (!empty($order_no)) {
			$condition = array("order_no"=>$order_no);
		}

		if (!$data = parent::where($condition)->find())
			return false;

		//判断是否需要支付，便于调用方处理            
		if (  $data['pay_status']!=self::PS_PAID ) {
			$data['need_pay'] = 1;
			$data['need_pay_amount'] = $data['amount']-$data['coupon_paid']-$data['money_paid'];
		}
		else $data['need_pay'] = 0; 

		return $data;
	}

	/*
	 * 取消订单
	 * @access public
	 * @param int $id 订单id
	 */
	public function cancel($id) {
		$id = intval($id);
		$condition = array(
			 "id" => $id,
		 );
		return parent::where($condition)->setField('order_status',self::OS_CANCELED);
	}

	/*
	 * 核实未支付商户订单号是否已存在
	 * @access public
	 * @param int $id 订单id
	 */
	public function checkExist($shop_id, $order_no) {
		$shop_id = intval($shop_id);
		$order_no = trim($order_no);
		$condition = array(
			"shop_id" => $shop_id,
			'pay_status' => array('neq',self::PS_NEW),
			);
		return parent::where($condition)->getField('id');
	}

	/*
	 * 设置订单属主
	 * @access public
	 * @param int $id 订单id
	 */
	public function assignOwner($id, $user_id) {
		$id = intval($id);
		$user_id = intval($user_id);
		$condition = array(
			"id" => $id,
			"user_id" => 0,
			);
		return parent::where($condition)->setField('user_id', $user_id);
	}

	/*
	 * 订单完成
	* @access public
	* @param string $order_no 订单号
	*/
	public function orderComplete($order_no) {
		$order_no = trim($order_no);
		if (!empty($order_no))
			return parent::where(array('order_no'=>$order_no))->setField('order_status',self::OS_COMPLETED);
		else
			return false;
	}

	/*
	 * 标记订单已完成
	 * @access public
	 * @param int $id 订单id
	 */
	public function completed($id) {
		$id = intval($id);
		$condition = array(
			 "id" => $id,
		 );
		return parent::where($condition)->setField('completed',self::COM_TD);
	}


	/*
	 * 订单详情
	* @access public
	* @param string $order_no 订单号
	*/
	public function getOrderInfo($order_no) {
		$order_no = trim($order_no);
		if (!empty($order_no)){
			return parent::alias('o')
				->join('__QS_ORDER_DETAIL__ d on o.id=d.order_id')
				->join('__SHOP__ s on o.shop_id=s.id')
				->field('amount,other_fee,car_no,create_time,destination,mdt_phone,driver_name,location,s.name as merchant,coupon_paid,money_paid,order_status')
				->where(array('order_no'=>$order_no))
				->find();
		}else
			return false;
	}

	/*
	 * 订单列表
	* @access public
	* @param int $user_id
	* @param int $page	
	* @param int $page_size
	* @param int $status
	*/
	public function getOrderList($user_id,$page,$page_size,$status) {
		$user_id	=	intval($user_id);
		$page		=	intval($page);
		$page_size	=	intval($page_size);
		if (empty($user_id)||empty($page)||empty($page_size))
			return false;
		else {
			
			//删除该用户未支付服务费的订单
			$noFeeList = parent::where(array('other_pay_status'=>self::OPS_NEW))->getField('id');
			if($noFeeList)
				parent::where(array('other_pay_status'=>self::OPS_NEW,'user_id'=>$user_id))->setField('is_delete',self::ID_DELETED);
			$condition=array();
			if($status!==''){
				$condition['order_status']	=	$status;
			}
			$condition['user_id']	=	$user_id;
			$condition['is_delete']	=	self::ID_NEW;
			$startNum				=	($page-1) * $page_size;
			return parent::alias('o')
				->join('__SHOP__ s on o.shop_id=s.id')
				->field('amount,create_time,s.logo as logo,s.name as merchant,order_status,order_no,other_pay_status')
				->where($condition)
				->order(array('o.id'=>'desc'))
				->limit($startNum,$page_size)
				->select();
				//return parent::getLastSql();
			
		}
	}

	
	/*
	* 未支付服务费、未取消、未删除的订单列表
	* @access public
	* @param int $user_id
	*/
	public function getUnpayList($user_id) {
		$user_id	=	intval($user_id);
		$condition = array(
			'other_fee'        =>array('GT',0),
			'other_pay_status' => array('EQ',self::OPS_NEW),
			'is_delete'        => array('EQ',self::ID_NEW),
			'order_status'     => array('NEQ',self::OS_CANCELED),
			'user_id'            => $user_id,
			);
		return parent::where($condition)->field('other_fee,order_no')->find();	
	}


	/*
	* 未完成订单
	* @access public
	* @param string $user_id 订单号
	*/
	public function getUnfinish($user_id){
		$user_id = intval($user_id);
		if (!empty($user_id)){
			$condition = array(
				'user_id'          => $user_id,
				'other_pay_status' => array('EQ',self::OPS_PAID),
				'order_status'     => array('EQ',self::OS_NEW),
				'result'           => array('IN',array(self::RESULT_NEW, self::RESULT_ING)),
				'UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(create_time)'  => array('ELT', 120),
			);
			return parent::where($condition)->field('create_time, order_no, 120-(UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(create_time)) remaining')->select();
		}else
			return false;
	}



	/*
	 * 订单删除
	* @access public
	* @param string $order_no 订单号
	*/
	public function orderDelete($order_no) {
		$order_no = trim($order_no);
		if (!empty($order_no))
			return parent::where(array('order_no'=>$order_no))->setField('is_delete',self::ID_DELETED);
		else
			return false;
	}

	/*
	 * 更新状态
	 * @access public
	 */
	public function update($id, $data=array()) {
		if (empty($data) || !is_array($data))
			return false;

		$id = intval($id);
		$condition = array(
			"id" => $id,
		);
				
		return parent::where($condition)->save($data);
	}

	/*
	* 获取商铺信息
	* @access public
	*/
	public function getShop() {
		return parent::alias('o')->join('__SHOP__ s on o.shop_id=s.id')->getField('s.id');
	}




}