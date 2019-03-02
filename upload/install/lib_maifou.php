<?php

/**
 * ECSHOP 鍓嶅彴鍏?敤鏂囦欢
 * ============================================================================
 * 鐗堟潈鎵€鏈 (C) 2005-2008 搴风洓鍒涙兂锛堝寳浜?級绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛沨ttp://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: liubo $
 * $Id: lib_maifou.php 5885 2009-02-16 05:09:07Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
#require_once ROOT_PATH . 'includes/config.inc.php';

define('HTTP_HOST', get_http_host());

/**
 * 鍒濆?鍖栧弬鏁板嚱鏁
 *
 * @return array
 * array('domain', 'db_name')
 *
 */
function base_init($check_locked='1')
{
    $domain = base_get_domain();

    $db_name = $GLOBALS['db_prefix'].$domain;
    $siteinfo = base_get_siteinfo($domain);
    if($siteinfo == 'is_old')
    {
        maifou_showmsg('瀵逛笉璧凤紝璇ョ嫭绔嬬綉搴楀凡缁忓仠姝㈣惀涓氥€侟br />');
    }
    if($siteinfo == false)
    {
        maifou_showmsg('瀵逛笉璧凤紝璇ョ嫭绔嬬綉搴椾笉瀛樺湪锛岃?纭??缃戝潃杈撳叆鏄?惁姝ｇ‘銆侟br /><a href="http://bbs.wdwd.com/forumdisplay.php?fid=24" title="">鐐规?杩涘叆鏀?寔璁哄潧</a>');
    }
//    if($siteinfo['shop_id']=='156716')
//    {
//         echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
//        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
//        <html xmlns="http://www.w3.org/1999/xhtml">
//        <head>
//        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
//        <title>WDWD&trade; 鎴戠殑缃戝簵--鐙?珛缃戝簵(鍩轰簬鏈€澶х殑寮€婧愬晢鍩庤蒋浠禘CSHOP)</title>
//        <style type="text/css">
//        body{margin:0px; padding:15% 0 0 0; font-size:12px; font-family:"瀹嬩綋"; line-height:20px; background:url(/data/404Bg.gif);
//        font-size:14px; color:#2d2d2d;
//        }
//        div{margin:0 auto; padding:0px;}
//        .conter{width:659px; height:186px; background:url() no-repeat left top;
//        padding:20px 0 0 37px;
//        }
//        .f1{font-size:16px; font-weight:bold; color:#ff0000; margin:17px 0 20px 130px; *margin:38px 0 20px 130px;}
//        .conter a{color:#0b79cd; text-decoration:underline;}
//        </style>
//        </head>
//        <body>
//         <div class="conter">
//          <p class="f1"> </p>
//          瀵逛笉璧凤紝璇ョ綉搴楀崌绾т腑锛屾暚璇锋湡寰匋br />
//         </div>
//        </body>
//        </html>';
//        exit;
//    }


    if($check_locked)
    {
        // 鍒ゆ柇缃戝簵鏄?惁琚?攣瀹
        if (isset($siteinfo['is_locked']) && $siteinfo['is_locked'] == 1)
        {
            header("HTTP/1.0 403 Access forbidden");
            maifou_showmsg('瀵逛笉璧凤紝璇ョ嫭绔嬬綉搴楀凡缁忚?鍏抽棴銆侟br /> 濡傞渶缁х画浣跨敤锛岃?鑱旂郴鎴戜滑銆侟br /> <a href="http://bbs.wdwd.com/forumdisplay.php?fid=24" title="">鐐规?杩涘叆鏀?寔璁哄潧</a>');
        }
    }
    if (isset($siteinfo['is_locked']) && $siteinfo['is_locked'] == 2)
    {
        if($siteinfo['shop_id']>'315768')
        {
            header("HTTP/1.0 403 Access forbidden");
            maifou_showmsg('瀵逛笉璧凤紝鎮ㄦ湭璐?拱鏈嶅姟鎴栬€呮湭婵€娲绘偍鐨勭綉搴椼€侟br /> 璇峰厛璐?拱鏈嶅姟锛岀劧鍚庡湪鐢ㄦ埛涓?績婵€娲绘偍鐨勭綉绔橖br /> <a href="http://www.wdwd.com/user.php?act=login" title="">鐐规?杩涘叆瀹樼綉鐧诲綍</a>');
        }
        else
        {
            header("HTTP/1.0 403 Access forbidden");
            //var_dump($shop_id,$siteinfo);
            maifou_showmsg('瀵逛笉璧凤紝璇ョ綉搴楀凡缁?0澶╂湭鐧诲綍锛屽凡缁忚?绯荤粺鍒犻櫎銆侟br /> 濡傞渶缁х画浣跨敤锛岃?鐧诲綍鍚庡湪鐢ㄦ埛涓?績閲嶆柊婵€娲汇€侟br /> <a href="http://www.wdwd.com/user.php?act=login" title="">鐐规?杩涘叆瀹樼綉鐧诲綍</a>');
        }

    }
    // 鍒ゆ柇缃戝簵鏄?惁杩囨湡

    if ((empty($siteinfo['base_service']) || time() > ($siteinfo['base_service']+86400*7)) && $siteinfo['level_info']['id']>0)
    {
        /* 宸茬粡瓒呰繃缃戝簵杩囨湡鏃堕棿锛屽仠姝㈣?闂?紝鏄剧ず缁?垂椤甸潰 */
        maifou_showmsg('瀵逛笉璧凤紝璇ョ嫭绔嬬綉搴楃殑鏈嶅姟鏈熼檺宸茬粡缁堟?銆侟br /> 濡傞渶缁х画浣跨敤锛岃?鍒扮敤鎴蜂腑蹇冭喘涔版湇鍔?<br /> <a href="http://www.wdwd.com/user.php" title="">鐐规?杩涘叆鐢ㄦ埛涓?績</a>');
    }
    if($siteinfo['level_info']['id'] ==0 && $siteinfo['shop_id']>'153696' && $siteinfo['base_service']<time() )
    {
        maifou_showmsg('瀵逛笉璧凤紝鍏嶈垂鐢ㄦ埛浣跨敤鏈熼檺鏄?竴骞达紝璇ョ嫭绔嬬綉搴楃殑鏈嶅姟鏈熼檺宸茬粡缁堟?銆侟br /> 濡傞渶缁х画浣跨敤锛岃?鍒扮敤鎴蜂腑蹇冭喘涔版湇鍔?<br /> <a href="http://www.wdwd.com/user.php" title="">鐐规?杩涘叆鐢ㄦ埛涓?績</a>');
    }
    // 鍒ゆ柇椤剁骇鍩熷悕鏄?惁鑳借?璁块棶
//    if (empty($siteinfo['top_level_domain']))
//    {
//        maifou_showmsg('瀵逛笉璧凤紝璇ョ嫭绔嬬綉搴楃殑澧炲€兼湇鍔℃湡闄愬凡缁忕粓姝?€侟br /> 濡傞渶缁х画浣跨敤锛岃?鍒扮敤鎴蜂腑蹇冭喘涔版湇鍔?<br /> <a href="http://www.wdwd.com/user.php" title="">鐐规?杩涘叆鐢ㄦ埛涓?績</a>');
//    }
//    tmp_count_pv($siteinfo['shop_id'], $domain);
    return array($domain, $db_name, $siteinfo['shop_id'], $siteinfo['db_host'], $siteinfo);
}

