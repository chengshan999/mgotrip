<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 交易
 */
class TransactionModel extends Model {

	/* 字段status */
	const STA_NEW     = 0;  //初始状态
	const STA_SUCCESS = 1;  //交易成功
	const STA_FAILED  = -1; //交易失败

	/* 字段completed */
	const COM_NEW     = 0;  //初始状态
	const COM_RECEIVE = 1;  //已完成

	/*
	 * 生成支付订单号
	 * @access protected
	 */
	protected function createNo() {
		/* 选择一个随机的方案 */
		mt_srand((double) microtime() * 1000000);
		return date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
	}

	/*
	 * 生成交易记录
	 * @access public
	 * @param array $data
	 */
	public function insert($data=array()) {
		if (empty($data) || !is_array($data))
			return false;
		$insert['trans_no']     = $this->createNo();
		$insert['serial_no']    = !empty($data['serial_no']) ? trim($data['serial_no']) : '';
		$insert['order_id']     = !empty($data['order_id']) ? intval($data['order_id']) : 0;
		$insert['order_no']     = !empty($data['order_no']) ? trim($data['order_no']) : '';
		$insert['corpor_id']    = !empty($data['corpor_id']) ? intval($data['corpor_id']) : 0;
		$insert['card_no']      = !empty($data['card_no']) ? trim($data['card_no']) : '';
		$insert['user_id']      = !empty($data['user_id']) ? intval($data['user_id']) : 0;
		$insert['amount']       = !empty($data['amount']) ? intval($data['amount']) : 0;
		$insert['status']       = !empty($data['status']) ? intval($data['status']) : self::STA_NEW;
		$insert['send_msg']     = !empty($data['send_msg']) ? trim($data['send_msg']) : '';
		$insert['response_msg'] = !empty($data['response_msg']) ? trim($data['response_msg']) : '';
		return parent::data($insert)->add();
	}

	/*
	 * 卡公司回调支付成功，记录回调信息，修改交易状态
	 * @access public
	 * 
	 */
	public function paySuccess($id,$data) {
		$data['status'] = self::STA_SUCCESS;
		return parent::where('id='.$id)->setField($data);
	}

	/*
	 * 卡公司回调支付失败，记录回调信息，修改交易状态
	 * @access public
	 * 
	 */
	public function payFail($id,$data) {
		$data['status'] = self::STA_FAILED;
		return parent::where('id='.$id)->setField($data);
	}

	/*
	 * 根据商城系统返回信息，修改交易记录为已完成  
	 * @access public
	 */
	public function completed($id,$serial_no) {
		$id        = intval($id);
		$serial_no = trim($serial_no);
		if ($id > 0)
			return parent::where('id='.$id)->setField('completed',self::COM_RECEIVE);
		else if (!empty($serial_no))
			return parent::where('serial_no='.$serial_no)->setField('completed',self::COM_RECEIVE);
	}

	/*
	 * 更新交易信息  
	 * @access public
	 */
	public function update($id,$data) {
		$id = intval($id);
		return parent::where('id='.$id)->setField($data);
	}

	/*
	 * 获取交易信息
	 * @access public
	 * @param int $id
	 * @param string $order_no
	 */
	public function getInfo($id=0,$trans_no='') {
		$id = intval($id);
		$trans_no = trim($trans_no);
		if ($id>0) {
			$condition = array("id"=>$id);
		}
		else if (!empty($trans_no)) {
			$condition = array("trans_no"=>$trans_no);
		}
		return parent::where($condition)->find();
	}


	/*
	  * 根据字段获取交易信息
	  */
	public function getInfoByField($condition) {
		if (!is_array($condition) || empty($condition)) {
			return false;
		}

		return parent::where($condition)->find();
	}


