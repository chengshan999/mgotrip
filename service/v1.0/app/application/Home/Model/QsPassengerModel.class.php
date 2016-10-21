<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 强生约租车乘客模型
 */
class QsPassengerModel extends Model {

	/*
	 * 获取乘客信息
	 * @access public
	 * @param string $id
	 */
	public function getInfo($id=0) {
		$id = intval($id);

		if ($id>0) {
			$condition = array(
				"id" => $id,
			);
			return parent::where($condition)->find();
		}
		else 
			return false;
	}

	/*
	 * 增加乘客信息
	 * @access public
	 */
	public function add_ps($data=array()) {
		if (empty($data) || !is_array($data))
			return false;
		$condition = array(
			"user_id" => $data['user_id'],
		);
		$field = array(
			"is_default" => 0,
		);
		parent::where($condition)->setField($field);
		//新增加的乘客信息默认为1
		return parent::data($data)->add();
	}

	/*
	 * 核实乘客是否存在，存在返回符合条件的乘客信息
	* @access public
	*/
	public function checkPasenger($user_id,$name,$mobile,$gender) {
		$user_id 	= intval($user_id);
		$name		= trim($name);
		$mobile 	= trim($mobile);
		$gender 	= intval($gender);
		if (empty($user_id) || empty($name) || empty($mobile) || empty($gender)){
			return false;
		}else{ 
			$condition = array(
				"user_id" 	=> $user_id,
				"name"		=> $name,
				"mobile" 	=> $mobile,
				"gender" 	=> $gender,
			);
			return parent::where($condition)->find();
		}
		
	}
	
	
	/*
	 * 返回乘客信息列表
	 * @access public
	 */
	public function getPasengerList($user_id) {
		$user_id = intval($user_id);
		if (!empty($user_id)) {
			$condition = array(
				"user_id" => $user_id,
			);
			return parent::where($condition)->order(array('is_default'=>'desc','id'=>'desc'))->limit(5)->select();
		}
		else
			return false;
	}



}