/**
 * 涓存椂缃戝簵PV缁熻?鍑芥暟
*/
function get_count_pv($shop_id)
{
    global $S_CFG;
    $link = @mysql_connect($S_CFG['users_ecshop'][1]['db_host'], $S_CFG['users_ecshop'][1]['db_user'], $S_CFG['users_ecshop'][1]['db_pass']);
    if($link)
    {
        @mysql_select_db('www_maifou_net',$link);
        $flag_re = mysql_query("SELECT lastmonth,lastmonth_pv FROM ecs_shop_pv WHERE shop_id='$shop_id' LIMIT 1");
        $flag = mysql_fetch_row($flag_re);
        $now_month = date('Ym');
        if(empty($flag[0]) || $now_month != $flag[0])
        {
            return 1;
        }
        else
        {
            return $flag[1];
        }
    }
    else
    {
        return 1;
    }
}


/**
 * Client璋冪敤 鍒濆?鍖栧弬鏁板嚱鏁
 *
 * @return array
 * array('domain', 'db_name')
 *
 */
function client_base_init($host)
{
    $result = array('errno' => 0);
    $domain = base_get_domain(true, $host);
    if ($domain === false)
    {
        $result['errno'] = 1; // 鍩熷悕鏈?€氳繃缁戝畾瀹℃牳鎴栧?妗堜俊鎭?笉鍚堟硶
        return $result;
    }
    $siteinfo = base_get_siteinfo($domain);
    if ($siteinfo === false)
    {
        $result['errno'] = 2; // 鏈?幏寰椾换浣曠綉搴椾俊鎭
        return $result;
    }
    // 鍒ゆ柇缃戝簵鏄?惁琚?攣瀹
    if (isset($siteinfo['is_locked']) && $siteinfo['is_locked'] == 1)
    {
        $result['errno'] = 3; // 璇ョ嫭绔嬬綉搴楀凡缁忚?鍏抽棴
        return $result;
    }
    // 鍒ゆ柇缃戝簵鏄?惁杩囨湡
    if (empty($siteinfo['base_service']) || time() > $siteinfo['base_service'])
    {
        $result['errno'] = 4; // 璇ョ嫭绔嬬綉搴楃殑鏈嶅姟鏈熼檺宸茬粡缁堟?
        return $result;
    }
    // 鍒ゆ柇椤剁骇鍩熷悕鏄?惁鑳借?璁块棶
    if ((strpos($host, $GLOBALS['server_domain']) === false) && ( empty($siteinfo['top_level_domain']) || time() > $siteinfo['top_level_domain']))
    {
        $result['errno'] = 5; // 璇ョ嫭绔嬬綉搴楃殑椤剁骇鍩熷悕鏈嶅姟鏈熼檺宸茬粡缁堟?
        return $result;
    }
    $siteinfo['domain'] = $domain;
    $siteinfo['db_name'] = $GLOBALS['db_prefix'].$domain;
    $result['siteinfo'] = $siteinfo;
    return $result;
}

/**
 * 杩斿洖鐢ㄦ埛瀛樺偍璺?緞
 *
 * @param   int $shop_id
 *
 * @return  array
 */
function parse_user_dir($shop_id)
{
    $level_dir[] = ceil($shop_id / 3000);
    $level_dir[] = $shop_id % 3000;
    return $level_dir;
}

/**
 * 鑾峰彇缃戝簵鍩
 *
 * @param boolean $client 鏄?惁瀹㈡埛绔?皟鐢 (榛樿?锛氬惁)
 *
 * @return string 鍩
 */
