<?php
// +----------------------------------------------------------------------
// | 预付365项目 前端封装 wjn 2015-12-04
// +----------------------------------------------------------------------

//namespace Lib;

class Curl {
	
	/*
	 * 通过curl方式post数据到接口并接收返回 2015/12/30 需要兼容get方式
	 * @access public
	 * @param string $url
	 * @param string $data
	 */
	public function getReturn($url, $data, $post=1) {
		$post_data = json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
		return $this->postData($url, $post_data, $post);
	}

	/*
	 * curl实现 2015/12/30 需要兼容get方式
	 * @access public
	 * @param string $url
	 * @param string $data
	 */
	public function postData($url, $post_data, $post=1) {
		if (is_array($post_data) || is_object($post_data)) {
			$post_data = http_build_query($post_data);
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($post) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
		$ret = curl_exec($ch);
		curl_close($ch);

		if ($return=json_decode($ret)) {
			global $Error;
			if (!empty($return->msg)) {
				$return->errCode = $return->msg;
				$return->msg = $Error[$return->msg];
			}
			return json_encode($return, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		}
		else 
			return $ret;
	}

	/*
	 * 根据yufu365项目接口规范返回错误警报
	 * @access public
	 * @param string $msg
	 */
	public function retError($msg) {
		$msg = trim($msg);
		$destination = LOG_PATH.date("Y-m-d").".txt";
		$log_dir = dirname($destination);
		if (!is_dir($log_dir)) {
			mkdir($log_dir, 0755, true);
		}
		$fh = fopen($destination,"a");
		isset($_SERVER['REQUEST_URI']) ? $path=$_SERVER['REQUEST_URI'] : ( isset($_SERVER['SCRIPT_FILENAME']) ? $path=$_SERVER['SCRIPT_FILENAME'] : $path="" );
		fwrite($fh, "[".date("Y-m-d H:i:s")."--".$path."]".$msg."\r\n");
		fclose($fh);

		global $Error;
		$data = array("status"=>"fail", "msg"=>$Error[$msg]);
		return json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
	}

}
