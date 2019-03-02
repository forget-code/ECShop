<?php

/**
 * ECSHOP 地区切换程序
 * ============================================================================
 * 版权所有 (C) 2005-2007 康盛创想（北京）科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 这是一个免费开源的软件；这意味着您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * ============================================================================
 * $Author: weberliu $
 * $Date: 2007-09-13 16:15:00 +0800 (星期四, 13 九月 2007) $
 * $Id: region.php 12056 2007-09-13 08:15:00Z weberliu $
*/

define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

require('./includes/init.php');
require('./includes/cls_json.php');

header('Content-type: text/html; charset=utf-8');

$type   = !empty($_REQUEST['type'])   ? intval($_REQUEST['type'])   : 0;
$parent = !empty($_REQUEST['parent']) ? intval($_REQUEST['parent']) : 0;

$arr['regions'] = get_regions($type, $parent);
$arr['type']    = $type;
$arr['target']  = !empty($_REQUEST['target']) ? stripslashes(trim($_REQUEST['target'])) : '';

$json = new JSON;
echo $json->encode($arr);

?>