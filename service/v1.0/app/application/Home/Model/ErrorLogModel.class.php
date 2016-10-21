<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 错误日志
 */
class ErrorLogModel extends Model {

	/*
	 * 增加错误日志
	 */
	public function log($data='',$ret='',$type='') {
		if (empty($data) && empty($ret))
			return false;
		$data = array(
			"url"  => $_SERVER['REQUEST_URI'],
			"data" => $data,
			"ret"  => $ret,
			"type" => $type,
		);
		return parent::data($data)->add();
	}



}