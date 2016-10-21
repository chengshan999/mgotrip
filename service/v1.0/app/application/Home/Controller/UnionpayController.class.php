<?php 
// +----------------------------------------------------------------------
// | 摩购出行客户端系统 银联支付管理
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

/*
 *  银联开发包文件所在目录 ********
 */
include_once C('UNIONPAY_FILE_PATH').'common.php';

class UnionpayController extends Controller {
    /*
	 * 默认函数 置空
	 */
	public function index(){}

	/*
	 * 银联支付，提交订单获取交易流水号
	 */
	public function appConsume(){
		$Curl = new \Think\Curl;
		//获取参数
		$data = $Curl->getData();

		//核实订单信息
		$Order = D('Order');
		if (!$order_info = $Order->getInfo(0, $data->order_no)) {
			$this->ajaxReturn($Curl->failArr("E201"), "JSON");
		}

		//获取secret_key
		$Shop = D('Shop');
		if (!$shop_info = $Shop->getInfo($order_info['shop_id'])) {
			$this->ajaxReturn($Curl->failArr("E993"), "JSON");
		}

		//根据token签名参数，与用户系统信息交互，验证用户信息
		$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
		if ($user_info==false || $user_info->status=="fail") {
			$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
		}
		else {
			$user_id   = $user_info->data->user_id;
			$device_no = $user_info->data->device_no;
		}

		//验证签名 	md5(order_no+amount+token+device_no+secret_key)
		$sign_arr = array(
			"order_no"    => $data->order_no,
			"amount"      => $data->amount,
			"token"       => $data->token,
			"device_no"   => $device_no,
			"secret_key"  => $shop_info['secret_key'],
		);
		$sign = $Curl->createSign($sign_arr);
\Think\Log::write("POST SIGN IS ".$data->sign." CREATE SIGN IS ".$sign,'YUFU365');
		if ($data->sign != $sign) {
			$this->ajaxReturn($Curl->failArr("E994"), "JSON");
		}

		//增加交易记录
		$Transaction = D('Transaction');
		$insert = array(
			"order_id"    => $order_info['id'],
			"serial_no"   => !empty($data->serial_no) ? $data->serial_no : $order_info['order_no'].'unionpay',
			"order_no"    => $order_info['order_no'],
			"corpor_id"   => 0,//补充支付方式corpor_id都是0
			"user_id"     => $user_id,
			"amount"      => $data->amount,
		);
		$transaction_id = $Transaction->insert($insert);

		$params = array(
			//以下信息非特殊情况不需要改动
			'version'      => '5.0.0',               //版本号
			'encoding'     => 'utf-8',				 //编码方式
			'certId'       => getSignCertId (),	     //证书ID
			'txnType'      => '01',				     //交易类型
			'txnSubType'   => '01',				     //交易子类
			'bizType'      => '000201',				 //业务类型
			'frontUrl'     => SDK_FRONT_NOTIFY_URL,  //前台通知地址
			'backUrl'      => SDK_BACK_NOTIFY_URL,	 //后台通知地址
			'signMethod'   => '01',	                 //签名方法
			'channelType'  => '08',	                 //渠道类型，07-PC，08-手机
			'accessType'   => '0',		             //接入类型
			'currencyCode' => '156',	             //交易币种，境内商户固定156
			
			//TODO 以下信息需要填写
			'merId'        => SDK_MERCHANT_ID,       //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
			'orderId'      => $data->order_no,       //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
			'txnTime'      => date('YmdHis'),        //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
			'txnAmt'       => $data->amount,         //交易金额，单位分，此处默认取demo演示页面传递的参数
			'reqReserved'  => $transaction_id,       //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据
			                                         //reqReserved 中保存交易id号，以便定位交易数据 -----wjn----2016/1/15-----

			//TODO 其他特殊用法请查看 pages/api_05_app/special_use_purchase.php
		);

		//更新交易信息
		$update = array(
			"send_msg" => json_encode($params),
			);
		$Transaction->update($transaction_id, $update);

		sign ( $params ); // 签名
		$url = SDK_App_Request_Url;
		\Think\Log::write("后台请求地址为>" . $url,'UNIONPAY');
		$result = post ( $params, $url, $errMsg );
		if (! $result) { //没收到200应答的情况
			//printResult ( $url, $params, "" );
			//echo "POST请求失败：" . $errMsg;
			//return;
			$this->ajaxReturn($Curl->failArr("E985"), "JSON");
		}
		\Think\Log::write("后台返回结果为>" . $result,'UNIONPAY');
		$result_arr = convertStringToArray ( $result );

		//printResult ( $url, $params, $result ); //页面打印请求应答数据

		if ( !verify ( $result_arr ) ){
			//echo "应答报文验签失败<br>\n";
			//return;
			$this->ajaxReturn($Curl->failArr("E984"), "JSON");
		}

		//echo "应答报文验签成功<br>\n";
		if ($result_arr["respCode"] == "00"){
			//成功
			//TODO
			//echo "成功接收tn：" . $result_arr["tn"] . "<br>\n";
			//echo "后续请将此tn传给手机开发，由他们用此tn调起控件后完成支付。<br>\n";
			//echo "手机端demo默认从仿真获取tn，仿真只返回一个tn，如不想修改手机和后台间的通讯方式，【此页面请修改代码为只输出tn】。<br>\n";
			\Think\Log::write("银联支付返回参数：" . json_encode($result_arr),'UNIONPAY');

			//更新交易信息
			$update = array(
				"card_no"     => $result_arr["tn"],//正式环境这个是唯一的，保存在这里便于查询
				);
			$Transaction->update($transaction_id, $update);

			$ret_data = array(
				"serial_no"     => $result_arr["tn"],
			);
			$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
		} else {
			//其他应答码做以失败处理
			 //TODO
			 //echo "失败：" . $result_arr["respMsg"] . "。<br>\n";
			 \Think\Log::write("银联支付获取tn失败：" . $result_arr["respMsg"],'UNIONPAY');
			 $this->ajaxReturn($Curl->failArr("E983"), "JSON");
		}
	}

