<?php
// +----------------------------------------------------------------------
// | 摩购出行客户端系统 交易管理
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class TransactionController extends Controller {
	/*
     * 默认函数 置空
     */
	public function index(){}

	/*
	 * 交易总计信息接口
	 */
	public function transSummary() {
		$Curl = new \Think\Curl;

		//获取参数
		$data = $Curl->getData();
		$year  = trim($data->year);
		$month = trim($data->month);


		//根据token签名参数，与用户系统信息交互，获取user_id
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}

		//调用交易模型，获取用户支出总额
		$Transaction = D("Transaction");
		$output_amount = $Transaction->summation($user_id);
		\Think\Log::write(json_encode($output_amount),'output_amount');
		if ($output_amount == false)
			$output_amount = 0;

		//调用退款模型，获取用户收入总额
		$Refund   = D('Refund');
		$input_amount = $Refund->summation($user_id);
		\Think\Log::write(json_encode($input_amount),'input_amount');
		if ($input_amount == false)
			$input_amount = 0;
		
		//处理完成，返回数据
		$ret_data = array(
				"income_amount"  => $input_amount,
				"payment_amount" => $output_amount,
		);
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
	}

	/*
	 * 交易记录查询 移动端调用
	 */
	public function recordQuery() {
		$Curl = new \Think\Curl;

		//获取参数
		$data = $Curl->getData();

		//根据token签名参数，与用户系统信息交互，获取user_id
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}
		$page = !empty($data->page) ? $data->page : 1;
		$page_size = !empty($data->page_size) ? $data->page_size : 20;//设置默认值
		$ret_data   = array();

		//获取综合交易记录：包括支付交易、退款交易
		$Transaction = D("Transaction");
		$records = $Transaction->synthesisTransList($user_id, $page, $page_size);
		// var_dump($records);
		// exit;
		if ($records['data_list']) {
			$temp_ret_data   = array();
			//获取合作商户信息
			$Shop   = D("Shop");
			$shop_list = $Shop->getInfoList();

			foreach($records['data_list'] as $record) {
				if (empty($record['trans_time']))
					continue;
				$ret_year  = substr($record['trans_time'], 0, 4);
				$ret_month = substr($record['trans_time'], 5, 2);
				if($record['trans_type']==1){
					$pay_name = '支付打车费';

				}elseif ($record['trans_type']==3){
					$pay_name = '强生退款';
				}

				if($record['trans_type']==1){
					$type = 1;
				}else{
					$type = 2;
				}
				$temp = array(
						"amount"       => !empty($record['amount']) ? $record['amount'] : 0,
						//"card_no"      => !empty($record['card_no']) ? $record['card_no'] : '',
						"shop_logo"    => $record['shop_id']>0 ? (!empty($shop_list[$record['shop_id']]['logo']) ? C('PIC_URL').$shop_list[$record['shop_id']]['logo'] : '') : '',
						"shop_name"    => $record['shop_id']>0 ? (!empty($shop_list[$record['shop_id']]['name']) ? $shop_list[$record['shop_id']]['name'] : '') : '',
						"trans_name"     => $pay_name,
						"trans_time"   => !empty($record['trans_time']) ? $record['trans_time'] : '',
						"trans_type"   =>  $type,//函数中返回2个值，这里简单做“支出”/“收入”区分；
				);

				$temp_ret_data[$ret_year.'-'.$ret_month]['list'][] = $temp;
				unset($temp);
			}

			if ($temp_ret_data) {
				$Refund   = D('Refund');
				//$RechargeLog = D('RechargeLog');

				foreach($temp_ret_data as $k=>$v) {
					$date = explode('-', $k);
					$expence_total = $Transaction->summation($user_id, $date[0], $date[1]);
\Think\Log::write(json_encode($expence_total),'expence_total');
					$income_total  = $Refund->summation($user_id, $date[0], $date[1]);
\Think\Log::write(json_encode($income_total),'income_total');

					$temp['year']          = $date[0];
					$temp['month']         = $date[1];
					$temp['expence_total'] = $expence_total ? $expence_total : 0;
					$temp['income_total']  = $income_total  ? $income_total  : 0;
					$temp['list']          = $v['list'];
					$ret_data[]            = $temp;
					unset($temp);
					unset($expence_total);
					unset($income_total);
				}
			}
		}
		//构造返回数据
		$this->ajaxReturn($Curl->succArr($ret_data), "JSON", JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
	}












}