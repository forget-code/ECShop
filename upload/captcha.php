<?php

/**
 * ECSHOP 生成验证码
 * ============================================================================
 * 版权所有 (C) 2005-2007 康盛创想（北京）科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 这是一个免费开源的软件；这意味着您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * ============================================================================
 * $Author: wj $
 * $Date: 2007-10-30 23:34:41 +0800 (星期二, 30 十月 2007) $
 * $Id: captcha.php 13336 2007-10-30 15:34:41Z wj $
*/

define('IN_ECS', true);
define('INIT_NO_SMARTY', true);

require('./includes/init.php');
require('./includes/cls_captcha.php');

$img = new captcha('data/captcha/', $_CFG['captcha_width'], $_CFG['captcha_height']);
@ob_end_clean(); //清除之前出现的多余输入
if (isset($_REQUEST['is_login']))
{
    $img->session_word = 'captcha_login';
}
$img->generate_image();

?>