	/*
	 * 银联支付，后台通知
	 */
	public function backReceive() {
		$Curl = new \Think\Curl;
		$Error = D('ErrorLog');

        $Curl = new \Think\Curl;
        //获取参数
        $data = $Curl->getData();
        $data = objectToArray($data->data);
		//获取参数
		$Error->log(json_encode($data), '', 'INPUT_DATA');

		//核实订单信息
		$Order = D('Order');
		if (!$order_info = $Order->getInfo(0, $data['orderId'])) {
			$Error->log('', 'order does not exist!', 'UNIONPAY_BACK_NOTICE');
		}

		if ($order_info['pay_status']==$Order::PS_PAID || $order_info['pay_status']==$Order::PS_CANCELED) {
			$Error->log('', 'order is paid or cancel! exit!', 'UNIONPAY_BACK_NOTICE');
			exit;
		}

		if ($data['signature']) {
			//可验证签名
			if (verify ($data)) {
				//验签成功 继续后续处理
				
				//交易模型  //reqReserved 中保存交易id号
				$Transaction = D('Transaction');
				if (!$trans_info = $Transaction->getInfo($data['reqReserved'])) {
					$Error->log('', 'transaction does not exist!', 'UNIONPAY_BACK_NOTICE');
				}

				//保存通知返回到交易信息
				$update = array(
					"response_msg"    => json_encode($data),
				);

				//判断respCode=00或A6即可认为交易成功
				if ($data['respCode']=='00' || $data['respCode']=='A6') {
					//更新交易
					$Transaction->paySuccess($data['reqReserved'], $update);
					//更新订单
					$Order->pay($order_info['id'],$data['txnAmt'],"unionpay");
					//获取最新订单信息
					$order_info = $Order->getInfo($order_info['id']);
					//跨控制器调用，发送支付回执
					R('Order/receipt',array($order_info));
				}
				else {
					//更新交易
					$Transaction->payFail($data['reqReserved'], $update);
				}

			}
			else {
				//验签失败
				$Error->log('', 'check signature is fail!', 'UNIONPAY_BACK_NOTICE');
			}
		}
		else {
			//签名为空
			$Error->log('', 'signature is empty!', 'UNIONPAY_BACK_NOTICE');
		}
		
		echo 1;//测试用
		
	}

