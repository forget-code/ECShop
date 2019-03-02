<?php

/**
 * ECSHOP 商品页
 * ============================================================================
 * 版权所有 (C) 2005-2007 康盛创想（北京）科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 这是一个免费开源的软件；这意味着您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * ============================================================================
 * $Author: bugii $
 * $Date: 2007-09-24 10:02:38 +0800 (星期一, 24 九月 2007) $
 * $Id: goods.php 12384 2007-09-24 02:02:38Z bugii $
*/

define('IN_ECS', true);

require('./includes/init.php');

$goods_id = !empty($_GET['id']) ? intval($_GET['id']) : '';
$act = !empty($_GET['act']) ? $_GET['act'] : '';

$_LANG['kilogram'] = '千克';
$_LANG['gram'] = '克';
$_LANG['home'] = '首页';
$smarty->assign('goods_id', $goods_id);
$goods_info = get_goods_info($goods_id);
$goods_info['goods_name'] = encode_output($goods_info['goods_name']);
$goods_info['goods_brief'] = encode_output($goods_info['goods_brief']);
$smarty->assign('goods_info', $goods_info);
$smarty->assign('footer', get_footer());

/* 查看商品图片操作 */
if ($act == 'view_img')
{
    $smarty->display('goods_img.wml');
    exit();
}

/* 检查是否有商品品牌 */
if (!empty($goods_info['brand_id']))
{
    $brand_name = $db->getOne("SELECT brand_name FROM " . $ecs->table('brand') . " WHERE brand_id={$goods_info['brand_id']}");
    $smarty->assign('brand_name', encode_output($brand_name));
}
/* 显示分类名称 */
$cat_array = get_parent_cats($goods_info['cat_id']);
krsort($cat_array);
$cat_str = '';
foreach ($cat_array as $key => $cat_data)
{
    $cat_array[$key]['cat_name'] = encode_output($cat_data['cat_name']);
    $cat_str .= "<a href='category.php?c_id={$cat_data['cat_id']}'>" . encode_output($cat_data['cat_name']) . "</a>-&gt;";
}
$smarty->assign('cat_array', $cat_array);
$comment = assign_comment($goods_id, 0);
$smarty->assign('comment', $comment);
$smarty->display('goods.wml');

?>