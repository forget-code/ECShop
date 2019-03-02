<?php

/**
 * ECSHOP 升级程序 之 控制器
 * ============================================================================
 * 版权所有 (C) 2005-2007 康盛创想（北京）科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 这是一个免费开源的软件；这意味着您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * ============================================================================
 * $Author: luhengqi $
 * $Date: 2007-02-12 14:56:05 +0800 (星期一, 12 二月 2007) $
 * $Id: index.php 5721 2007-02-12 06:56:05Z luhengqi $
 */

require_once('./includes/init.php');

/* 初始化语言变量 */
$updater_lang = get_current_lang();
if ($updater_lang === false)
{
    die('Please set system\'s language!');
}

/* 加载升级程序所使用的语言包 */
$updater_lang_package_path = ROOT_PATH . 'upgrade/languages/' . $updater_lang . '.php';
if (file_exists($updater_lang_package_path))
{
    include_once($updater_lang_package_path);
    $smarty->assign('lang', $_LANG);
}
else
{
    die('Can\'t find language package!');
}

/* 初始化流程控制变量 */
$step = isset($_REQUEST['step']) ? $_REQUEST['step'] : 'readme';
if ($step !== 'done' && get_current_version() === get_new_version())
{
    $step = 'error';
    $err->add($_LANG['is_last_version']);

    if (isset($_REQUEST['IS_AJAX_REQUEST'])
            && $_REQUEST['IS_AJAX_REQUEST'] === 'yes')
    {
        die(implode(',', $err->get_all()));
    }
}
switch($step)
{
/* 说明页面 */
case 'readme' :
    $smarty->assign('new_version', VERSION);
    $smarty->assign('old_version', get_current_version());
    $smarty->assign('updater_lang', $updater_lang);
    $smarty->display('readme.php');

    break;

/* 检查环境页面 */
case 'check' :
    include_once(ROOT_PATH . 'upgrade/includes/lib_env_checker.php');
    include_once(ROOT_PATH . 'upgrade/includes/checking_dirs.php');

    $dir_checking = check_dirs_priv($checking_dirs);

    $templates_root = array(
        'dwt' => ROOT_PATH . 'themes/default/',
        'lbi' => ROOT_PATH . 'themes/default/library/');
    $template_checking = check_templates_priv($templates_root);

    $rename_priv = check_rename_priv();

    $disabled = '';
    if ($dir_checking['result'] === 'ERROR'
            || !empty($template_checking)
            || !empty($rename_priv))
    {
        $disabled = 'disabled="true"';
    }

    $has_unwritable_tpl = 'yes';
    if (empty($template_checking))
    {
        $template_checking = $_LANG['all_are_writable'];
        $has_unwritable_tpl = 'no';
    }

    $smarty->assign('config_info', get_config_info());
    $smarty->assign('dir_checking', $dir_checking['detail']);
    $smarty->assign('has_unwritable_tpl', $has_unwritable_tpl);
    $smarty->assign('template_checking', $template_checking);
    $smarty->assign('rename_priv', $rename_priv);
    $smarty->assign('disabled', $disabled);
    $smarty->display('checking.php');

    break;

/* 获得版本列表 */
case 'get_ver_list' :
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $cur_ver = get_current_version();
    $new_ver = get_new_version();
    $needup_ver_list = get_needup_version_list($cur_ver, $new_ver);
    $result = array('msg'=>'OK', 'cur_ver'=>$cur_ver, 'needup_ver_list'=>$needup_ver_list);

    echo  $json->encode($result);

    break;

/* 获得某个SQL文件的SQL语句数 */
case 'get_record_number' :
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $next_ver = isset($_REQUEST['next_ver']) ? $_REQUEST['next_ver'] : '';
    $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

    if ($next_ver === '' || $type === '')
    {
        die('EMPTY');
    }

    $result = array('msg'=>'OK', 'rec_num'=>get_record_number($next_ver, $type));
    echo  $json->encode($result);

    break;

/* 备份数据库 */
case 'dump_database' :
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $next_ver = isset($_REQUEST['next_ver']) ? $_REQUEST['next_ver'] : '';
    if ($next_ver === '')
    {
        die('EMPTY');
    }

    $result = dump_database($next_ver);

    if($result === false)
    {
        echo implode(',', $err->last_message());
    }
    else
    {
        echo 'OK';
    }

    break;
case 'rollback' :
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $next_ver = isset($_REQUEST['next_ver']) ? $_REQUEST['next_ver'] : '';
    if ($next_ver === '')
    {
        die('EMPTY');
    }

    $result = rollback($next_ver);

    if($result === false)
    {
        echo implode(',', $err->last_message());
    }
    else
    {
        echo 'OK';
    }

    break;

/* 升级文件 */
case 'update_files' :
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $next_ver = isset($_REQUEST['next_ver']) ? $_REQUEST['next_ver'] : '';

    if ($next_ver === '')
    {
        die('EMPTY');
    }

    $result = update_files($next_ver);
    echo  $json->encode($result);

    break;

/* 升级数据结构 */
case 'update_structure' :
    $next_ver = isset($_REQUEST['next_ver']) ? $_REQUEST['next_ver'] : '';
    $cur_pos = isset($_REQUEST['cur_pos']) ? $_REQUEST['cur_pos'] : '';

    if ($next_ver === '' || intval($cur_pos) < 1)
    {
        die('EMPTY');
    }

    $result = update_structure_automatically($next_ver, intval($cur_pos)-1);
    if ($result === false)
    {
        echo implode(',', $err->last_message());
    }
    else
    {
        echo 'OK';
    }

    break;

/* 升级数据 */
case 'update_data' :
    $next_ver = isset($_REQUEST['next_ver']) ? $_REQUEST['next_ver'] : '';

    if ($next_ver === '')
    {
        die('EMPTY');
    }

    update_database_optionally($next_ver);
    $result = update_data_automatically($next_ver);
    if ($result === false)
    {
        die(implode(',', $err->last_message()));
    }

    echo 'OK';

    break;

/* 更新版本号 */
case 'update_version' :
    $next_ver = isset($_REQUEST['next_ver']) ? $_REQUEST['next_ver'] : '';

    if ($next_ver === '')
    {
        die('EMPTY');
    }

    update_version($next_ver);

    echo 'OK';

    break;

/* 成功页面 */
case 'done' :
    clear_all_files();
    $smarty->display('done.php');

    break;

/* 出错页面 */
case 'error' :
    $err_msg = implode(',', $err->get_all());
    if (empty($err_msg))
    {
        $err_msg = $_LANG['js_error'];
    }
    $smarty->assign('err_msg', $err_msg);
    $smarty->display('error.php');

    break;

/* 出现异常 */
default :
    die('ERROR, unknown step!');

}

?>