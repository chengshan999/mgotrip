<?php
 
// ######(以下配置为PM环境：入网测试环境用，生产环境配置见文档说明)#######
// 签名证书路径
define('SDK_SIGN_CERT_PATH', C('UNIONPAY_FILE_PATH') . 'certs/acp_test_sign.pfx');

// 签名证书密码
const SDK_SIGN_CERT_PWD = '000000';

// 密码加密证书（这条一般用不到的请随便配）
define('SDK_ENCRYPT_CERT_PATH', C('UNIONPAY_FILE_PATH') . 'certs/acp_test_enc.cer');

// 验签证书路径（请配到文件夹，不要配到具体文件）
define('SDK_VERIFY_CERT_DIR', C('UNIONPAY_FILE_PATH') . 'certs/');

// 前台请求地址
const SDK_FRONT_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/frontTransReq.do';

// 后台请求地址
const SDK_BACK_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/backTransReq.do';

// 批量交易
const SDK_BATCH_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/batchTrans.do';

//单笔查询请求地址
const SDK_SINGLE_QUERY_URL = 'https://101.231.204.80:5000/gateway/api/queryTrans.do';

//文件传输请求地址
const SDK_FILE_QUERY_URL = 'https://101.231.204.80:9080/';

//有卡交易地址
const SDK_Card_Request_Url = 'https://101.231.204.80:5000/gateway/api/cardTransReq.do';

//App交易地址
const SDK_App_Request_Url = 'https://101.231.204.80:5000/gateway/api/appTransReq.do';

// 前台通知地址 (商户自行配置通知地址)
const SDK_FRONT_NOTIFY_URL = 'http://www.yufu365.com/unionpay/FrontReceive.php';

// 后台通知地址 (商户自行配置通知地址，需配置外网能访问的地址)
const SDK_BACK_NOTIFY_URL = 'http://www.yufu365.com/unionpay/BackReceive.php';

//文件下载目录 
define('SDK_FILE_DOWN_PATH', C('UNIONPAY_FILE_PATH') . 'file/');

//日志 目录 
define('SDK_LOG_FILE_PATH', C('UNIONPAY_FILE_PATH') . 'logs/');

//日志级别
const SDK_LOG_LEVEL = 'INFO';

//商户号
const SDK_MERCHANT_ID = '777290058110097';

?>