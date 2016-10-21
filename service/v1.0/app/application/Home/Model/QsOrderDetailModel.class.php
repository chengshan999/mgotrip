<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 强生订单模型
 */
class QsOrderDetailModel extends Model {

	/*
	 * 返回订单详情信息
	 * @access public
	 * @param string $id
	 */
	public function getInfo($id=0, $order_id='') {
		$id = intval($id);
		$order_id = intval($order_id);

		if ($id>0) {
			$condition = array(
				"id" => $id,
			);
			return parent::where($condition)->find();
		}
		else if ($order_id) {
			$condition = array(
				"order_id" => $order_id,
			);
			return parent::where($condition)->find();
		}
		else 
			return false;
	}

	/*
	 * 生成订单详情
	 * @access public
	 */
	public function insert($data=array()) {
		if (empty($data) || !is_array($data))
			return false;
				
		return parent::add($data);
	}

	/*
	 * 更新详情
	 * @access public
	 */
	public function update($order_id, $data=array()) {
		if (empty($data) || !is_array($data))
			return false;

		$order_id = intval($order_id);
		$condition = array(
			"order_id" => $order_id,
		);
				
		return parent::where($condition)->save($data);
	}


}