	/*
	 * 银联支付交易结果查询
	 * @input string tn 交易流水号
	 * @input string order_no 支付系统订单号
	 */
	public function query(){
		$Curl = new \Think\Curl;
		$Error = D('ErrorLog');

		//获取参数
		$data = $Curl->getData();

		//根据token签名参数，与用户系统信息交互，获取user_id
		if (!empty($data->device) && !empty($data->token)) {
			$user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
			if ($user_info==false || $user_info->status=="fail") {
				$this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
			}
			else {
				$user_id   = $user_info->data->user_id;
				$device_no = $user_info->data->device_no;
			}
		}
		else {
			$this->ajaxReturn($Curl->failArr("E203"), "JSON");
		}

		$tn = trim($data->tn);
		$order_no = trim($data->order_no);

		//根据订单号和tn查询当前交易信息
		$Order = D("Order");
		$Transaction = D("Transaction");
		if ( !$trans_info  = $Transaction->getInfoByField(array("order_no"=>$order_no, "card_no"=>$tn)) ) {
			$this->ajaxReturn($Curl->failArr("E201"), "JSON");
		}

		//首先查询交易状态
		if ($trans_info['status']==$Transaction::STA_SUCCESS || $trans_info['status']==$Transaction::STA_FAILED) {
			$ret_data = array(
				"status" => $trans_info['status'],
			);
			$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
		}
		//到银联后台进行查询
		else if ($trans_info['status']==$Transaction::STA_NEW) {
			$send_msg = json_decode($trans_info['send_msg']);
			$params = array(				
				//以下信息非特殊情况不需要改动
				'version'     => '5.0.0',		  //版本号
				'encoding'    => 'utf-8',		  //编码方式
				'certId'      => getSignCertId (), //证书ID	
				'signMethod'  => '01',		  //签名方法
				'txnType'     => '00',		  //交易类型	
				'txnSubType'  => '00',		  //交易子类
				'bizType'     => '000000',	  //业务类型
				'accessType'  => '0',		  //接入类型
				'channelType' => '07',		  //渠道类型

				//TODO 以下信息需要填写
				'orderId'     => $data->order_no,	//请修改被查询的交易的订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数
				'merId'       => SDK_MERCHANT_ID,	//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
				'txnTime'     => $send_msg->txnTime,//请修改被查询的交易的订单发送时间，格式为YYYYMMDDhhmmss，此处默认取demo演示页面传递的参数
			);
\Think\Log::write("银联查询输入参数：".json_encode($params),'UNIONPAY');

			//签名
			sign ( $params );
			$url = SDK_SINGLE_QUERY_URL;
			$result = post ( $params, $url, $errMsg );
			$result_arr = convertStringToArray ( $result );

			//保存通知返回到交易信息
			$update = array(
				"response_msg"    => $result,
			);

			if ( !verify ( $result_arr ) ){
				//银联查询应答报文验签失败
				$Error->log('', 'check signature is fail!', 'UNIONPAY_QUERY');
				$this->ajaxReturn($Curl->failArr("E984"), "JSON");
			}
			//银联查询应答报文验签成功

			if ($result_arr["respCode"] == "00"){
			  if ($result_arr["origRespCode"] == "00"){
					//交易成功

					//更新交易
					$Transaction->paySuccess($trans_info['id'], $update);
					//更新订单
					$Order->pay($trans_info['order_id'],$send_msg->txnAmt,"unionpay");
					
					//获取最新订单信息
					$order_info = $Order->getInfo($trans_info['order_id']);
					
					//若有账户余额冻结款，扣除冻结款
					if ( $order_info['pay_status']==$Order::PS_PAID ) {
						//核实是否有账户余额冻结款，这里扣除冻结款（账户余额部分支付的，都会先冻结，这里再扣款）
						if ($order_info['account_paid']>0 && $order_info['account_paid']<$order_info['amount']) {
							$post_data = array(
								"order_id" => $order_info['id'],
								"order_no" => $order_info['order_no'],
								"version"  => "1.0",
							);
							$frozen_charged = $Curl->getReturn(C('USER_FROZEN_CHARGED'), $post_data);
							if ($frozen_charged==false || $frozen_charged->status=="fail") {
								$Error->log(json_encode($post_data), json_encode($frozen_charged), 'USER_FROZEN_CHARGED');
							}
						}
					}

					//跨控制器调用，发送支付回执
					R('Order/receipt',array($order_info));

					//返回数据
					$ret_data = array(
						"status" => $Transaction::STA_SUCCESS,
					);
					$this->ajaxReturn($Curl->succArr($ret_data), "JSON");

				} else if ($result_arr["origRespCode"] == "03"
						|| $result_arr["origRespCode"] == "04"
						|| $result_arr["origRespCode"] == "05" ){
					//后续需发起交易状态查询交易确定交易状态

					//返回数据
					$ret_data = array(
						"status" => $Transaction::STA_NEW,
					);
					$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
				} else {
					//其他应答码做以失败处理

					//更新交易
					$Transaction->payFail($trans_info['id'], $update);

					//获取最新订单信息
					$order_info = $Order->getInfo($trans_info['order_id']);
					//跨控制器调用，发起退款，订单回归初始状态，便于再次支付
					R('Order/refund',array($order_info, $data->device));

					//返回数据
					$ret_data = array(
						"status" => $Transaction::STA_FAILED,
					);
					$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
				}
			} else if ($result_arr["respCode"] == "03"
					|| $result_arr["respCode"] == "04"
					|| $result_arr["respCode"] == "05" ){
				//后续需发起交易状态查询交易确定交易状态
				
				//返回数据
				$ret_data = array(
					"status" => $Transaction::STA_NEW,
				);
				$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
			} else {
				//其他应答码做以失败处理

				//更新交易
				$Transaction->payFail($trans_info['id'], $update);

				//获取最新订单信息
				$order_info = $Order->getInfo($trans_info['order_id']);
				//跨控制器调用，发起退款，订单回归初始状态，便于再次支付
				R('Order/refund',array($order_info, $data->device));

				//返回数据
				$ret_data = array(
					"status" => $Transaction::STA_FAILED,
				);
				$this->ajaxReturn($Curl->succArr($ret_data), "JSON");
			}
		}
	}


