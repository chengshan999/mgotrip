<?php 
// +----------------------------------------------------------------------
// | 摩购出行客户端系统 商户信息管理
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class ShopController extends Controller {
    /*
	 * 默认函数 置空
	 */
	public function index(){}

	public function getInfo(){
		$Curl = new \Think\Curl;

		//获取参数
		$data = $Curl->getData();
		$partner_no  = trim($data->partner_no);
		$terminal_no = trim($data->terminal_no);

		//调用商户模型，获取商户数据
		$Shop = D("Shop");
		$shop = $Shop->getInfo($partner_no);
		if ($shop === false) {
			$this->ajaxReturn($Curl->failArr("E993"), "JSON");
		}
		
		//调用终端模型，获取终端信息
		$Terminal = D("Terminal");
		$terminal = $Terminal->getInfo($terminal_no);
		if ($terminal == false) {
			$this->ajaxReturn($Curl->failArr("E992"), "JSON");
		}

		if ($shop['id']!=$terminal['shop_id']) {
			$this->ajaxReturn($Curl->failArr("E990"), "JSON");
		}
		//构造返回数据
		$ret_data = array(
			"id"            => $shop['id'],
			"partner_name"  => $shop['name'].$terminal['name'],
		);
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON", JSON_UNESCAPED_UNICODE);
    }

	/*
     * 获取服务费信息
    */
    public function qsService(){
    	$Curl 	= new \Think\Curl;
		//获取参数
		$data = $Curl->getData();
    	$ServiceFee 	= D("ServiceFee");
    	$service_info = $ServiceFee->getList();
    	if ($service_info === false) {
    		$this->ajaxReturn($Curl->failArr("E980"), "JSON");
    	}
    	//构造返回数据
    	$ret_data =array();
    	if($service_info){
    		foreach($service_info as $k=>$v) {
		    	$temp = array(
		    			"amount"	=> $v['amount'],
		    			"state"		=> $v['state'],
		    	);
	    		$ret_data[] = $temp;
	    		unset($temp);
    		}
    	}
    	$this->ajaxReturn($Curl->succArr($ret_data), "JSON", JSON_UNESCAPED_UNICODE);
    }
}