<?php 
// +----------------------------------------------------------------------
// | 摩购出行客户端系统 乘客信息管理
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class PassengerController extends Controller {
    /*
	 * 默认函数 置空
	 */
	public function index(){}

	public function passengerList(){
		$Curl = new \Think\Curl;

		//获取参数
		$data = $Curl->getData();
		\Think\Log::write(json_encode($data),'input data');
		//根据token签名参数，与用户系统信息交互，获取user_id
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		\Think\Log::write(C('USER_GET_ID'),'user info url');
		\Think\Log::write(json_encode($user_info),'user info res');
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
		}
		
		//调用乘客模型，获取乘客信息列表
		$QsPassenger = D('QsPassenger');
		$passenger_list = $QsPassenger->getPasengerList($user_id);
		if ($passenger_list === false) {
			$this->ajaxReturn($Curl->failArr("E712"), "JSON");
		}
		$ret_data = array();
		if ($passenger_list) {
			foreach($passenger_list as $k=>$v) {
				$temp = array(
					"id"          => $v['id'],
					"name"        => $v['name'],
					"gender"      => $v['gender'],
					"mobile"      => $v['mobile'],
				);
				$ret_data[] = $temp;
				unset($temp);
			}
		}
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON", JSON_UNESCAPED_UNICODE);
    }
}