    //PC端支付
    public function PCConsume(){
        $Curl = new \Think\Curl;
        //获取参数
        $data = $Curl->getData();

        //核实订单信息
        $Order = D('Order');
        if (!$order_info = $Order->getInfo(0, $data->order_no)) {
            $this->ajaxReturn($Curl->failArr("E201"), "JSON");
        }

        //获取secret_key
        $Shop = D('Shop');
        if (!$shop_info = $Shop->getInfo($order_info['shop_id'])) {
            $this->ajaxReturn($Curl->failArr("E993"), "JSON");
        }

        //根据token签名参数，与用户系统信息交互，验证用户信息
        $user_info = $Curl->getReturn(C('USER_GET_ID'), array("device"=>$data->device,"token"=>$data->token));
        if ($user_info==false || $user_info->status=="fail") {
            $this->ajaxReturn($Curl->failArr( !empty($user_info->msg) ? $user_info->msg : "E203" ), "JSON");
        }
        else {
            $user_id   = $user_info->data->user_id;
            $device_no = $user_info->data->device_no;
        }

        //验证签名 	md5(order_no+amount+token+device_no+secret_key)
        $sign_arr = array(
            "order_no"    => $data->order_no,
            "amount"      => $data->amount,
            "token"       => $data->token,
            "device_no"   => $device_no,
            "secret_key"  => $shop_info['secret_key'],
        );
        $sign = $Curl->createSign($sign_arr);
        \Think\Log::write("POST SIGN IS ".$data->sign." CREATE SIGN IS ".$sign,'YUFU365');
        if ($data->sign != $sign) {
            $this->ajaxReturn($Curl->failArr("E994"), "JSON");
        }

        //增加交易记录
        $Transaction = D('Transaction');
        $insert = array(
            "order_id"    => $order_info['id'],
            "serial_no"   => !empty($data->serial_no) ? $data->serial_no : $order_info['order_no'].'unionpay',
            "order_no"    => $order_info['order_no'],
            "corpor_id"   => 0,//补充支付方式corpor_id都是0
            "user_id"     => $user_id,
            "amount"      => $data->amount,
            "send_msg"    => $data->send_msg  ,
        );
        $transaction_id = $Transaction->insert($insert);
        $ret_data = array(
            "transaction_id" => $transaction_id,
        );
        $this->ajaxReturn($Curl->succArr($ret_data), "JSON");
//        $url = SDK_FRONT_TRANS_URL;
//        \Think\Log::write("后台请求地址为>" . $url,'UNIONPAY');
//        $html_form = createAutoFormHtml( $params, $url );
//        echo $html_form;
    }



