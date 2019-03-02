<?php

/**
 * ECSHOP 绠＄悊涓?績鍏?敤鏂囦欢
 * ============================================================================
 * 鐗堟潈鎵€鏈 (C) 2005-2007 搴风洓鍒涙兂锛堝寳浜?級绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 杩欐槸涓€涓?厤璐瑰紑婧愮殑杞?欢锛涜繖鎰忓懗鐫€鎮ㄥ彲浠ュ湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜
 * 杩涜?淇?敼銆佷娇鐢ㄥ拰鍐嶅彂甯冦€
 * ============================================================================
 * $Author: levie $
 * $Date: 2008-09-04 16:41:47 +0800 (Thu, 04 Sep 2008) $
 * $Id: init.php 5666 2008-09-04 08:41:47Z levie $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

define('ECS_ADMIN', true);

//error_reporting(E_ALL);

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 鍙栧緱褰撳墠ecshop鎵€鍦ㄧ殑鏍圭洰褰 */
define('ROOT_PATH', str_replace('includes/init.php', '', str_replace('\\', '/', __FILE__)));

/* 鍒濆?鍖栬?缃 */
@ini_set('memory_limit',          '16M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);


if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path',      '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path',      '.:' . ROOT_PATH);
}

include('includes/config.inc.php');

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

require('includes/inc_constant.php');
require('includes/cls_ecshop.php');
require('includes/cls_error.php');
require('includes/lib_common.php');
require('includes/lib_main.php');
require('includes/lib_time.php');
require('includes/lib_base.php');
require('includes/cls_exchange.php');
require('includes/global.inc.php');

/* 瀵圭敤鎴蜂紶鍏ョ殑鍙橀噺杩涜?杞?箟鎿嶄綔銆?/
if (!get_magic_quotes_gpc())
{
    if (!empty($_GET))
    {
        $_GET  = addslashes_deep($_GET);
    }
    if (!empty($_POST))
    {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE   = addslashes_deep($_COOKIE);
    $_REQUEST  = addslashes_deep($_REQUEST);
    $_FILES  = addslashes_deep($_FILES);

}
/* 鍒涘缓 ECSHOP 瀵硅薄 */
$ecs = new ECS($db_name, $prefix);

/* 鍒濆?鍖栨暟鎹?簱绫 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
//$db_user = new cls_mysql($db_host, $db_user, $db_pass, '');
$db_bbs = new cls_mysql($db_host, $db_user, $db_pass, 'uc_maifou_net');
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 鍒涘缓閿欒?澶勭悊瀵硅薄 */
$err = new ecs_error('message.htm');

/* 鍒濆?鍖杝ession */
require(ROOT_PATH . 'includes/cls_session.php');
$sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'), 'ECSCP_ID');

/* 鍒濆?鍖 action */
if (!isset($_REQUEST['act']))
{
    $_REQUEST['act'] = '';
}
elseif (($_REQUEST['act'] == 'login' || $_REQUEST['act'] == 'logout' || $_REQUEST['act'] == 'signin') && strpos($_SERVER['PHP_SELF'], '/privilege.php') === false)
{
    $_REQUEST['act'] = '';
}
elseif (($_REQUEST['act'] == 'forget_pwd' || $_REQUEST['act'] == 'reset_pwd' || $_REQUEST['act'] == 'get_pwd') && strpos($_SERVER['PHP_SELF'], '/get_password.php') === false)
{
    $_REQUEST['act'] = '';
}

// TODO : 鐧诲綍閮ㄥ垎鍑嗗?鎷垮嚭鍘诲仛锛屽埌鏃跺€欐妸浠ヤ笅鎿嶄綔涓€璧锋尓杩囧幓
if ($_REQUEST['act'] == 'captcha')
{
    include('includes/cls_captcha.php');

    $img = new captcha('data/captcha/');
    @ob_end_clean(); //娓呴櫎涔嬪墠鍑虹幇鐨勫?浣欒緭鍏
    $img->generate_image();

    exit;
}

require('languages/' .$_CFG['lang']. '/admin/common.php');
require('languages/' .$_CFG['lang']. '/admin/log_action.php');

if (file_exists('languages/' . $_CFG['lang'] . '/admin/' . basename($_SERVER['PHP_SELF'])))
{
    include('languages/' . $_CFG['lang'] . '/admin/' . basename($_SERVER['PHP_SELF']));
}

if (!file_exists('templates/caches'))
{
    @mkdir('templates/caches', 0777);
    @chmod('templates/caches', 0777);
}

if (!file_exists('templates/compiled/admin'))
{
    @mkdir('templates/compiled/admin', 0777);
    @chmod('templates/compiled/admin', 0777);
}

clearstatcache();

/* 鍒涘缓 Smarty 瀵硅薄銆?/
require('includes/cls_template.php');
$smarty = new cls_template;

$smarty->template_dir  = ROOT_PATH . 'templates';
$smarty->compile_dir   = ROOT_PATH . 'templates/compiled/admin';
//$smarty->plugins_dir   = ROOT_PATH . 'includes/smarty/plugins';
//$smarty->caching       = false;
//$smarty->compile_force = false;
//$smarty->register_resource('db', array('db_get_template', 'db_get_timestamp', 'db_get_secure', 'db_get_trusted'));

//$smarty->register_function('insert_scripts', 'smarty_insert_scripts');
//$smarty->register_function('create_pages',   'smarty_create_pages');

$smarty->assign('lang', $_LANG);

/* 濡傛灉鏈夋柊鐗堟湰锛屽崌绾 */
if (!isset($_CFG['ecs_version']))
{
    $_CFG['ecs_version'] = 'v2.5';
}

