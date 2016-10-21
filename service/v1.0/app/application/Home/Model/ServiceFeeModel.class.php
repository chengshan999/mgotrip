<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 服务费模型
 */
class ServiceFeeModel extends Model {


	/*
	 * 返回服务费信息
	* @access public
	*/

	public function getList($shop_id=0) {
		$shop_id = trim($shop_id);
		if (!empty($shop_id)) {
			$condition = array(
					"shop_id" => $shop_id,
			);
			return parent::where($condition)->select();
		}
		else
			return parent::select();

		
	}



}