    //PC端限联回职
    public function PCbackReceive() {
        $Curl = new \Think\Curl;
        $Error = D('ErrorLog');
        $data = $Curl->getData();

        $data = objectToArray($data->data);

        $Error->log(json_encode($data), '', 'INPUT_DATA');

        //核实订单信息
        $Order = D('Order');

        if (!$order_info = $Order->getInfo(0, $data['orderId'])) {
            $Error->log('', 'order does not exist!', 'UNIONPAY_BACK_NOTICE');
        }

        if ($order_info['pay_status']==$Order::PS_PAID || $order_info['pay_status']==$Order::PS_CANCELED) {
            $Error->log('', 'order is paid or cancel! exit!', 'UNIONPAY_BACK_NOTICE');
            exit;
        }

        if ($data['signature']) {
            //可验证签名
            if (verify ($data)) {
                //验签成功 继续后续处理

                //交易模型  //reqReserved 中保存交易id号
                $Transaction = D('Transaction');


                $reqReserved_array = explode(",",$data['reqReserved']);
               // if (!$trans_info = $Transaction->getInfo($data['reqReserved'])) {
                if (!$trans_info = $Transaction->getInfo($reqReserved_array[0])) {
                    $Error->log('', 'transaction does not exist!', 'UNIONPAY_BACK_NOTICE');
                }

                //保存通知返回到交易信息
                $update = array(
                    "response_msg"    => json_encode($data),
                );

                //判断respCode=00或A6即可认为交易成功
                if ($data['respCode']=='00' || $data['respCode']=='A6') {
                    //更新交易
                    $Transaction->paySuccess($reqReserved_array[0], $update);
                    //更新订单
                    $Order->pay($order_info['id'],$data['txnAmt'],"unionpay");
                    //获取最新订单信息
                    $order_info = $Order->getInfo($order_info['id']);
                    //跨控制器调用，发送支付回执
                    R('Order/receipt',array($order_info));
                }
                else {
                    //更新交易
                    $Transaction->payFail($reqReserved_array[0], $update);
                }

            }
            else {
                //验签失败
                $Error->log('', 'check signature is fail!', 'UNIONPAY_BACK_NOTICE');
            }
        }
        else {
            //签名为空
            $Error->log('', 'signature is empty!', 'UNIONPAY_BACK_NOTICE');
        }

        echo 1;//测试用

    }



}