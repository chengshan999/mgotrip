<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

/**
 * 代金券类型模型
 */
class CouponTypeModel extends Model {

	/*
	 * 获取可用代金券类型列表
	 * @access public
	 */
	public function getList($batch) {
		$condition = array(
			"use_start_date" => array('lt',date("Y-m-d", time())),
			"use_end_date"   => array('gt',date("Y-m-d", time())),
			'exchange_batch'   => $batch,
		);
		if (!$data = parent::where($condition)->select())
			return false;
		
		return $data;
	}
	
	



	


}