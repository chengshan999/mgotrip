<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

/**
 * 兑换码模型
 */
class ExchangeCouponModel extends Model {

	/* 字段status */
	const S_CANCELED = -1; //已作废
	const S_NEW      = 0; //初始状态

	/*
	 * 获取兑换码信息
	 * @access public
	 */
	public function getInfo($code='') {
		$code = trim($code);
		if (empty($code)) {
			return false;
		}

		$condition = array("code"=>$code);
		if (!$data = parent::where($condition)->find())
			return false;
		
		return $data;
	}
	
	/*
	 * 标记兑换码已用
	 * @access public
	 */
	public function used($id=0, $user_id) {
		$id = intval($id);
		$user_id = intval($user_id);
		if ($id<=0 || $user_id<=0) {
			return false;
		}

		$condition = array("id"=>$id);
		$update = array(
			'user_id' => $user_id,
			'use_date'    => date("Y-m-d H:i:s"),
		);
		return parent::where($condition)->setField($update);
	}

	/*
	 * 核实某用户某批次是否已体验
	 * @access public
	 */
	public function checkUser($user_id, $batch) {
		$user_id = intval($user_id);
		$batch   = trim($batch);

		$condition = array(
			'user_id' => $user_id,
			'batch'   => $batch,
		);
		return parent::where($condition)->find();
	}



	


}