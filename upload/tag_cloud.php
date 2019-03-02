<?php

/**
 * ECSHOP 标签云
 * ============================================================================
 * 版权所有 (C) 2005-2007 康盛创想（北京）科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 这是一个免费开源的软件；这意味着您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * ============================================================================
 * $Author: fenghl $
 * $Date: 2008-01-14 17:46:45 +0800 (星期一, 14 一月 2008) $
 * $Id: tag_cloud.php 13976 2008-01-14 09:46:45Z fenghl $
*/

define('IN_ECS', true);

require('./includes/init.php');

assign_template();
$position = assign_ur_here(0, $_LANG['tag_cloud']);
$smarty->assign('page_title', $position['title']);    // 页面标题
$smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
$smarty->assign('categories', get_categories_tree()); // 分类树
$smarty->assign('helps',      get_shop_help());       // 网店帮助
$smarty->assign('top_goods',  get_top10());           // 销售排行
$smarty->assign('promotion_info', get_promotion_info());

/* 调查 */
$vote = get_vote();
if (!empty($vote))
{
    $smarty->assign('vote_id', $vote['id']);
    $smarty->assign('vote',    $vote['content']);
}

assign_dynamic('tag_cloud');

$tags = get_tags();

if (!empty($tags))
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    color_tag($tags);
}

$smarty->assign('tags', $tags);

$smarty->display('tag_cloud.dwt');

?>