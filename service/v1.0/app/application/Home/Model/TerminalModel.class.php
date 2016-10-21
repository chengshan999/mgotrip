<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 终端模型
 */
class TerminalModel extends Model {

	/*
	 * 返回终端信息
	 * @access public
	 * @param string $id
	 */
	public function getInfo($terminal_no) {
		$terminal_no = trim($terminal_no);

		if (!empty($terminal_no)) {
			$condition = array(
				"terminal_no"=>$terminal_no
			);
			return parent::where($condition)->find();
		}
		else 
			return false;
	}

	/*
	 * 获取商户编码
	 * @access public
	 * @param array $partner_no 商户标识码
	 */
	public function getId($terminal_no) {
		$terminal_no = trim($terminal_no);
		$condition = array(
				"terminal_no"=>$terminal_no
			);
		return parent::where($condition)->getField("id");
	}

	/*
	 * 获取商户标识码
	 * @access public
	 * @param array $id 商户编码
	 */
	public function getTerminal($id) {
		$id = intval($id);
		$condition = array(
				"id"=>$id
			);
		return parent::where($condition)->getField("terminal_no,shop_id");
	}

}