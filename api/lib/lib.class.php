<?php
// +----------------------------------------------------------------------
// | 预付365项目 前端封装 wjn 2015-12-04
// +----------------------------------------------------------------------

//namespace Lib;

class Lib {
	
	/*
	 * RSA解密
	 * @access public
	 * @param string $encryptData
	 */
	public function decrypt($encryptData="") {
		if (!empty($encryptData)) {
			$privateKeyFilePath = COMMON_PATH."prv.pem";
			if (!file_exists($privateKeyFilePath)) {
				$this->log('密钥的文件路径不正确');
				return false;
			}
			if (!extension_loaded('openssl')) { 
				$this->log('php需要openssl扩展支持');
				return false;
			}
			$privateKey = openssl_pkey_get_private(file_get_contents($privateKeyFilePath));
			if (!$privateKey) {
				$this->log('密钥不可用');
				return false;
			}
			$encryptData = base64_decode($encryptData);
			$decryptData ='';
			if (openssl_private_decrypt($encryptData, $decryptData, $privateKey)) {
				$this->log('RSA decryped is '.$decryptData);
				return $decryptData;
			}
			else {
				$this->log('解密失败');
				return false;
			}
		}
	}

	/*
	 * DES解密
	 * @access public
	 * @param string $sStr
	 */
	public function ddecrypt($sStr) {
		$sKey = 'yufu365secretkey';
        $decrypted= mcrypt_decrypt(  
			MCRYPT_RIJNDAEL_128,  
			$sKey,  
			base64_decode($sStr),  
			MCRYPT_MODE_ECB  
		);
		if (!$decrypted) {
			$dec_s = strlen($decrypted);  
			$padding = ord($decrypted[$dec_s-1]);  
			$decrypted = substr($decrypted, 0, -$padding); 
		}
        return $decrypted;  
    }

	/*
	 * 记录日志
	 * @access public
	 * @param string $data
	 */
	public function log($data="") {
		if (!empty($data)) {
			$destination = LOG_PATH.date("Y-m-d").".txt";
			$log_dir = dirname($destination);
			if (!is_dir($log_dir)) {
				mkdir($log_dir, 0755, true);
			}
			$fh = fopen($destination,"a");
			fwrite($fh, "[".date("Y-m-d H:i:s")."--".$this->ip()."]".$data."\r\n");
			fclose($fh);
		}
	}

	/*
	 * 获取ip
	 * @access public
	 * @param string $data
	 */
	public function ip(){
		if (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) {
			$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
		}
		elseif (isset($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])) {
			$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
		}
		elseif (isset($HTTP_SERVER_VARS["REMOTE_ADDR"])) {
			$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
		}
		elseif (getenv("HTTP_X_FORWARDED_FOR")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		}
		elseif (getenv("HTTP_CLIENT_IP")) {
			$ip = getenv("HTTP_CLIENT_IP");
		}
		elseif (getenv("REMOTE_ADDR")) {
			$ip = getenv("REMOTE_ADDR");
		}
		else {
			$ip = "Unknown";
		}
		return $ip;
	}

	/*
	 * curl实现
	 * @access public
	 * @param string $url
	 * @param string $data
	 */
	public function postData($url, $post_data) {
		if (is_array($post_data) || is_object($post_data)) {
			$post_data = http_build_query($post_data);
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$ret = curl_exec($ch);
		curl_close($ch);

		return $ret;
	}

	/*
	 * 根据yufu365项目接口规范返回错误警报
	 * @access public
	 * @param string $msg
	 */
	public function retError($msg) {
		$msg = trim($msg);
		$this->log($msg);
		$data = array("status"=>"fail", "msg"=>$msg);
		return json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
	}

	/*
	 * 根据yufu365项目接口规范返回成功
	 * @access public
	 * @param string $msg
	 */
	public function retSucc() {
		$data = array("status"=>"succ", "msg"=>"");
		return json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
	}

	/*
	 * 上传图片操作
	 * @access public
	 * @param string $msg
	 */
	public function upload($file, $url='', $data=array()) {
		mt_srand((double) microtime() * 1000000);
		/* 用户上传头像 */
		if ( preg_match('/user\/avatar\/set/', $url) ) { 
			do {
				$file_name = date("YmdHis").str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT).substr(basename($file['name']), strripos($file['name'],'.'));
				$target_path = ICON_SAVE_PATH.$file_name;
			} while (file_exists($target_path));
			
			if(move_uploaded_file($file['tmp_name'], $target_path)) { 
			   return ICON_VISIT_PATH.$file_name;
			}
			else { 
			   return false;
			}
		}
		/* 上传退货图片 */
		else if ( preg_match('/mall\/return\/upload_pic/', $url) ) { 
			$package_id = intval($data['package_id']);
			$goods_id   = intval($data['goods_id']);
			if ($package_id<=0 || $goods_id<=0) {
				return false;
			}
			do {
				$file_name = $package_id.'-'.$goods_id.'-'.str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT).substr(basename($file['name']), strripos($file['name'],'.'));
				$target_path = RETURN_SAVE_PATH.$file_name;
			} while (file_exists($target_path));

			if(move_uploaded_file($file['tmp_name'], $target_path)) { 
			   return RETURN_VISIT_PATH.$file_name;
			}
			else { 
			   return false;
			}
		}
	}



}
