<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

/**
 * 用户代金券模型
 */
class UserCouponModel extends Model {

	/*
	 * 获取用户代金券列表信息 *分页
	 * @access public
	 */
	public function getList($user_id, $page=1, $page_size=10, $status=0) {
		$return = array();

		$user_id   = intval($user_id);
		$page      = intval($page);
		$page_size = intval($page_size);

		$condition = array(
			"uc.user_id"   => $user_id,
		);

		//接口定义的入参status与数据表status做转换
		if ($status == 1) { //已使用
			$condition['uc.order_id'] = array('gt', 0);
		}
		else if ($status == -1) { //已过期
			$condition['ct.use_end_date'] = array('lt', date("Y-m-d", time()) );
			$condition['uc.order_id'] = array('eq', 0);
		}
		else if ($status == 0) { //未使用 
			$condition['ct.use_end_date'] = array('gt', date("Y-m-d", time()) );
			$condition['uc.order_id'] = array('eq', 0);
		}
		//获取总记录数 及 偏移量
		$total = parent::alias('uc')->join('__COUPON_TYPE__ ct on ct.id=uc.type_id', 'LEFT')->where($condition)->count();
		$total_page = ceil(intval($total)/$page_size);
		if ( $page<=0 ) $page = 1;
		$offset = ($page-1)*$page_size;
		$data_list = parent::alias('uc')->join('__COUPON_TYPE__ ct on ct.id=uc.type_id', 'LEFT')
			->join('__SHOP__ s on s.id=ct.shop_id','LEFT')
			->where($condition)->order('uc.id desc')->limit($offset, $page_size)->getField('uc.id,ct.amount, ct.pic, ct.shop_id, ct.name,s.name shop_name, ct.use_start_date, ct.use_end_date, ct.min_order_amount');
		//返回数据
		$return['total'] = $total;
		$return['data_list'] = $data_list;
		return $return;
	}


	/*
	 * 获取用户代金券列表信息 *不分页
	 * @access public
	 */
	public function getAllList($user_id, $status) {
		$return = array();

		$user_id  = intval($user_id);
		$status   = intval($status);

		$condition = array(
			"uc.user_id"   => $user_id,
		);

		//接口定义的入参status与数据表status做转换
		if ($status == 0) { //全部
		}
		else if ($status == 1) { //最新到账
			$condition['ct.use_start_date'] = array('gt', date("Y-m-d", time()-30*24*3600) );//30天之内
			$condition['uc.order_id'] = array('eq', 0);
		}
		else if ($status == 2) { //即将到期
			$condition['ct.use_end_date'] = array('between',array(date("Y-m-d", time()), date("Y-m-d", time()+30*24*3600)));//30天之内
			$condition['uc.order_id'] = array('eq', 0);
		}
		else if ($status == 3) { //已使用
			$condition['uc.order_id'] = array('gt', 0);
		}
		else if ($status == 4) { //已过期
			$condition['ct.use_end_date'] = array('lt', date("Y-m-d", time()) );
			$condition['uc.order_id'] = array('eq', 0);
		}
		else if ($status == 5) { //未使用 包括“最新到账”和“即将到期”
			$condition['ct.use_end_date'] = array('egt', date("Y-m-d", time()) );
			$condition['uc.order_id'] = array('eq', 0);
		}
		
		//联合查询，获取代金券类型信息
		$data_list = parent::alias('uc')->join('__COUPON_TYPE__ ct on ct.id=uc.type_id', 'LEFT')->where($condition)->order('uc.id desc')->getField('uc.id, uc.code, uc.used_time, uc.order_id, ct.id type_id, ct.amount, ct.shop_id, ct.name, ct.use_start_date, ct.use_end_date, ct.min_order_amount, ct.pic');

		//返回数据
		//$return['total'] = $total;
		//$return['data_list'] = $data_list;
		return $data_list;
	}


	/*
	 * 生成代金券
	 * @access public
	 */
	public function insert($data=array()) {
		if (empty($data) || !is_array($data))
			return false;
		
		//生成代金券码
		if (empty($data['code']))
			$data['code'] = "******";
				
		return parent::data($data)->add();
	}

	/*
	 * 恢复代金券
	 * @access public
	 */
	public function renew($user_id, $id) {
		if (intval($user_id)<0 || intval($id)<=0)
			return false;

		$condition = array(
			"id"      => intval($id),
		);
		if (intval($user_id)>0) {
			$condition["user_id"] = intval($user_id);
		}

		$update = array(
			"order_id" => 0,
		);
		
		return parent::where($condition)->setField($update);
	}

	/*
	 * 核实某批次是否已发放
	 * @access public
	 */
	public function exist($user_id, $type_id) {
		if (intval($user_id)<0 || intval($type_id)<=0)
			return false;

		$condition = array(
			"user_id"      => intval($user_id),
			"type_id"      => intval($type_id),
		);
		
		return parent::where($condition)->find();
	}


	/*
	 * 获取当前代金券信息
	 * @access public
	 */
	public function getInfo($id) {
		if (intval($id) <= 0)
			return false;
		if(strpos(',',$id)){
			$arr=explode($id);
			foreach($arr as $v){
				$newarr[]=intval($v);
			}
			$condition['uc.id']=array('in',$newarr);

		}else {

			$condition = array(
				"uc.id" => intval($id),
			);

			//return parent::where($condition)->find();

		}
		return parent::alias('uc')->join('__COUPON_TYPE__ ct on ct.id=uc.type_id', 'LEFT')->field('uc.*,ct.name,ct.amount,ct.min_order_amount,ct.use_end_date')->where($condition)->find();

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









	


}