function base_get_domain($client = false, $host = '')
{
    $http_host = empty($host)? HTTP_HOST : $host;
    $list = get_domain_suffix();
    foreach($list as $suffix)
    {
        if (strpos($http_host, $suffix['suffix_domain']) !== false)
        {
            return $http_host;
            //return str_replace($GLOBALS['server_domain'], '', $http_host);
        }
    }
    $domain = base_get_top_domain($http_host);
    if ($domain === false)
    {
        if($client === true)
        {
             return false;
        }else{
                // 鏈?粦瀹氳?鍩熷悕;
                header("HTTP/1.0 403 Access forbidden");
                maifou_showmsg('瀵逛笉璧凤紝鍩熷悕 ' . $http_host. ' 灏氭湭閫氳繃缁戝畾瀹℃牳鎴栧?妗堜俊鎭?笉鍚堟硶,璇疯仈绯绘垜浠?殑绠＄悊鍛?);
        }
     }else{
            return $domain;
     }
}

/**
 * 鑾峰彇缃戝簵鐨勫熀纭€淇℃伅
 *
 * @param string $domain 鍩
 *
 脳 @return array 鍩虹?淇℃伅鏁版嵁
 */
function base_get_siteinfo($domain)
{
    $key = md5('domain_'.$domain);
    $siteinfo = get_memcache_data($key);
    //$siteinfo = false;
    if ($siteinfo === false || empty($siteinfo))
    {
        $args = array('domain' => $domain);
        $result = remote_procedure_call('manage', 'getinfo', 'getexpire', $args);

        if ((!empty($result) && $result['value'] === true && $result['type'] == 'array') || $result['content'] == 'is_old')
        {
            $siteinfo = $result['content'];
            set_memcache_data($key, json_encode($siteinfo));
        }
        else
        {
            $siteinfo = false;
        }
    }
    else
    {
        $siteinfo = json_decode($siteinfo, 1);
    }
    return $siteinfo;
}
/**
 * 鑾峰彇鍩熷悕鍚庣紑鍒楄〃
 *
 */
function get_domain_suffix()
{
    $key = md5('domain_suffix_list_maifou');
    $list = get_memcache_data($key);
    if($list == false || empty($list))
    {
        $args = array();
        $result = remote_procedure_call('manage', 'getinfo', 'getsuffix', $args);
        if(!empty($result) && $result['value'] === true && $result['type'] == 'string')
        {
            $list = $result['content'];
            set_memcache_data($key, $list);
        }else{
            $list = false;
        }
    }
    return $list;
    //return json_decode($list,1);
}
/**
 * 鑾峰彇鍩烞y椤剁骇鍩熷悕
 *
 * @param string $top_domain 椤剁骇鍩熷悕
 *
 * @return string 鍩
 */
function base_get_top_domain($top_domain)
{
    $key = md5('top_level_domain_'.$top_domain);
    $domain = get_memcache_data($key);
    if ($domain === false || empty($domain))
    {
        $args = array('bound_for' => $top_domain);
        $result = remote_procedure_call('manage', 'getinfo', 'getdomain', $args);
        if (!empty($result) && $result['value'] === true && $result['type'] == 'string')
        {
            $domain = $result['content'];
            set_memcache_data($key, $domain);
        }
        elseif(!empty($result) && $result['value'] === 1 && $result['type'] == 'string')
        {
            $domain = $result['content'];
            header("Location:http://$domain");
            exit();
        }
        else
        {
            $domain = false;
        }
    }
    return $domain;
}

/**
 * 鑾峰彇HTTP_SERVER_NAME
 *
 * @return string 褰撳墠鍩熷悕
 */
function get_http_host()
{
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
    {
        $domain = $_SERVER['HTTP_X_FORWARDED_HOST'];
    }
    elseif (isset($_SERVER['HTTP_HOST']))
    {
        $domain = $_SERVER['HTTP_HOST'];
    }
    elseif (isset($_SERVER['SERVER_NAME']))
    {
        $domain = $_SERVER['SERVER_NAME'];
    }
    else
    {
        $domain = '';
    }
    return $domain;
}

/**
 * 瀛樺偍鍐呭?鍒板唴瀛
 *
 * @author dolphin
 *
 * @param string $key  鍐呭?KEY
 * @param string $data  鍐呭?
 * @param int    $expire  杩囨湡鏃堕棿
 *
 * @return string || FALSE  瀵瑰簲KEY鍐呭? 鎴 甯冨皵鍊?鍋?
 */
function set_memcache_data($key, $data, $expire = 3600)
{
    static $memcache_object;
    if (!is_object($memcache_object) && function_exists('memcache_connect'))
    {
        $memcache_object = @memcache_connect($GLOBALS['memcache_server'], $GLOBALS['memcache_port']);
    }
    if (is_object($memcache_object))
    {
        memcache_set($memcache_object, $key, $data, 0, $expire);
        return true;
    }
    return false;
}

/**
 * 浠庡唴瀛樺彇鍐呭?
 *
 * @author dolphin
 *
 * @param string $key  鍐呭?KEY
 *
 * @return string || FALSE  瀵瑰簲KEY鍐呭? 鎴 甯冨皵鍊?鍋?
 */
function get_memcache_data($key)
{

   static $memcache_object;

    if (!is_object($memcache_object) && function_exists('memcache_connect'))
    {
        $memcache_object = memcache_connect($GLOBALS['memcache_server'], $GLOBALS['memcache_port']);
    }
    if (is_object($memcache_object))
    {
        $data = memcache_get($memcache_object, $key);
        return $data;
    }

    return false;
}

/**
 * 鍒犻櫎鍐呭瓨鍐呭?
 *
 * @author dolphin
 *
 * @param string $key  鍐呭?KEY
 *
 * @return boolean甯冨皵鍊?鐪焲鍋?
 */
function delete_memcache_data($key)
{
    if(function_exists('memcache_connect'))
    {
        $memcache_object1 = @memcache_connect($GLOBALS['memcache_server1'], $GLOBALS['memcache_port']);
        $memcache_object2 = @memcache_connect($GLOBALS['memcache_server2'], $GLOBALS['memcache_port']);
    }
    if(is_object($memcache_object1))
    {
         memcache_delete($memcache_object1, $key);
    }
    if(is_object($memcache_object2))
    {
         memcache_delete($memcache_object2, $key);
    }
    return true;

}

/**
 * 缃戝簵寮傚父鏃舵樉绀轰俊鎭
 *
 * @param string $msg 鏄剧ず淇℃伅
 *
 * @return void
 */
function maifou_showmsg($msg)
{
    $notice_str =
<<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Refresh" content="10; URL=http://www.wdwd.com" />

<title>WDWD&trade; 鎴戠殑缃戝簵--鐙?珛缃戝簵(鍩轰簬鏈€澶х殑寮€婧愬晢鍩庤蒋浠禘CSHOP)</title>
<style type="text/css">
body{margin:0px; padding:15% 0 0 0; font-size:12px; font-family:"瀹嬩綋"; line-height:20px; background:url(/data/404Bg.gif);
font-size:14px; color:#2d2d2d;
}
div{margin:0 auto; padding:0px;}
.conter{width:659px; height:186px; background:url(/data/404Bg1.gif) no-repeat left top;
padding:20px 0 0 37px;
}
.f1{font-size:16px; font-weight:bold; color:#ff0000; margin:17px 0 20px 130px; *margin:38px 0 20px 130px;}
.conter a{color:#0b79cd; text-decoration:underline;}
</style>
</head>
<body>
 <div class="conter">
  <p class="f1"><!-- span id="sec">10绉掑悗</span --> <a href="http://www.wdwd.com">杩斿洖鎴戠殑缃戝簵棣栭〉</a></p>
  @@temp_str@@
  <p>闇€瑕佹洿澶氬府鍔╄?涓庢垜浠?仈绯 <a href="http://bbs.wdwd.com" style="color:#000;">鎴戠殑缃戝簵鏀?寔璁哄潧</a></p>
 </div>
 <script>
/*
var sec = 10;
window.setInterval("if(sec != 0)document.getElementById('sec').innerHTML = --sec;", 1000);
*/
</script>
</body>
</html>
EOT;
    $notice_str = str_replace('@@temp_str@@', $msg, $notice_str);
    exit($notice_str);
}

// ==================== API閫氳?鍩虹?鍑芥暟 ==============================
/**
 * 杩滅▼杩囩▼璋冪敤 (API鐨勪富璋冪敤鍑芥暟)
 *
 * @param string $site    璇锋眰搴旂敤绔欑偣 (bbs | manage | www)
 * @param string $module  妯″潡鍚
 * @param string $action  鍔ㄤ綔
 * @param array  $args    鍙戦€佺殑鏁版嵁
 *
 * @return array
 */
function remote_procedure_call($site, $module, $action, $args = array())
{
//    global $shop_id;
    $s = $sep = '';
    $args['api_key'] = $GLOBALS['api_key'];
    foreach($args as $k => $v)
    {
        if(is_array($v))
        {
            $s2 = $sep2 = '';
            foreach($v as $k2=>$v2)
            {
                $s2 .= "$sep2{$k}[$k2]=".urlencode(api_stripslashes($v2));
                $sep2 = '&';
            }
            $s .= $sep.$s2;
        }
        else
        {
            $s .= "$sep$k=".urlencode(api_stripslashes($v));
        }
        $sep = '&';
    }
    $api_ip = $GLOBALS['api_ip_addr'][$site];

    $postdata = remote_requestdata($module, $action, $s);
//    if($shop_id==33419)
//    {
//         var_dump('http://'.$GLOBALS['api_url'][$site].'/api/api.php?',$postdata);
//    }
      //if($action=='limit_login')
      //{
          //var_dump('http://'.$GLOBALS['api_url'][$site].'/api/api.php?',$postdata);
          //exit;
      //}
    $result = remote_fopen('http://'.$GLOBALS['api_url'][$site].'/api/api.php', 0, $postdata, '', TRUE, $api_ip, 20);
    if (!empty($result))
    {
        $result = json_decode($result, 1);
    }
    return $result;
}
function remote_procedure_call2($site, $module, $action, $args = array())
{
    $s = $sep = '';
    $args['api_key'] = $GLOBALS['api_key'];
    foreach($args as $k => $v)
    {
        if(is_array($v))
        {
            $s2 = $sep2 = '';
            foreach($v as $k2=>$v2)
            {
                $s2 .= "$sep2{$k}[$k2]=".urlencode(api_stripslashes($v2));
                $sep2 = '&';
            }
            $s .= $sep.$s2;
        }
        else
        {
            $s .= "$sep$k=".urlencode(api_stripslashes($v));
        }
        $sep = '&';
    }
    $api_ip = maifou_gethostbyname($GLOBALS['api_url'][$site]);
    $postdata = remote_requestdata($module, $action, $s);//var_dump($postdata);
    $result = remote_fopen('http://'.$GLOBALS['api_url'][$site].'/api/api.php', 0, $postdata, '', TRUE, $api_ip, 20);
    if (!empty($result))
    {
        $result = json_decode($result, 1);
    }
    return $result;
}

/**
 * 瀛楃?涓插姞瀵嗕互鍙婅В瀵嗗嚱鏁
 *
 * @param string $string        鍘熸枃鎴栬€呭瘑鏂
 * @param string $operation     鎿嶄綔(ENCODE | DECODE), 榛樿?涓 DECODE
 * @param string $key           瀵嗛挜
 * @param int $expiry           瀵嗘枃鏈夋晥鏈? 鍔犲瘑鏃跺€欐湁鏁堬紝 鍗曚綅 绉掞紝0 涓烘案涔呮湁鏁
 * @return string               澶勭悊鍚庣殑 鍘熸枃鎴栬€ 缁忚繃 base64_encode 澶勭悊鍚庣殑瀵嗘枃
 *
 * @example
 *
 *      $a = authcode('abc', 'ENCODE', 'key');
 *      $b = authcode($a, 'DECODE', 'key');  // $b(abc)
 *
 *      $a = authcode('abc', 'ENCODE', 'key', 3600);
 *      $b = authcode('abc', 'DECODE', 'key'); // 鍦ㄤ竴涓?皬鏃跺唴锛?b(abc)锛屽惁鍒 $b 涓虹┖
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

        $ckey_length = 4;       //note 闅忔満瀵嗛挜闀垮害 鍙栧€ 0-32;
                                //note 鍔犲叆闅忔満瀵嗛挜锛屽彲浠ヤ护瀵嗘枃鏃犱换浣曡?寰嬶紝鍗充究鏄?師鏂囧拰瀵嗛挜瀹屽叏鐩稿悓锛屽姞瀵嗙粨鏋滀篃浼氭瘡娆′笉鍚岋紝澧炲ぇ鐮磋В闅惧害銆
                                //note 鍙栧€艰秺澶э紝瀵嗘枃鍙樺姩瑙勫緥瓒婂ぇ锛屽瘑鏂囧彉鍖 = 16 鐨 $ckey_length 娆℃柟
                                //note 褰撴?鍊间负 0 鏃讹紝鍒欎笉浜х敓闅忔満瀵嗛挜

        $key = md5($key ? $key : CODE_KEY);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
                $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
                $j = ($j + $box[$i] + $rndkey[$i]) % 256;
                $tmp = $box[$i];
                $box[$i] = $box[$j];
                $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
                $a = ($a + 1) % 256;
                $j = ($j + $box[$a]) % 256;
                $tmp = $box[$a];
                $box[$a] = $box[$j];
                $box[$j] = $tmp;
                $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
                if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                        return substr($result, 26);
                } else {
                        return '';
                }
        } else {
                return $keyc.str_replace('=', '', base64_encode($result));
        }
}

/**
 *  杩滅▼鎵撳紑URL
 *  @param string $url       鎵撳紑鐨剈rl锛屻€€濡 http://www.baidu.com/123.htm
 *  @param int $limit        鍙栬繑鍥炵殑鏁版嵁鐨勯暱搴
 *  @param string $post      瑕佸彂閫佺殑 POST 鏁版嵁锛屽?uid=1&password=1234
 *  @param string $cookie    瑕佹ā鎷熺殑 COOKIE 鏁版嵁锛屽?uid=123&auth=a2323sd2323
 *  @param bool $bysocket    TRUE/FALSE 鏄?惁閫氳繃SOCKET鎵撳紑
 *  @param string $ip        IP鍦板潃
 *  @param int $timeout      杩炴帴瓒呮椂鏃堕棿
 *  @param bool $block       鏄?惁涓洪樆濉炴ā寮
 *  @return                  鍙栧埌鐨勫瓧绗︿覆
 */
function remote_fopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE)
{
//    global $shop_id;
    $return = '';
    $matches = parse_url($url);
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'].'?'.@$matches['query'].(@$matches['fragment'] ? '#'.@$matches['fragment'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : 80;
    if($post)
    {
        $out = "POST $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= 'Content-Length: '.strlen($post)."\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cache-Control: no-cache\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
        $out .= $post;
    }
    else
    {
        $out = "GET $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
    }
    $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);

    if(!$fp)
    {
        return '';//note $errstr : $errno \r\n
    }
    else
    {
        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        $status = stream_get_meta_data($fp);

        if(!$status['timed_out'])
        {
            while (!feof($fp))
            {
                if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n"))
                {
                    break;
                }
            }

            $stop = false;
            while(!feof($fp) && !$stop)
            {
                $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                $return .= $data;
                if($limit)
                {
                    $limit -= strlen($data);
                    $stop = $limit <= 0;
                }
            }
        }

        @fclose($fp);
        return $return;
    }
}

/**
 * 鍙朓P鍦板潃
 *
 * @param string $domain 鍩熷悕
 *
 * @return string IP鍦板潃
 */
function maifou_gethostbyname($domain)
{
    static $iphosts = array();
    if (!isset($iphosts[$domain]))
    {
        $iphosts[$domain] = @gethostbyname($domain);
    }
    return $iphosts[$domain];
}

function remote_requestdata($module, $action, $data)
{
    $_SERVER['HTTP_USER_AGENT'] = empty($_SERVER['HTTP_USER_AGENT']) ? time().date('YddHis') : $_SERVER['HTTP_USER_AGENT'];
    $s = urlencode(authcode($data.'&agent='.md5($_SERVER['HTTP_USER_AGENT'])."&time=".time(), 'ENCODE', CODE_KEY));
    $post = "m=$module&a=$action&input=$s";
    return $post;
}

function api_stripslashes($string) {
    !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    if(MAGIC_QUOTES_GPC) {
        return stripslashes($string);
    } else {
        return $string;
    }
}

/**
 * 浠庡唴瀛樺彇鍟嗘埛鑷?畾涔夋ā鏉垮唴瀹
 *
 * @author dolphin
 *
 * @param int $domain  鍟嗘埛鍩熷悕
 * @param string $theme  妯℃澘椋庢牸
 * @param string $filename  妯℃澘鏂囦欢
 * @param string $type  妯℃澘鍐呭?绫诲瀷
 *
 * @return string   妯℃澘鍐呭?
 */
function get_memcache_custom_template($domain, $theme, $filename, $type)
{


    $hash_key = md5($domain.$theme.$filename.$type);
    $content = get_memcache_data($hash_key);
    if ($content !== false)
    {
        return $content;
    }

    $sql = 'SELECT content, mtime FROM '. $GLOBALS['ecs']->table('tpldata') . ' WHERE ' .
           "theme = '$theme' AND filename = '$filename' AND lang = '".$GLOBALS['_CFG']['lang']."' AND
            type = '$type'";
    $result = $GLOBALS['db']->getRow($sql);
    $content = $result['content'];
    $mtime = $result['mtime'];
    if(!empty($content))
    {
        set_memcache_custom_template($domain, $theme, $filename, $type, $content, $mtime);
        return $content;
    }
    else
    {
        $content = '';
        if($type == 'html')
        {
            $file = ROOT_PATH . 'themes/' . $theme . '/' . $filename . '.dwt';
            $content = file_get_contents($file);
        }
        return $content;
    }
}

/**
 * 璁剧疆鍟嗘埛鑷?畾涔夋ā鏉垮唴瀹
 *
 * @author dolphin
 *
 * @param string $theme  妯℃澘椋庢牸
 * @param string $filename  妯℃澘鏂囦欢
 * @param string $type  妯℃澘鍐呭?绫诲瀷
 * @param string $content   妯℃澘鍐呭?
 *
 * @return boolean
 */
function set_custom_template($theme, $filename, $type, $content, $time)
{
    if( empty($theme) || empty($filename) || empty($type) || empty($content) )
    {
        return false;
    }
    global $db;
    $content = addslashes($content);
    $sql = 'SELECT tpl_id FROM ' . $GLOBALS['ecs']->table('tpldata') . ' WHERE ' .
    "theme = '$theme' AND filename = '$filename' AND lang = '".$GLOBALS['_CFG']['lang']."' AND
            type = '$type'";
    $row = $db->getOne($sql);
    if (!empty($row))
    {
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('tpldata') . " SET content='$content', mtime=$time WHERE " .
        "tpl_id = '$row'";
    }
    else
    {
        $sql = 'INSERT INTO ' . $GLOBALS['ecs']->table('tpldata') . ' (theme, filename, type, content,lang, mtime) ' .
        " VALUES ('$theme', '$filename', '$type', '$content','".$GLOBALS['_CFG']['lang']."', $time)";
    }
    $db->query($sql);

    if($db->affected_rows()>0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * 瀛樺偍鍟嗘埛鑷?畾涔夋ā鏉垮唴瀹瑰埌鍐呭瓨
 *
 * @author dolphin
 *
 * @param int $domain  鍟嗘埛鍩熷悕
 * @param string $theme  妯℃澘椋庢牸
 * @param string $filename  妯℃澘鏂囦欢
 * @param string $type  妯℃澘鍐呭?绫诲瀷
 * @param string $content   妯℃澘鍐呭?
 * @param int $mtime   妯℃澘鏇存柊鏃堕棿
 *
 * @return boolean
 */
function set_memcache_custom_template($domain, $theme, $filename, $type, $content, $mtime)
{
    $hash_key = md5($domain.$theme.$filename.$type);
    $hash_time_key = md5($domain.$theme.$filename.$type.'_time');
    set_memcache_data($hash_key, $content, $expire = 0);
    set_memcache_data($hash_time_key, $mtime, $expire = 0);

    return true;
}


function api_construct()
{
    global $db;
    $module = isset($_REQUEST['m']) ? $_REQUEST['m'] : 'none_app';
    if ($module != 'none_app' && in_array($module, $GLOBALS['api_objs']))
    {
        $module_file = ROOT_PATH . 'api/module/' . $module . '.php';
        if (file_exists($module_file))
        {
            /*鏁版嵁瑙ｆ瀽*/
            $data = authcode($_REQUEST['input'], 'DECODE', CODE_KEY);
            parse_str($data, $data);
            $action = $_REQUEST['a'];
            //寮曠敤module鏂囦欢
            include($module_file);
        }
        else
        {
            show_api_msg(false, 'string', 'api module not exsit');
        }
    }
    else
    {
        show_api_msg(false, 'string', 'api module not in use');
    }
}

/**
 * 鏄剧ずapi鐨勭粨鏋滀俊鎭
 *
 * @param boolean $value 缁撴灉
 * @param string $type 缁撴灉绫诲瀷
 * @param string $msg 淇℃伅
 */
function show_api_msg($result = false, $type = 'string', $msg)
{
    echo json_encode(array('value' => $result, 'type' => $type, 'content' => $msg));

    exit;
}

function api_order($action, $order)
{
    global $db, $shop_id;
    if ($action == 'insert')
    {
        if (!empty($order['order_info']))
        {
            $result = remote_procedure_call('manage', 'order', 'insert', $order);
        }
    }
    elseif ($action == 'remove')
    {
        if (!empty($order['ids']))
        {
            $result = remote_procedure_call('manage', 'order', 'remove', $order);
        }
    }
    elseif ($action == 'edit')
    {
        if (!empty($order['ids']) && !empty($order['order_info']))
        {
            $request['ids'] = $order['ids'];
            $request['shop_id'] = $shop_id;
            $request['order_info'] = $order['order_info'];
            if (isset($order['add_goods']))
            {
                $request['add_goods'] = 'true';
                $request['order_goods'] = $order['order_goods'];
            }
            if (isset($order['remove_goods']))
            {
                $request['remove_goods'] = 'true';
                $request['goods_ids'] = $order['goods_ids'];
            }
            $result = remote_procedure_call('manage', 'order', 'edit', $request);
        }
    }
    elseif ($action == 'merge')
    {
        if (!empty($order['to_order_sn']) && !empty($order['order_info']) && !empty($order['from_order_sn']))
        {
             $to_order_sn = $order['to_order_sn'];
             $order_info = $order['order_info'];
             if (empty($order_info))
             {
                 $get_query  = "SELECT * FROM " . $this->ecs->table('order_info') . " WHERE order_sn='{$to_order_sn}' AND shop_id='{$this->shop_id}'";
                 $order_info = $this->db->getRow($get_query);
             }
             $order['order_info'] = serialize($order_info);
             $request['to_order_sn'] =$order['to_order_sn'];
             $request['from_order_sn'] =$order['from_order_sn'];
             $request['order_info'] =$order['order_info'];
             $request['ids'] =$order['ids'];
             $request['shop_id'] = $shop_id;
             $result = remote_procedure_call('manage', 'order', 'merge', $request);
        }
    }
    elseif ($action == 'insert_goods')
    {
        if (!empty($order['order_goods']))
        {
            $result = remote_procedure_call('manage', 'order', 'insert_goods', $order);
        }
    }
    elseif ($action == 'remove_goods')
    {
        if (!empty($order['goods_ids']))
        {
            $result = remote_procedure_call('manage', 'order', 'remove_goods', $order);
        }
    }
    elseif ($action == 'insert_log')
    {
        $result = remote_procedure_call('manage', 'paylog', 'insert', $order);
    }
    elseif ($action == 'edit_log')
    {
        $result = remote_procedure_call('manage', 'paylog', 'edit', $order);
    }
    return $result;
}

/**
 * 鑾峰彇鐢ㄦ埛鑷?畾涔夎?瑷€椤
 *
 * @author dolphin
 *
 * @return array 璇?█椤规暟缁
 */
function get_user_custom_languages($fn=null)
{
    $domain = $GLOBALS['domain'];
    $theme = $GLOBALS['_CFG']['template'];
    $filenames = array('user', 'common', 'shopping_flow');
    global $_LANG;
    if (!empty($fn) && in_array($fn, $filenames))
    {
        $filenames = array($fn);
    }
    $type = 'lang';

    foreach($filenames as $filename)
    {
        $lang = get_memcache_custom_template($domain, $theme, $filename, $type);
        if(!empty($lang))
        {
            $lang = unserialize($lang);
            foreach ($lang as $key => $val)
            {
                $key = str_replace('$', '', $key);
                eval("\$$key = \"\$val\";");
            }
        }
    }
}

/**
 * 鍚庡彴绠＄悊鍑芥暟 - 妫€鏌ュ晢鍝佹暟閲
 *
 * @param boolean $isajax 鏄?惁Ajax鏂瑰紡璋冪敤
 *
 * @return true | false
 */
function check_goods_amount($isajax = false, $isclient = false)
{
    static $goods_amount = -1;
    if ($goods_amount == -1)
    {
        $goods_amount = $GLOBALS['db']->getOne("SELECT count(*) FROM " . $GLOBALS['ecs']->table('goods') . " WHERE `is_delete`='0'");
    }
    if ((empty($GLOBALS['personal']['goods_amount']) || time() > $GLOBALS['personal']['goods_amount']) && ($goods_amount > $GLOBALS['base_goods_amount']))
    {
        if ($isclient === true)
        {
            return false;
        }
        if ($isajax === false)
        {
            sys_msg(sprintf($GLOBALS['_LANG']['goods_amount_error'], $GLOBALS['base_goods_amount']), 0 , array(), false);
        }
        else
        {
            header("Location: $isajax\n");
            exit;
        }
    }
    return true;
}

/**
 * 鏍规嵁ID鑾峰彇鍖哄煙鍚嶇О
 *
 * @access      public
 * @param       int     region_id   鍖哄煙id
 * @return      array
 */
function get_region_name($region_id = 0, $parent)
{
    if(empty($region_id))
    {
        return '';
    }
    $sql = 'SELECT region_name FROM ' . $GLOBALS['ecs']->table('region') .
            " WHERE region_id = '$region_id' AND parent_id = '$parent'";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 鏍规嵁鍚嶇О鑾峰彇鍖哄煙ID
 *
 * @access      public
 * @param       string     region_name    鍖哄煙鍚嶇О
 * @return      array
 */
function get_region_id($region_name = '', $parent)
{
    if(empty($region_name))
    {
        return '';
    }
    $sql = 'SELECT region_id FROM ' . $GLOBALS['ecs']->table('region') .
            " WHERE region_name = '$region_name' AND parent_id = '$parent'";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 閲嶆柊瑙ｉ噴鍟嗗搧鍥剧墖璺?緞
 *
 * @access     public
 * @param      string       &$goods_img 鍟嗗搧鍥剧墖
 *
 */

function parse_goods_img(&$goods_img)
{
    if (strpos($goods_img, DATA_DIR) !== 0)
    {
        $goods_img = DATA_DIR . '/' . $goods_img;
    }
}

/**
 * 淇冮攢淇℃伅鍒板崠鍚﹂?椤
 * @param   array  $data
 * @return  bool
 */
function sales_promotion($data = array())
{
    if (!empty($data) && $data['title'] != '' && $data['item_type'] != 'bonus' && $GLOBALS['personal']['is_commerce'] == 1)
    {
        $data['domain'] = $GLOBALS['domain'];
        $data['shop_name'] = $GLOBALS['_CFG']['shop_name'];
        $data['img_url'] = empty($data['img_url']) ? '' : $data['img_url'];
        $action = ($data['act_type'] == 'insert') ? ('add_sales_promotion') : ('update_sales_promotion');
        $result = remote_procedure_call('manage', 'shop', $action, $data);
        if($result['value'] == true)
        {
            return true;
        }
        else
        {
            return $result;
        }
    }
    else
    {
        return false;
    }
}

/**
 * 淇冮攢淇℃伅鍒板崠鍚﹂?椤
 * @param   array  $data
 * @return  bool
 */
function del_sales_promotion($item_id, $item_type)
{
    if (!empty($item_id) && $item_type != 'bonus' && $GLOBALS['personal']['is_commerce'] == 1 )
    {
        $data['domain'] = $GLOBALS['domain'];
        $data['item_id'] = $item_id;
        $data['item_type'] = $item_type;
        $result = remote_procedure_call('manage', 'shop', 'del_sales_promotion', $data);
        if($result['value'] == true)
        {
            return true;
        }
        else
        {
            return false;
        }


    }
    else
    {
        return false;
    }
}

/**
 * 鎺ㄨ崘鍒板崠鍚﹂?椤
 * @param   string  $goods_id   鍟嗗搧缂栧彿
 * @return  bool
 */
function add_maifou_special($ids)
{
    if (!empty($ids))
    {

        $sql = 'SELECT  `goods_id`, `goods_name`, `shop_price`, `goods_img`
                FROM ' . $GLOBALS['ecs']->table('goods') . ' WHERE `goods_id` ' . db_create_in($ids);
        $goods_info = $GLOBALS['db']->getAll($sql);
        foreach ($goods_info AS $key => $val)
        {
            $goods_info[$key]['goods_thumb'] = get_image_path($val['goods_id'], '', true);
        }

        $args = array('info_code' => serialize($goods_info), 'domain' => $GLOBALS['domain']);
        $result = remote_procedure_call('manage', 'shop', 'add_special', $args);
        if($result['value'] == true)
        {
            return true;
        }
        else
        {
            return false;
        }


    }
    else
    {
        return false;
    }
}

/**
 * 鎺ㄨ崘鍒板崠鍚﹂?椤
 * @return   string  $goods_id   鍟嗗搧缂栧彿锛屽彲浠ヤ负澶氫釜锛岀敤 ',' 闅斿紑
 */
function get_maifou_special_list()
{
    $args = array('domain' => $GLOBALS['domain']);
    $result = remote_procedure_call('manage', 'shop', 'get_special_list', $args);
    if ($result['value'] == true)
    {
        if (is_array($result['content']))
        {
            foreach ($result['content'] AS $v)
            {
                $goods_id[] .= $v['goods_id'];
            }
        }

        return implode(',', $goods_id);
    }

    return false;


}

/**
 * 鏇存敼宸叉帹鑽愬埌鍗栧惁'搴椾富鎺ㄨ崘鍟嗗搧'鐨勫晢鍝佷俊鎭
 * @param   string  $goods_id   鍟嗗搧缂栧彿
 * @return  void
 */
function edit_maifou_special($goods_id)
{
    if (!empty($goods_id)  && $GLOBALS['personal']['is_commerce'] == 1)
    {
        $goods_arr = explode(',', get_maifou_special_list());
        if (in_array($goods_id, $goods_arr))
        {
            $sql = 'SELECT  `goods_id`, `goods_name`, `shop_price`, `goods_img`
                    FROM ' . $GLOBALS['ecs']->table('goods') . ' WHERE `goods_id` = \'' . $goods_id . '\'';
            $goods = $GLOBALS['db']->getRow($sql);

            if (!empty($goods))
            {
                $goods['domain'] = $GLOBALS['domain'];
                $goods['goods_thumb'] = get_image_path($goods['goods_id'], '', true);
                $result = remote_procedure_call('manage', 'shop', 'edit_special', $goods);
            }
        }
    }
}

/**
 * 鍙栨秷鎺ㄨ崘鍒板崠鍚﹂?椤
 * @param   string  $goods_id   鍟嗗搧缂栧彿锛屽彲浠ヤ负澶氫釜锛岀敤 ',' 闅斿紑
 * @return  bool
 */
function del_maifou_special($goods_id)
{
    if (!empty($goods_id))
    {
        $args = array('goods_id' => $goods_id, 'domain' => $GLOBALS['domain']);
        $result = remote_procedure_call('manage', 'shop', 'del_special', $args);

        if($result['value'] == true)
        {
            return true;
        }
        else
        {
            return false;
        }


    }
    else
    {
        return false;
    }
}
/**
 * 鏇存柊鍟嗗簵鍚嶇О
 * @param   string  $goods_id   鍟嗗搧缂栧彿锛屽彲浠ヤ负澶氫釜锛岀敤 ',' 闅斿紑
 * @return  bool
 */
function update_shop_name($shop_name)
{
    global $shop_id;
    if (!empty($shop_name))
    {
        $args = array('shop_id' => $shop_id, 'shop_name' => $shop_name);
        $result = remote_procedure_call('manage', 'getinfo', 'update_shop_name', $args);

        if($result['value'] == true)
        {
            return true;
        }
        else
        {
            return false;
        }


    }
    else
    {
        return false;
    }
}
//鍏抽敭瀛楄繃婊
function check_censor()
{
    if(empty($_REQUEST))
    {
        return false;
    }
    $key = md5('check_word_list');
    $check_word_list = get_memcache_data($key);
    if ($check_word_list === false || empty($check_word_list))
    {
        $result = remote_procedure_call('manage', 'getinfo', 'get_check_word', array());
        if (!empty($result) && $result['value'] === true && $result['type'] == 'string')
        {
            $check_word_list = str_replace('/','\/',$result['content']);
            set_memcache_data($key, $check_word_list, '14400');//缂撳瓨4灏忔椂
        }
        else
        {
            return false;
        }
    }
    $check_str = null;
    foreach($_REQUEST as $v)
    {
        if(is_numeric($v) || empty($v))
        {
            continue;
        }
        if(is_array($v))
        {
            $check_str .= var_export($v,true) . ' ';
        }else{
            $check_str .= $v . ' ';
        }
    }
    if(preg_match("/($check_word_list)/i",$check_str))
    {
       preg_match_all("/($check_word_list)/i",$check_str,$check_words);
      $check_words =array_unique($check_words['1']);
      $check_words=implode('銆?,$check_words);
      return $check_words;
    }
}

/**
 * 璁惧畾鐢ㄦ埛鏂囦欢鐩?綍鍦板潃
 * @param   int     $shop_id   缃戝簵ID
 * @param   string  $domain    缃戝簵鍩熷悕
 * @return  array('涓€绾х洰褰?,'浜岀骇鐩?綍')
 */
function parse_dir($shop_id,$domain)
{
    if ($shop_id>34627)
     {
         $shop_id = $shop_id - 34627;//鍑忔帀鍩烘暟34627
         $dir_arr[] = ceil($shop_id / 22000);
         $dir_arr[] = $shop_id % 22000;
         define('USER_PATH', 'user_files/'.$dir_arr[0].'/' . $domain . '/');
     }
    else
     {
         $dir_arr[] = 0;
         $dir_arr[] = $shop_id;
         define('USER_PATH', 'user_files/' . $domain . '/');
     }
     return $dir_arr;
}


//鑾峰彇鍥㈣喘鍟嗗搧鍒楄〃
function get_groupon_list()
{
    $get_date = date('Y-m-d');
    $get_time = strtotime($get_date);
    $key=md5('get_groupon_list'.$get_date);
    $result = get_memcache_data($key);
    if($result == false || empty($result))
    {
        $groupon = remote_procedure_call('manage', 'groupon', 'get_all_list', array('shop_id' => $GLOBALS['shop_id'],'get_time' => $get_time));
        if ($groupon['value']==true && $groupon['type']=='array')
        {
            $out = $groupon['content'];
        }
        set_memcache_data($key,serialize($out),$out['expire']);
    }
    else
    {
        $out = unserialize($result);
    }
    return $out;
}


?>