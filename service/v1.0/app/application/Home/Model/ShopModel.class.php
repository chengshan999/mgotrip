<?php
// +----------------------------------------------------------------------
// | 摩购外出（强生）项目
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 合作商户模型
 */
class ShopModel extends Model {

	/* 字段status */
	const STA_NEW      = 0;  //初始状态
	const STA_ENABLE   = 1;  //可用商户
	const STA_INVALID  = -1; //不可用商户

	/*
	 * 获取商户编码
	 * @access public
	 * @param array $partner_no 商户标识码
	 */
	public function getId($partner_no) {
		$partner_no = trim($partner_no);
		$condition = array(
			"partner_no" => $partner_no,
		);
		return parent::where($condition)->getField("id");
	}

	/*
	 * 获取商户标识码
	 * @access public
	 * @param array $id 商户编码
	 */
	public function getPartnerNo($id) {
		$id = intval($id);
		$condition = array(
			"id" => $id,
		);
		return parent::where($condition)->getField("partner_no");
	}

	/*
	 * 获取商户secret_key
	 * @access public
	 * @param array $partner_no 商户标识码
	 */
	public function getSK($partner_no="", $shop_id=0) {
		$partner_no = trim($partner_no);
		$shop_id    = intval($shop_id);
		if (!empty($partner_no)) {
			$condition = array(
				"partner_no" => $partner_no,
			);
			return parent::where($condition)->getField("secret_key");
		}
		else if ($shop_id>0) {
			$condition = array(
				"id" => $shop_id,
			);
			return parent::where($condition)->getField("secret_key");
		}
		else 
			return false;
	}

	/*
	 * 获取商户信息
	 * @access public
	 * @param array $shop_id 商户编号
	 */
	public function getInfo( $partner_no="",$shop_id=0) {
		$shop_id    = intval($shop_id);
		$partner_no = trim($partner_no);
		if ($shop_id > 0) {
			$condition = array(
				"id" => $shop_id,
				);
			return parent::where($condition)->find();
		}
		else if (!empty($partner_no)) {
			$condition = array(
				"partner_no" => $partner_no,
				);
			return parent::where($condition)->find();
		}
		else 
			return false;
	}

	/*
	 * 获取全部商户相关信息
	 */
	public function getInfoList() {
		$condition['status'] = self::STA_ENABLE;
		return parent::where($condition)->getField('id,name,logo');
	}


}