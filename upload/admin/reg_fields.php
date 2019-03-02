<?php

/**
 * ECSHOP 会员等级管理程序
 * ============================================================================
 * 版权所有 2005-2008 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: testyang $
 * $Id: reg_fields.php 15013 2008-10-23 09:31:42Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$exc = new exchange($ecs->table("reg_fields"), $db, 'id', 'reg_field_name');

/*------------------------------------------------------ */
//-- 会员注册项列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    $fields = array();
    $fields = $db->getAll("SELECT * FROM " . $ecs->table('reg_fields') . "ORDER BY id");

    $smarty->assign('ur_here',      $_LANG['021_reg_fields']);
    $smarty->assign('action_link',  array('text' => $_LANG['add_reg_field'], 'href'=>'reg_fields.php?act=add'));
    $smarty->assign('full_page',    1);

    $smarty->assign('reg_fields',   $fields);

    assign_query_info();
    $smarty->display('reg_fields.htm');
}


/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $fields = array();
    $fields = $db->getAll("SELECT * FROM " .$ecs->table('reg_fields') . "ORDER BY id");

    $smarty->assign('reg_fields',   $fields);
    make_json_result($smarty->fetch('reg_fields.htm'));
}

/*------------------------------------------------------ */
//-- 添加会员注册项
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('reg_fields');

    $form_action          = 'insert';

    $smarty->assign('ur_here',     $_LANG['add_reg_field']);
    $smarty->assign('action_link', array('text' => $_LANG['021_reg_fields'], 'href'=>'reg_fields.php?act=list'));
    $smarty->assign('form_action', $form_action);

    assign_query_info();
    $smarty->display('reg_field_info.htm');
}

/*------------------------------------------------------ */
//-- 增加会员注册项到数据库
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('reg_fields');

    /* 检查是否存在重名的会员注册项 */
    if (!$exc->is_only('reg_field_name', trim($_POST['reg_field_name'])))
    {
        sys_msg(sprintf($_LANG['field_name_exist'], trim($_POST['reg_field_name'])), 1);
    }

    $sql = "INSERT INTO " .$ecs->table('reg_fields') ."( ".
                "reg_field_name".
            ") VALUES (".
                "'$_POST[reg_field_name]')";
    $db->query($sql);

    /* 管理员日志 */
    admin_log(trim($_POST['reg_field_name']), 'add', 'reg_fields');
    clear_cache_files();

    $lnk[] = array('text' => $_LANG['back_list'],    'href'=>'reg_fields.php?act=list');
    $lnk[] = array('text' => $_LANG['add_continue'], 'href'=>'reg_fields.php?act=add');
    sys_msg($_LANG['add_field_success'], 0, $lnk);
}

/*------------------------------------------------------ */
//-- 删除会员注册项
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('reg_fields');

    $field_id = intval($_GET['id']);
    $field_name = $exc->get_name($field_id);

    if ($exc->drop($field_id))
    {
        /* 删除会员扩展信息表的相应信息 */
        $sql = "DELETE FROM " . $GLOBALS['ecs']->table('reg_extend_info') . " WHERE reg_field_id = '" . $field_id . "'";
        @$GLOBALS['db']->query($sql);

        admin_log(addslashes($field_name), 'remove', 'reg_fields');
        clear_cache_files();
    }

    $url = 'reg_fields.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;

}
/*
 *  编辑会员注册项名称
 */
elseif ($_REQUEST['act'] == 'edit_name')
{
    $id = intval($_REQUEST['id']);
    $val = empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));
    check_authz_json('reg_fields');
    if ($exc->is_only('reg_field_name', $val, $id))
    {
        if ($exc->edit("reg_field_name = '$val'", $id))
        {
            /* 管理员日志 */
            admin_log($val, 'edit', 'reg_fields');
            clear_cache_files();
            make_json_result(stripcslashes($val));
        }
        else
        {
            make_json_error($db->error());
        }
    }
    else
    {
        make_json_error(sprintf($_LANG['rank_name_exists'], htmlspecialchars($val)));
    }
}

?>