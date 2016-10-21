<?php
 
// ######(以下配置为PM环境：入网测试环境用，生产环境配置见文档说明)#######
// 签名证书路径
//const SDK_SIGN_CERT_PATH = 'D:/certs/acp_prod_sign.pfx';
define('SDK_SIGN_CERT_PATH', C('UNIONPAY_FILE_PATH') . 'certs/unionpayprivatekey.pfx');

// 签名证书密码
const SDK_SIGN_CERT_PWD = '903903';

// 密码加密证书（这条一般用不到的请随便配）
//const SDK_ENCRYPT_CERT_PATH = 'D:/certs/acp_prod_enc.cer';
define('SDK_ENCRYPT_CERT_PATH', C('UNIONPAY_FILE_PATH') . 'certs/acp_prod_enc.cer');

// 验签证书路径（请配到文件夹，不要配到具体文件）
//const SDK_VERIFY_CERT_DIR = 'D:/certs/';
define('SDK_VERIFY_CERT_DIR', C('UNIONPAY_FILE_PATH') . 'certs/');

// 前台请求地址
const SDK_FRONT_TRANS_URL = 'https://gateway.95516.com/gateway/api/frontTransReq.do';

// 后台请求地址
const SDK_BACK_TRANS_URL = 'https://gateway.95516.com/gateway/api/backTransReq.do';

// 批量交易
const SDK_BATCH_TRANS_URL = 'https://gateway.95516.com/gateway/api/batchTrans.do';

//单笔查询请求地址
const SDK_SINGLE_QUERY_URL = 'https://gateway.95516.com/gateway/api/queryTrans.do';

//文件传输请求地址
const SDK_FILE_QUERY_URL = 'https://filedownload.95516.com/';

//有卡交易地址
const SDK_Card_Request_Url = 'https://gateway.95516.com/gateway/api/cardTransReq.do';

//App交易地址
const SDK_App_Request_Url = 'https://gateway.95516.com/gateway/api/appTransReq.do';

// 前台通知地址 (商户自行配置通知地址)
//const SDK_FRONT_NOTIFY_URL = 'http://www.yufu365.com/unionpay/FrontReceive.php';
const SDK_FRONT_NOTIFY_URL = 'http://www.yufu365.com';

// 后台通知地址 (商户自行配置通知地址)
const SDK_BACK_NOTIFY_URL = 'http://www.yufu365.com/unionpay/BackReceive.php';

//文件下载目录 
//const SDK_FILE_DOWN_PATH = 'd:/file/';
define('SDK_FILE_DOWN_PATH', C('UNIONPAY_FILE_PATH') . 'file/');

//日志 目录 
//const SDK_LOG_FILE_PATH = 'd:/logs/';
define('SDK_LOG_FILE_PATH', C('UNIONPAY_FILE_PATH') . 'logs/');

//日志级别
const SDK_LOG_LEVEL = 'INFO';

//商户号
const SDK_MERCHANT_ID = '802310053110714';

?>