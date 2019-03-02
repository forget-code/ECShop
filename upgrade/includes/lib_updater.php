<?php

/**
 * ECSHOP 升级程序 之 模型
 * ============================================================================
 * 版权所有 (C) 2005-2007 康盛创想（北京）科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 这是一个免费开源的软件；这意味着您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * ============================================================================
 * $Author: paulgao $
 * $Date: 2007-01-30 15:37:32 +0800 (星期二, 30 一月 2007) $
 * $Id: index.php 4749 2007-01-30 07:37:32Z paulgao $
 */

/**
 * 获得需要升级的版本号列表。
 * @param   string      $old_version    旧版本号
 * @param   string      $new_version    新版本号
 * @return  array
 */
function get_needup_version_list($old_version, $new_version)
{
    /* 需要升级的版本号列表 */
    $need_list = array();
    $need = false;
    $version_history = read_version_history();

    foreach ($version_history as $version)
    {
        if ($need)
        {
            $need_list[] = $version;
            if ($version == $new_version)
            {
                $need = false;
            }
        }
        else
        {
            if ($version == $old_version)
            {
                $need = true;
            }
        }
    }

    return $need_list;
}

/**
 * 读取版本历史记录，并按字典序排序。
 * @return  array
 */
function read_version_history()
{
    $ver_history = array('v2.0.5');
    $pkg_root = ROOT_PATH . 'upgrade/packages/';
    $ver_handle = @opendir($pkg_root);
    while (($filename = @readdir($ver_handle)) !== false)
    {
        $filepath = $pkg_root . $filename;
        if(is_dir($filepath) && strpos($filename, '.') !== 0)
        {
            $ver_history[] = $filename;
        }
    }
    asort($ver_history);

    return $ver_history;
}

/**
 * 获得原有系统的语言。
 * @return  mixed       成功返回具体的语言，失败返回false。
 */
function  get_current_lang()
{
    global $db, $ecs;

    $lang = $db->getOne('SELECT value FROM ' . $ecs->table('shop_config') . " WHERE code = 'lang'");
    $lang = $lang ? $lang : false;

    return $lang;
}

/**
 * 获得最新的版本号。
 * @return  string
 */
function get_new_version()
{
    return  preg_replace('/(?:\.|\s+)[a-z]*$/i', '', VERSION);
}

/**
 * 获得原有系统的版本号。
 * @return  string
 */
function  get_current_version()
{
    global $db, $ecs;

    $ver = $db->getOne('SELECT value FROM ' . $ecs->table('shop_config') . " WHERE code = 'ecs_version'");
    $ver = $ver ? $ver : 'v2.0.5';
    $ver = preg_replace('/\.[a-z]*$/i', '', $ver);

    return $ver;
}

/**
 * 获得某个SQL文件的记录数(SQL语句数量)。
 * @return  int
 */
function get_record_number($next_ver, $type)
{
    global $db, $prefix;

    $file_path = ROOT_PATH . 'upgrade/packages/' . $next_ver . '/' . $type . '.sql';
    $se = new sql_executor($db, 'utf8', 'ecs_', $prefix);

    $query_items = $se->parse_sql_file($file_path);

    if(empty($query_items))
    {
        return 0;
    }

    return count($query_items);
}

/**
 * 获得配置信息。
 * @return  array
 */
function get_config_info()
{
    global $_LANG;
    $config = array();

    $config['config_path'] = array($_LANG['config_path'], '/data/config.php');
    $config['db_host'] = array($_LANG['db_host'], $GLOBALS['db_host']);
    $config['db_name'] = array($_LANG['db_name'], $GLOBALS['db_name']);
    $config['db_user'] = array($_LANG['db_user'], $GLOBALS['db_user']);
    $config['db_pass'] = array($_LANG['db_pass'], '*******');
    $config['prefix'] = array($_LANG['db_prefix'], $GLOBALS['prefix']);
    if (isset($GLOBALS['timezone']))
    {
        $config['timezone'] = array($_LANG['timezone'], $GLOBALS['timezone']);
    }
    if (isset($GLOBALS['cookie_path']))
    {
        $config['cookie_path'] = array($_LANG['cookie_path'], $GLOBALS['cookie_path']);
    }
    if (isset($GLOBALS['admin_dir']))
    {
        $config['admin_dir'] = array($_LANG['admin_dir'], $GLOBALS['admin_dir']);
    }

    return $config;
}

/**
 * 创建版本对象。
 * @return  mixed   成功返回版本对象，失败返回false。
 */
function create_ver_obj($version)
{
    global $err, $_LANG;

    $file_path = ROOT_PATH . 'upgrade/packages/' . $version . '/' . $version . '.php';
    if (file_exists($file_path))
    {
        include_once($file_path);

        // 把 . 替换成 _，把空格去掉，前面加 up_
        $classname = 'up_' . str_replace('.', '_', str_replace(' ', '', $version));
        $ver_obj = new $classname();

        return $ver_obj;
    }
    else
    {
        $err->add($_LANG['create_ver_failed']);

        return false;
    }
}

