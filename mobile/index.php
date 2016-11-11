<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('APP_DEBUG', true);
define('APP_PATH','./Application/');

//define('API_SITE','http://api.mgolocal.com/');
define('API_SITE','http://qs.test.yufu365.com/');
// 引入ThinkPHP入口文件
require '../service/v1.0/tp/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单1