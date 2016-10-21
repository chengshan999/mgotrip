<?php 
/* 定义站点根 */
define('ROOT_PATH', str_replace('lib/init.php', '', str_replace('\\', '/', __FILE__)));

define('COMMON_PATH',     ROOT_PATH.'lib/common/');
define('LOG_PATH',        ROOT_PATH.'lib/log/');

/* 用户头像保存地址 */
define('ICON_SAVE_PATH',  ROOT_PATH.'../../web/pictures/mgotrip/user/icon/');
define('ICON_VISIT_PATH', 'icon/');//

require(ROOT_PATH.'lib/lib.class.php');

?>