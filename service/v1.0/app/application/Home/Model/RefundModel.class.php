<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

/**
 * 退款记录日志
 */
class RefundModel extends Model {

	/* 字段refund_status */
	const RS_NEW      = 0;  //初始状态
	const RS_REJECTED = -1; //拒绝退款
	const RS_REFUND   = 1;  //已退款

	/*
	 * 生成退款单号
	 * @access protected
	 */
	protected function createNo() {
		/* 选择一个随机的方案 */
		mt_srand((double) microtime() * 1000000);
		$refund_no = "";
		do {
			$refund_no = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
		} while(parent::where('refund_no='.$refund_no)->find());
		return $refund_no;
	}

	/*
	 * 增加退款记录
	 */
	public function insert($data='') {
		if (empty($data))
			return false;

		//生成退款单号
		if (empty($data['refund_no']))
			$data['refund_no'] = $this->createNo();

		if (empty($data['refund_status'])) {
			$data['refund_status'] = self::RS_NEW;
		}
		return parent::data($data)->add();
	}

	/*
	 * 获取退款单信息
	 * @access public
	 * @param int $id
	 * @param string $order_no
	 */
	public function getInfo($id=0,$refund_no='') {
		$id = intval($id);
		$refund_no = trim($refund_no);
		
		if ($id>0) {
			$condition = array("id"=>$id);
		}
		else if (!empty($refund_no)) {
			$condition = array("refund_no"=>$refund_no);
		}

		return parent::where($condition)->find();
	}

	/*
	 * 更新退款操作
	 */
	 public function refund($id,$data) {
		$id = intval($id);
		if ($id<=0)
			return false;
		$data['refund_status'] == self::RS_REFUND;
		return parent::fetchSql(true)->where('id='.$id)->setField($data);
	}

	/*
	 * 获取列表
	 */
	public function getList($order_id=0, $return_id=0) {
		
		$order_id = intval($order_id);
		$return_id = intval($return_id);
		if ($order_id<0 || $return_id<0)
			return false;

		if ($order_id>0) {
			$condition = array(
				"order_id" => $order_id,
			);
		}
		else if ($return_id>0) {
			$condition = array(
				"return_id" => $return_id,
			);
		}
		return parent::where($condition)->order('time_apply')->select();
	}

	/*
	 * 管理后台 退款单列表
	 * @access public
	 */
	public function adminSearchList($search) {
		if (!is_array($search)) {
			return false;
		}
		$page =  !empty($search['page']) ? ( intval($search['page'])>0 ? intval($search['page']) : 1 ) : 1 ;
		$page_size =  !empty($search['page_size']) ? ( intval($search['page_size'])>0 ? intval($search['page_size']) : 20 ) : 20 ;

		$condition = array();

		if (!empty($search['order_no'])) {
			$condition['r.order_no'] = trim($search['order_no']);
		}
		if (!empty($search['status'])) {
			//入参状态值：-1：全部；0：未退款；1：已退款；2：审核失败；
			if ($search['status'] == 0) {
				$condition['r.refund_status'] = self::RS_NEW;
			}
			else if ($search['status'] == 1) {
				$condition['r.refund_status'] = self::RS_REFUND;
			}
			else if ($search['status'] == 2) {
				$condition['r.refund_status'] = self::RS_REJECTED;
			}
		}
		if (!empty($search['user_id'])) {
			$condition['r.user_id'] = intval($search['user_id']);
		}
		//起止时间检索
		if (!empty($search['begin_date']) && !empty($search['end_date'])) {
			$condition['r.time_apply'] = array('between',array($search['begin_date'], $search['end_date']));
		}
		else if (!empty($search['begin_date'])) {
			$condition['r.time_apply'] = array('gt', $search['begin_date'] );
		}
		else if (!empty($search['end_date'])) {
			$condition['r.time_apply'] = array('lt', $search['end_date'] );
		}

		//获取总记录数 及 偏移量
		$total = parent::alias('r')->where($condition)->count();
		$total_page = ceil(intval($total)/$page_size);
		if ($total_page>0 && $page>$total_page) $page = $total_page;
		$offset = ($page-1)*$page_size;
		$data_list = parent::alias('r')->where($condition)->limit($offset, $page_size)->select();

		//返回数据
		$return['total'] = $total;
		$return['data_list'] = $data_list;
		return $return;
	}


	/*
	 * 某用户的退款总额查询
	 * @access public
	 * @param int $user_id
	 */
	 public function summation($user_id,$year='',$month='') {
		 $user_id = intval($user_id);
		 $year    = intval($year);
		 $month   = intval(preg_replace('/^0/','',trim($month)));
		 // var_dump($year);
		 // var_dump($month);
		 // exit;
		 $condition = array(
			 "user_id" => $user_id,
			 "refund_status"  => self::RS_REFUND, 
			 );
		 if ($year>0 && $month>0) {
			 $begin_date = strtotime($year.'-'.$month.'-01');
			 if ($month==12)
				 $end_date   = strtotime(($year+1).'-01-01')-1;
			 else
				 $end_date   = strtotime($year.'-'.($month+1).'-01')-1;
			 $condition['unix_timestamp(`time_refund`)'] = array('between',array($begin_date,$end_date));
		 }
		 else if ($year>0) {
			 $begin_date = strtotime($year.'-01-01');
			 $end_date   = strtotime(($year+1).'-01-01')-1;
			 $condition['unix_timestamp(`time_refund`)'] = array('between',array($begin_date,$end_date));
		 }
		 return parent::where($condition)->sum('amount_actual');
	 }


	



}