	/*
    * 某用户的支付总额查询
    * @access public
    * @param int $user_id
    */
	public function summation($user_id,$year='',$month='') {
		$user_id = intval($user_id);
		$year    = intval($year);
		$month   = intval(preg_replace('/^0/','',trim($month)));
		$condition = array(
				"user_id" => $user_id,
				"status"  => self::STA_SUCCESS,
		);
		if ($year>0 && $month>0) {
			$begin_date = strtotime($year.'-'.$month.'-01');
			if ($month==12)
				$end_date   = strtotime(($year+1).'-01-01')-1;
			else
				$end_date   = strtotime($year.'-'.($month+1).'-01')-1;
			$condition['unix_timestamp(`create_time`)'] = array('between',array($begin_date,$end_date));
		}
		else if ($year>0) {
			$begin_date = strtotime($year.'-01-01');
			$end_date   = strtotime(($year+1).'-01-01')-1;
			$condition['unix_timestamp(`create_time`)'] = array('between',array($begin_date,$end_date));
		}
		return parent::where($condition)->sum('amount');
	}

		
	/*
    * 交易记录按时间段查询
    * @access public
    * @param int $user_id
    * @param int $begin_date
    * @param int $end_date
    */
	public function recordQueryByDate($user_id,$begin_date="",$end_date="",$page=1,$page_size=10) {
		$user_id = intval($user_id);

		$condition = array(
				"t.user_id"     => $user_id,
				"t.status"      => self::STA_SUCCESS,
		);

		if ($begin_date && $end_date) {
			$condition['unix_timestamp(t.`create_time`)'] = array('between',array($begin_date,$end_date));
		}
		else if ($begin_date) {
			$condition['unix_timestamp(t.`create_time`)'] = array('gt',$begin_date);
		}
		else if ($end_date) {
			$condition['unix_timestamp(t.`create_time`)'] = array('lt',$end_date);
		}

		//获取总记录数 及 偏移量
		$total = parent::alias('t')->where($condition)->count();
		if($total>0){
			$total_page = ceil(intval($total)/$page_size);
			if ($page > $total_page) $page = $total_page;
			$offset = ($page-1)*$page_size;

			$data_list = parent::alias('t')
					->join('__ORDER__ o on t.order_id=o.id', 'LEFT')
					->join('__SHOP__ s on o.shop_id=s.id', 'LEFT')
					->where($condition)->order('id desc')->limit($offset, $page_size)->
					getField('t.id,t.trans_no,t.order_id,t.order_no,t.amount,t.pay_type,t.create_time,s.partner_no,s.logo,s.name shop_name');
			//返回数据
			$return['total'] = $total;
			$return['trans_list'] = $data_list;
		}else{
			$return['total'] = '0';
			$return['trans_list'] = '';
		}
		return $return;
	}

	/*
    * 交易记录按订单查询
    * @access public
    * @param int $user_id
    * @param int $begin_date
    * @param int $end_date
    */
	public function recordQueryByOrder($id) {
		$id = intval($id);

		$condition = array(
				"order_id"  => $id,
				"status"    => self::STA_SUCCESS,
		);
		return parent::where($condition)->order('id')->getField('trans.id,trans.corpor_id,trans_no,order_no,amount,create_time,card_code,card_name,card_logo,corporation_name,corporation_logo');
	}



	/*
     * 获取用户综合交易记录
     */
	public function synthesisTransList($user_id, $page=1, $page_size=20, $begin_date="",$end_date="") {
		$user_id   = intval($user_id);
		$page      = intval($page);
		$page_size = intval($page_size);
		$begin_date = trim($begin_date);
		$end_date 	= trim($end_date);
		if ($user_id <= 0 )
			return false;

		if(!empty($begin_date) && !empty($end_date)){
			$trans_where = " and create_time between '".$begin_date."' and '".$end_date."'";
			$refund_where = " and time_refund between '".$begin_date."' and '".$end_date."'";

			$trans_where1 = " and t.create_time between '".$begin_date."' and '".$end_date."'";
			$refund_where1 = " and r.time_refund between '".$begin_date."' and '".$end_date."'";

		}
		$Refund   = D('Refund');
		$total = M()->query("select sum(total) as total from ((select count(*) as total
							from tb_transaction where user_id=".$user_id." and status=".self::STA_SUCCESS.$trans_where.")
							union all
							(select count(*) as total
							from tb_refund where user_id=".$user_id." and refund_status=".$Refund::RS_REFUND.$refund_where."))a");
		
		$total = $total[0]['total'];
		$total_page = ceil(intval($total)/$page_size);
		if ($page < 1)
			$page = 1;
		else if ($page>$total_page)
			//$page = $total_page;
			return false;//返回空，不获取数据

		$offset = ($page-1)*$page_size;
		$list = M()->query("(select t.id,t.order_id,o.shop_id,t.order_no,t.amount,t.create_time as trans_time,1 as trans_type
							from tb_transaction as t left join tb_order as o on t.order_id=o.id where t.user_id=".$user_id." and t.status=".self::STA_SUCCESS.$trans_where1.")
							union all 
							
							(select r.id,r.order_id,o.shop_id,r.order_no,r.amount_actual as amount,r.time_refund as trans_time,3
							from tb_refund as r left join tb_order as o on r.order_id=o.id where r.user_id=".$user_id." and r.refund_status=".$Refund::RS_REFUND.$refund_where1.")
							order by trans_time desc limit $offset,$page_size");
	
		$return['total'] = $total;
		$return['data_list'] = $list;
		return $return;
	}



}