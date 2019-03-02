<?php

/**
 * ECSHOP 后台标签管理
 * ============================================================================
 * 版权所有 (C) 2005-2007 康盛创想（北京）科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 这是一个免费开源的软件；这意味着您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * ============================================================================
 * $Author: fenghl $
 * $Date: 2008-01-25 14:12:18 +0800 (星期五, 25 一月 2008) $
 * $Id: tag_manage.php 14050 2008-01-25 06:12:18Z fenghl $
*/

define('IN_ECS', true);

require('includes/init.php');

/* act操作项的初始化 */
$_REQUEST['act'] = trim($_REQUEST['act']);
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}

/*------------------------------------------------------ */
//-- 获取标签数据列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 权限判断 */
    admin_priv('tag_manage');

    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['tag_list']);
    $smarty->assign('full_page',    1);

    $tag_list = get_tag_list();
    $smarty->assign('tag_list',     $tag_list['tags']);
    $smarty->assign('filter',       $tag_list['filter']);
    $smarty->assign('record_count', $tag_list['record_count']);
    $smarty->assign('page_count',   $tag_list['page_count']);

    $sort_flag  = sort_flag($tag_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 页面显示 */
    assign_query_info();
    $smarty->display('tag_manage.htm');
}

/*------------------------------------------------------ */
//-- 编辑
/*------------------------------------------------------ */

elseif($_REQUEST['act'] == 'edit')
{
    admin_priv('tag_manage');

    $tag_id = $_GET['id'];
    $tag = get_tag_info($tag_id);

    $smarty->assign('tag', $tag);
    $smarty->assign('ur_here',      $_LANG['tag_edit']);
    $smarty->assign('action_link', array('href' => 'tag_manage.php?act=list', 'text' => $_LANG['tag_list']));

    assign_query_info();
    $smarty->display('tag_edit.htm');
}

/*------------------------------------------------------ */
//-- 更新
/*------------------------------------------------------ */

elseif($_REQUEST['act'] == 'update')
{
    admin_priv('tag_manage');

    $tag_words = empty($_POST['tag_name']) ? '' : trim($_POST['tag_name']);
    $id = intval($_POST['id']);
    $goods_id = intval($_POST['goods_id']);

    if (tag_is_only($tag_words, $id))
    {
        sys_msg(sprintf($_LANG['tagword_exist'], $tag_words));
    }
    else
    {
        if (edit_tag($tag_words, $id, $goods_id))
        {
            admin_log($tag_name, 'edit', 'tag');

            /* 清除缓存 */
            clear_cache_files();

            $link[0]['text'] = $_LANG['back_list'];
            $link[0]['href'] = 'tag_manage.php?act=list';

            sys_msg($_LANG['tag_edit_success'], 0, $link);
        }
        else
        {
            sys_msg(sprintf($_LANG['tagedit_fail'], $name));
        }
    }
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('tag_manage');

    $tag_list = get_tag_list();
    $smarty->assign('tag_list',     $tag_list['tags']);
    $smarty->assign('filter',       $tag_list['filter']);
    $smarty->assign('record_count', $tag_list['record_count']);
    $smarty->assign('page_count',   $tag_list['page_count']);

    $sort_flag  = sort_flag($tag_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('tag_manage.htm'), '',
        array('filter' => $tag_list['filter'], 'page_count' => $tag_list['page_count']));
}

/*------------------------------------------------------ */
//-- 搜索
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'search_goods')
{
    check_authz_json('tag_manage');

    include_once('../includes/cls_json.php');

    $json   = new JSON;
    $filter = $json->decode($_GET['JSON']);
    $arr    = get_goods_list($filter);
    if (empty($arr))
    {
        $arr[0] = array(
            'goods_id'   => 0,
            'goods_name' => ''
        );
    }

    make_json_result($arr);
}