/**
 * 机械化地升级数据库结构。
 * @return  boolean
 */
function update_structure_automatically($next_ver, $cur_pos)
{
    global $db, $prefix, $err;

    $ver_obj = create_ver_obj($next_ver);
    if (!is_object($ver_obj) || empty($ver_obj->sql_files['structure']))
    {
        return true;
    }

    $structure_path = ROOT_PATH . 'upgrade/packages/' . $next_ver . '/structure.sql';
    $se = new sql_executor($db, 'utf8', 'ecs_', $prefix,
            ROOT_PATH . 'data/upgrade_'.$next_ver.'.log',
            $ver_obj->auto_match, array(1062, 1146));

    $query_item = $se->get_spec_query_item($structure_path, $cur_pos);
    $se->query($query_item);

    if (!empty($se->error))
    {
        $err->add($se->error);
        return false;
    }

    return true;
}

/**
 * 机械化地升级数据库数据。
 * @return  boolean
 */
function update_data_automatically($next_ver)
{
    global $db, $ecs, $prefix, $err;

    $ver_obj = create_ver_obj($next_ver);
    if (!is_object($ver_obj) || empty($ver_obj->sql_files['data']))
    {
        return true;
    }

    $se = new sql_executor($db, 'utf8', 'ecs_', $prefix,
            ROOT_PATH . 'data/upgrade_'.$next_ver.'.log',
            $ver_obj->auto_match, array(1062, 1146));

    $data_path = '';
    $ver_root = ROOT_PATH . 'upgrade/packages/' . $next_ver . '/';
    if (is_array($ver_obj->sql_files['data']))
    {
        $lang = get_current_lang();
        if (!isset($ver_obj->sql_files['data'][$lang]))
        {
           $lang = 'zh_cn';
        }
        $data_path = $ver_root . $ver_obj->sql_files['data'][$lang];
    }
    else
    {
        $data_path =  $ver_root . $ver_obj->sql_files['data'];
    }
    $se->run_all(array($data_path));

    if (!empty($se->error))
    {
        $err->add($se->error);
        return false;
    }

    return true;
}

/**
 * 随心所欲地升级数据库。
 * @return  boolean
 */
function update_database_optionally($next_ver)
{
    $ver_obj = create_ver_obj($next_ver);
    if ($ver_obj === false)
    {
        return false;
    }

    $ver_obj->update_database_optionally();

    return true;
}

/**
 * 升级文件。
 * @return  array
 */
function update_files($next_ver)
{
    global $err;

    $ver_obj = create_ver_obj($next_ver);
    if ($ver_obj === false)
    {
        return array('msg'=>'OK');
    }

    $result = $ver_obj->update_files();
    if ($result === false)
    {
        $msg = $err->last_message();
        if (is_array($msg)
                && isset($msg['type'])
                && $msg['type'] === 'NOTICE')
        {
            return array('type'=>'NOTICE', 'msg'=>$msg);
        }
    }

    return array('msg'=>'OK');
}

/**
 * 升级版本。
 * @return  void
 */
function update_version($next_ver)
{
    global $db, $ecs;

    $db->query('UPDATE ' . $ecs->table('shop_config') . "  SET value='$next_ver' WHERE code='ecs_version'");
}

function dump_database($next_ver)
{
    global $db, $err;

    include_once(ROOT_PATH . 'admin/includes/cls_sql_dump.php');
    require_once(ROOT_PATH . 'upgrade/packages/' . $next_ver . '/dump_table.php');
    $path = ROOT_PATH . 'upgrade';
    @set_time_limit(300);

    $dump = new cls_sql_dump($db);
    $run_log = ROOT_PATH . 'upgrade/run.log';
    $sql_file_name = $next_ver;
    $max_size = '2048';
    $vol = 1;

    /* 变量验证 */
    $allow_max_size = intval(@ini_get('upload_max_filesize')); //单位M
    if ($allow_max_size > 0 && $max_size > ($allow_max_size * 1024))
    {
        $max_size = $allow_max_size * 1024; //单位K
    }

    if ($max_size > 0)
    {
        $dump->max_size = $max_size * 1024;
    }

    $tables = array();
    foreach ($temp AS $table)
    {
        $tables[$table] = -1;
    }

    $dump->put_tables_list($run_log, $tables);

    /* 开始备份 */
    $tables = $dump->dump_table($run_log, $vol);

    if ($tables === false)
    {
        $err->add($dump->errorMsg());
        return false;
    }

    if(@file_put_contents(ROOT_PATH . 'upgrade/' . $sql_file_name . '.sql', $dump->dump_sql))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function rollback($next_ver)
{
    global $db, $prefix, $err;

    $structure_path[] = ROOT_PATH . 'upgrade/' . $next_ver . '.sql';

    if(!file_exists($structure_path[0]))
    {
        return false;
    }

    $se = new sql_executor($db, 'utf8', 'ecs_', $prefix);
    $result = $se->run_all($structure_path);
    if ($result === false)
    {
        $err->add($se->error);
        return false;
    }

    return true;
}

?>