if (preg_replace('/\.[a-z]*$/i', '', $_CFG['ecs_version']) != preg_replace('/\.[a-z]*$/i', '', VERSION)
        && file_exists('upgrade/index.php'))
{
    // 杞?埌鍗囩骇鏂囦欢
    header("Location: upgrade/index.php\n");

    exit;
}

/* 楠岃瘉绠＄悊鍛樿韩浠 */
if ((!isset($_SESSION['admin_id']) || intval($_SESSION['admin_id']) <= 0) &&
    $_REQUEST['act'] != 'login' && $_REQUEST['act'] != 'signin' &&
    $_REQUEST['act'] != 'forget_pwd' && $_REQUEST['act'] != 'reset_pwd' && $_REQUEST['act'] != 'check_order')
{
    /* session 涓嶅瓨鍦?紝妫€鏌?ookie */
    if (!empty($_COOKIE['ECSCP']['admin_id']) && !empty($_COOKIE['ECSCP']['admin_pass']))
    {
        // 鎵惧埌浜哻ookie, 楠岃瘉cookie淇℃伅
        $sql = 'SELECT user_id, user_name, password, action_list, last_login ' .
                ' FROM ' .$ecs->table('admin_user') .
                " WHERE user_id = '" . intval($_COOKIE['ECSCP']['admin_id']) . "'";
        $row = $db->GetRow($sql);

        if (!$row)
        {
            // 娌℃湁鎵惧埌杩欎釜璁板綍
            setcookie($_COOKIE['ECSCP']['admin_id'],   '', 1);
            setcookie($_COOKIE['ECSCP']['admin_pass'], '', 1);

            if (!empty($_REQUEST['is_ajax']))
            {
                make_json_error($_LANG['priv_error']);
            }
            else
            {
                header("Location: privilege.php?act=login\n");
            }

            exit;
        }
        else
        {
            // 妫€鏌ュ瘑鐮佹槸鍚︽?纭
            if (md5($row['password'] . $_CFG['hash_code']) == $_COOKIE['ECSCP']['admin_pass'])
            {
                !isset($row['last_time']) && $row['last_time'] = '';
                set_admin_session($row['user_id'], $row['user_name'], $row['action_list'], $row['last_time']);

                // 鏇存柊鏈€鍚庣櫥褰曟椂闂村拰IP
                $db->query('UPDATE ' . $ecs->table('admin_user') .
                            " SET last_login = '" . gmtime() . "', last_ip = '" . real_ip() . "'" .
                            " WHERE user_id = '" . $_SESSION['admin_id'] . "'");
            }
            else
            {
                setcookie($_COOKIE['ECSCP']['admin_id'],   '', 1);
                setcookie($_COOKIE['ECSCP']['admin_pass'], '', 1);

                if (!empty($_REQUEST['is_ajax']))
                {
                    make_json_error($_LANG['priv_error']);
                }
                else
                {
                    header("Location: privilege.php?act=login\n");
                }

                exit;
            }
        }
    }
    else
    {
        if (!empty($_REQUEST['is_ajax']))
        {
            make_json_error($_LANG['priv_error']);
        }
        else
        {
            header("Location: privilege.php?act=login\n");
        }

        exit;
    }
}
if ($_REQUEST['act'] != 'login' && $_REQUEST['act'] != 'signin' &&
    $_REQUEST['act'] != 'forget_pwd' && $_REQUEST['act'] != 'reset_pwd' && $_REQUEST['act'] != 'check_order')
{
    $admin_path = preg_replace('/:\d+/', '', $ecs->url());

    if (!empty($_SERVER['HTTP_REFERER']) &&
        strpos(preg_replace('/:\d+/', '', $_SERVER['HTTP_REFERER']), $admin_path) === false)
    {
        if (!empty($_REQUEST['is_ajax']))
        {
            make_json_error($_LANG['priv_error']);
        }
        else
        {
            header("Location: privilege.php?act=login\n");
        }

        exit;
    }
}

/* 绠＄悊鍛樼櫥褰曞悗鍙?湪浠讳綍椤甸潰浣跨敤 act=phpinfo 鏄剧ず phpinfo() 淇℃伅 */
if ($_REQUEST['act'] == 'phpinfo' && function_exists('phpinfo'))
{
    phpinfo();

    exit;
}

//header('Cache-control: private');
header('content-type: text/html; charset=utf-8');
header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

if ((DEBUG_MODE & 1) == 1)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(E_ALL ^ E_NOTICE);
}
if ((DEBUG_MODE & 4) == 4)
{
    include('includes/lib.debug.php');
}

/* 鏈嶅姟杩囨湡閭?欢鎻愮ず */
//mail_expiry_notice();

/* 鍒ゆ柇鏄?惁鏀?寔gzip妯″紡 */
if (gzip_enabled())
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}

?>