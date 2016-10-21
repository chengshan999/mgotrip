<?php
// +----------------------------------------------------------------------
// | 预付365项目 前端封装 wjn 2015-12-04
// +----------------------------------------------------------------------

class Check {
	
	/*
	 * 字符串验证
	 * @access public
	 * @param string $str
	 * @param string $length
	 */
	public function isString($str="", $length=0) {
		if ($length>0 && strlen($str)>$length)
			return false;
		if ( preg_match('/[^a-zA-Z0-9]/', $str)==true )
			return false;
		return true;
	}

	/*
	 * 空格过滤
	 * @access public
	 * @param string $str
	 * @param string $length
	 */
	public function filterSpace($str="") {
		return preg_replace('/ */', '', $str);
	}

	/*
	 * 拼接字串拆分
	 * @access public
	 * @param string $str
	 */
	public function splitData($str="") {
		if (empty($str)) 
			return false;
		$data = explode('&', trim($str));
		foreach ($data as $k=>$v) {
			$data[$k] = str_replace('###', '&', $v);
		}
		return $data;
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


}