/*------------------------------------------------------ */
//-- 批量删除标签
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch_drop')
{
    admin_priv('tag_manage');

    if (isset($_POST['checkboxes']))
    {
        $count = 0;
        foreach ($_POST['checkboxes'] AS $key => $id)
        {
            $sql = "DELETE FROM " .$ecs->table('tag'). " WHERE tag_id='$id'";
            $db->query($sql);

            $count++;
        }

        admin_log($count, 'remove', 'tag_manage');
        clear_cache_files();

        $link[] = array('text' => $_LANG['back_list'], 'href'=>'tag_manage.php?act=list');
        sys_msg(sprintf($_LANG['drop_success'], $count), 0, $link);
    }
    else
    {
        $link[] = array('text' => $_LANG['back_list'], 'href'=>'tag_manage.php?act=list');
        sys_msg($_LANG['no_select_tag'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 删除标签
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('tag_manage');

    include_once('../includes/cls_json.php');
    $json = new JSON;

    $id = intval($_GET['id']);

    /* 获取删除的标签的名称 */
    $tag_name = $db->getOne("SELECT tag_words FROM " .$ecs->table('tag'). " WHERE tag_id = '$id'");

    $sql = "DELETE FROM " .$ecs->table('tag'). " WHERE tag_id = '$id'";
    $result = $GLOBALS['db']->query($sql);
    if ($result)
    {
        /* 管理员日志 */
        admin_log(addslashes($tag_name), 'remove', 'tag_manage');

        $url = 'tag_manage.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
        header("Location: $url\n");
        exit;
    }
    else
    {
       make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑标签名称
/*------------------------------------------------------ */

elseif($_REQUEST['act'] == "edit_tag_name")
{
    check_authz_json('tag_manage');

    $name = trim($_POST['val']);
    $id = intval($_POST['id']);

    if (tag_is_only($name, $id))
    {
        make_json_error(sprintf($_LANG['tagword_exist'], $name));
    }
    else
    {
        if (edit_tag($name, $id))
        {
            admin_log($name,'edit','tag');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['tagedit_fail'], $name));
        }
    }
}

/**
 * 判断同一商品的标签是否唯一
 *
 * @param $name  标签名
 * @param $id  标签id
 * @return bool
 */
function tag_is_only($name, $id)
{
    $db = $GLOBALS['db'];
    $sql = "SELECT goods_id FROM ecs_tag WHERE tag_id = '$id'";
    $row = $db->getRow($sql);

    $sql = "SELECT COUNT(*) FROM ecs_tag WHERE tag_words = '$name'" .
           " AND goods_id = '$row[goods_id]' AND tag_id != '$id'";

    if($db->getOne($sql) == 0)
    {
        return false;
    }
    else
    {
        return true;
    }
}

/**
 * 更新标签
 *
 * @param  $name
 * @param  $id
 * @return bool
 */
function edit_tag($name, $id, $goods_id = '')
{
    $db = $GLOBALS['db'];
    $sql = "UPDATE ecs_tag SET tag_words = '$name'";
    if(!empty($goods_id))
    {
        $sql .= ", goods_id = '$goods_id'";
    }
    $sql .= " WHERE tag_id = '$id'";
    if ($db->query($sql))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * 获取标签数据列表
 * @access  public
 * @return  array
 */
function get_tag_list()
{
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 't.tag_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('tag');
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    $sql = "SELECT t.tag_id, u.user_name, t.goods_id, g.goods_name, t.tag_words ".
            "FROM " .$GLOBALS['ecs']->table('tag'). " AS t ".
            "LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=t.user_id ".
            "LEFT JOIN " .$GLOBALS['ecs']->table('goods'). " AS g ON g.goods_id=t.goods_id ".
            "ORDER by $filter[sort_by] $filter[sort_order] LIMIT ". $filter['start'] .", ". $filter['page_size'];
    $row = $GLOBALS['db']->getAll($sql);
    foreach($row as $k=>$v)
    {
        $row[$k]['tag_words'] = htmlspecialchars($v['tag_words']);
    }

    $arr = array('tags' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 取得标签的信息
 * return array
 */

function get_tag_info($tag_id)
{
    $sql = 'SELECT tag_id, tag_words, goods_name, t.goods_id FROM ' . $GLOBALS['ecs']->table('tag') . ' AS t' .
           ' LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON t.goods_id=g.goods_id' .
           " WHERE tag_id = '$tag_id'";
    $row = $GLOBALS['db']->getRow($sql);

    return $row;
}

?>