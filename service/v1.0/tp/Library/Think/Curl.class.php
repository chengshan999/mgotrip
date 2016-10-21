<?php
// +----------------------------------------------------------------------
// | 预付365项目 补充库文件 wjn 2015-11-18
// +----------------------------------------------------------------------
namespace Think;
class Curl {
	
	/*
	 * 通过curl方式post数据到接口并接收返回
	 * @access public
	 * @param string $url
	 * @param string $data
	 */
	public function getReturn($url, $data)
	{
		$post_data = json_encode($data, JSONUNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
		return $this->postData($url, $post_data);
	}

	/*
	 * curl实现
	 * @access public
	 * @param string $url
	 * @param string $data
	 */
	public function postData($url, $post_data)
	{
		if (is_array($post_data) || is_object($post_data)) {
			$post_data = http_build_query($post_data);
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$ret = curl_exec($ch);
		if ($ret === false) {
			\Think\Log::write(curl_error($ch), 'service\v1.0\tp\Library\Think\Curl.class.php CURL ERROR!!');
		}
		curl_close($ch);

		$return = json_decode($ret);
		if (json_last_error()>0) 
		{
			\Think\Log::write($ret,'service\v1.0\tp\Library\Think\Curl.class.php JSON_DEOCDE ERROR!!');
		}
		return $return;
	}

	/*
	 * 创建签名
	 * @access public
	 * @param array $data 签名数据数组
	 */
	public function createSign($data) {
		if(!is_array($data) || empty($data)) {
			return false;
		}
		$str = "";
		foreach($data as $k=>$v)
		{
			$str .= $v;
		}
		return md5($str);
	}

	/*
	 * 接收入参数据 本项目入参，不是POST或GET格式，这里简单处理下；
	 * @access public
	 */
	public function getData() {
		//原来的方法
		$data = json_decode(file_get_contents('php://input'));
		
		//使用TP的I函数的方法
		//$data = array_keys(I('put.'));
		//$data = json_decode($data[0]);
		return $data;
	}

	/*
	 * 接口返回错误信息
	 * @access public
	 * @param string $code 错误标识码
	 */
	public function failArr($code) {
		$return = array();
		$return['status'] = "fail";
		$return['msg']    = trim($code);
		return $return;
	}

	/*
	 * 接口返回成功信息
	 * @access public
	 * @param array $ret_data 数组
	 */
	public function succArr($ret_data=array()) {
		$return = array(
			"status" => "succ",
			"msg"    => "",
			"data"   => $ret_data,
		);
		return $return;
	}
	
	/*
	 * RSA解密
	* @access public
	* @param string $encryptData
	*/
	public function encrypt($originalData="") {
		if (!empty($originalData)) {
			$path = str_replace('Curl.class.php', '', str_replace('\\', '/', __FILE__));
			$publicKeyFilePath = $path.'pub.pem';
			
			if (!file_exists($publicKeyFilePath)) {
				return $this->failArr('密钥的文件路径不正确');
			}
			if (!extension_loaded('openssl')) {
				return $this->failArr('php需要openssl扩展支持');
			}
			$publicKey	= openssl_pkey_get_public(file_get_contents($publicKeyFilePath));
			if (!$publicKey) {
				return $this->failArr('密钥不可用');
			}
			//$encryptData = base64_decode($encryptData);
			$encryptData = '';
			if (openssl_public_encrypt($originalData, $encryptData, $publicKey)) {
				 
				/**
				 * 加密后 可以base64_encode后方便在网址中传输 或者打印  否则打印为乱码
				 */
				//echo '加密成功，加密后数据(base64_encode后)为:', base64_encode($encryptData), PHP_EOL;
				return base64_encode($encryptData);
			} else {
				return $this->failArr('加密失败');
			}
		}
	}
	
}
