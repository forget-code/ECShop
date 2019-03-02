<?php

define('IN_ECS', true);

/* 报告所有错误 */
ini_set('display_errors',  1);
error_reporting(E_ALL ^ E_NOTICE);

/* 清除所有和文件操作相关的状态信息 */
clearstatcache();

/* 定义站点根 */
define('ROOT_PATH', str_replace('upgrade/includes/init.php', '', str_replace('\\', '/', __FILE__)));

require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'admin/includes/lib_main.php');
require(ROOT_PATH . 'includes/lib_time.php');
clear_all_files();

/* 加载数据库配置文件 */
if (file_exists(ROOT_PATH . 'data/config.php'))
{
    include(ROOT_PATH . 'data/config.php');
}
elseif (file_exists(ROOT_PATH . 'includes/config.php'))
{
    if (!rename(ROOT_PATH . 'includes/config.php', ROOT_PATH . 'data/config.php'))
    {
        die('Can\'t move config.php, please move it from includes/ to data/ manually!');
    }
    include(ROOT_PATH . 'data/config.php');
}
else
{
    die('Can\'t find config.php!');
}

require(ROOT_PATH . 'includes/cls_ecshop.php');
require(ROOT_PATH . 'includes/cls_mysql.php');
/* 创建 ECSHOP 对象 */
$ecs = new ECS($db_name, $prefix);
/* 初始化数据库类 */
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);

/* 创建错误处理对象 */
require(ROOT_PATH . 'includes/cls_error.php');
$err = new ecs_error('message.dwt');

require(ROOT_PATH . 'includes/cls_sql_executor.php');

/* 初始化模板引擎 */
require(ROOT_PATH . 'upgrade/includes/cls_template.php');
$smarty = new template(ROOT_PATH . 'upgrade/templates/');

require(ROOT_PATH . 'upgrade/includes/lib_updater.php');

/* 发送HTTP头部，保证浏览器识别UTF8编码 */
header('Content-type: text/html; charset=utf-8');

@set_time_limit(360);
?>