<?php

/**
 * ECSHOP 鍩虹?鍑芥暟搴
 * ============================================================================
 * 鐗堟潈鎵€鏈 2005-2008 涓婃捣鍟嗘淳缃戠粶绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: sxc_shop $
 * $Id: lib_base.php 6108 2009-09-17 07:40:49Z xiaoxinxin $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 鎴?彇UTF-8缂栫爜涓嬪瓧绗︿覆鐨勫嚱鏁
 *
 * @param   string      $str        琚?埅鍙栫殑瀛楃?涓
 * @param   int         $length     鎴?彇鐨勯暱搴
 * @param   bool        $append     鏄?惁闄勫姞鐪佺暐鍙
 *
 * @return  string
 */
function sub_str($str, $length = 0, $append = true)
{
    $str = trim($str);
    $strlength = strlen($str);

    if ($length == 0 || $length >= $strlength)
    {
        return $str;
    }
    elseif ($length < 0)
    {
        $length = $strlength + $length;
        if ($length < 0)
        {
            $length = $strlength;
        }
    }

    if (function_exists('mb_substr'))
    {
        $newstr = mb_substr($str, 0, $length, EC_CHARSET);
    }
    elseif (function_exists('iconv_substr'))
    {
        $newstr = iconv_substr($str, 0, $length, EC_CHARSET);
    }
    else
    {
        //$newstr = trim_right(substr($str, 0, $length));
        $newstr = substr($str, 0, $length);
    }

    if ($append && $str != $newstr)
    {
        $newstr .= '...';
    }

    return $newstr;
}

/**
 * 鑾峰緱鐢ㄦ埛鐨勭湡瀹濱P鍦板潃
 *
 * @access  public
 * @return  string
 */
function real_ip()
{
    static $realip = NULL;

    if ($realip !== NULL)
    {
        return $realip;
    }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            /* 鍙朮-Forwarded-For涓??涓€涓?潪unknown鐨勬湁鏁圛P瀛楃?涓 */
            foreach ($arr AS $ip)
            {
                $ip = trim($ip);

                if ($ip != 'unknown')
                {
                    $realip = $ip;

                    break;
                }
            }
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            if (isset($_SERVER['REMOTE_ADDR']))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $realip = '0.0.0.0';
            }
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_CLIENT_IP'))
        {
            $realip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $realip = getenv('REMOTE_ADDR');
        }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
}

/**
 * 璁＄畻瀛楃?涓茬殑闀垮害锛堟眽瀛楁寜鐓т袱涓?瓧绗﹁?绠楋級
 *
 * @param   string      $str        瀛楃?涓
 *
 * @return  int
 */
function str_len($str)
{
    $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));

    if ($length)
    {
        return strlen($str) - $length + intval($length / 3) * 2;
    }
    else
    {
        return strlen($str);
    }
}

/**
 * 鑾峰緱鐢ㄦ埛鎿嶄綔绯荤粺鐨勬崲琛岀?
 *
 * @access  public
 * @return  string
 */
function get_crlf()
{
/* LF (Line Feed, 0x0A, \N) 鍜 CR(Carriage Return, 0x0D, \R) */
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Win'))
    {
        $the_crlf = '\r\n';
    }
    elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Mac'))
    {
        $the_crlf = '\r'; // for old MAC OS
    }
    else
    {
        $the_crlf = '\n';
    }

    return $the_crlf;
}

/**
 * 閭?欢鍙戦€
 *
 * @param: $name[string]        鎺ユ敹浜哄?鍚
 * @param: $email[string]       鎺ユ敹浜洪偖浠跺湴鍧€
 * @param: $subject[string]     閭?欢鏍囬?
 * @param: $content[string]     閭?欢鍐呭?
 * @param: $type[int]           0 鏅?€氶偖浠讹紝 1 HTML閭?欢
 * @param: $notification[bool]  true 瑕佹眰鍥炴墽锛 false 涓嶇敤鍥炴墽
 *
 * @return boolean
 */
function send_mail($name, $email, $subject, $content, $type = 0, $notification=false)
{
    /* 濡傛灉閭?欢缂栫爜涓嶆槸EC_CHARSET锛屽垱寤哄瓧绗﹂泦杞?崲瀵硅薄锛岃浆鎹㈢紪鐮 */
    if ($GLOBALS['_CFG']['mail_charset'] != EC_CHARSET)
    {
        $name      = ecs_iconv(EC_CHARSET, $GLOBALS['_CFG']['mail_charset'], $name);
        $subject   = ecs_iconv(EC_CHARSET, $GLOBALS['_CFG']['mail_charset'], $subject);
        $content   = ecs_iconv(EC_CHARSET, $GLOBALS['_CFG']['mail_charset'], $content);
        $GLOBALS['_CFG']['shop_name'] = ecs_iconv(EC_CHARSET, $GLOBALS['_CFG']['mail_charset'], $GLOBALS['_CFG']['shop_name']);
    }
    $charset   = $GLOBALS['_CFG']['mail_charset'];
    /**
     * 浣跨敤mail鍑芥暟鍙戦€侀偖浠
     */
    if ($GLOBALS['_CFG']['mail_service'] == 0 && function_exists('mail'))
    {
        /* 閭?欢鐨勫ご閮ㄤ俊鎭 */
        $content_type = ($type == 0) ? 'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
        $headers = array();
        $headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($GLOBALS['_CFG']['shop_name']) . '?='.'" <' . $GLOBALS['_CFG']['smtp_mail'] . '>';
        $headers[] = $content_type . '; format=flowed';
        if ($notification)
        {
            $headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($GLOBALS['_CFG']['shop_name']) . '?='.'" <' . $GLOBALS['_CFG']['smtp_mail'] . '>';
        }

        $res = @mail($email, '=?' . $charset . '?B?' . base64_encode($subject) . '?=', $content, implode("\r\n", $headers));

        if (!$res)
        {
            $GLOBALS['err'] ->add($GLOBALS['_LANG']['sendemail_false']);

            return false;
        }
        else
        {
            return true;
        }
    }
    /**
     * 浣跨敤smtp鏈嶅姟鍙戦€侀偖浠
     */
    else
    {
        /* 閭?欢鐨勫ご閮ㄤ俊鎭 */
        $content_type = ($type == 0) ?
            'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
        $content   =  base64_encode($content);

        $headers = array();
        $headers[] = 'Date: ' . gmdate('D, j M Y H:i:s') . ' +0000';
        $headers[] = 'To: "' . '=?' . $charset . '?B?' . base64_encode($name) . '?=' . '" <' . $email. '>';
        $headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($GLOBALS['_CFG']['shop_name']) . '?='.'" <' . $GLOBALS['_CFG']['smtp_mail'] . '>';
        $headers[] = 'Subject: ' . '=?' . $charset . '?B?' . base64_encode($subject) . '?=';
        $headers[] = $content_type . '; format=flowed';
        $headers[] = 'Content-Transfer-Encoding: base64';
        $headers[] = 'Content-Disposition: inline';
        if ($notification)
        {
            $headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($GLOBALS['_CFG']['shop_name']) . '?='.'" <' . $GLOBALS['_CFG']['smtp_mail'] . '>';
        }

        /* 鑾峰緱閭?欢鏈嶅姟鍣ㄧ殑鍙傛暟璁剧疆 */
        $params['host'] = $GLOBALS['_CFG']['smtp_host'];
        $params['port'] = $GLOBALS['_CFG']['smtp_port'];
        $params['user'] = $GLOBALS['_CFG']['smtp_user'];
        $params['pass'] = $GLOBALS['_CFG']['smtp_pass'];

        if (empty($params['host']) || empty($params['port']))
        {
            // 濡傛灉娌℃湁璁剧疆涓绘満鍜岀?鍙ｇ洿鎺ヨ繑鍥 false
            $GLOBALS['err'] ->add($GLOBALS['_LANG']['smtp_setting_error']);

            return false;
        }
        else
        {
            // 鍙戦€侀偖浠
            if (!function_exists('fsockopen'))
            {
                //濡傛灉fsockopen琚??鐢?紝鐩存帴杩斿洖
                $GLOBALS['err']->add($GLOBALS['_LANG']['disabled_fsockopen']);

                return false;
            }

            include_once(ROOT_PATH . 'includes/cls_smtp.php');
            static $smtp;

            $send_params['recipients'] = $email;
            $send_params['headers']    = $headers;
            $send_params['from']       = $GLOBALS['_CFG']['smtp_mail'];
            $send_params['body']       = $content;

            if (!isset($smtp))
            {
                $smtp = new smtp($params);
            }

            if ($smtp->connect() && $smtp->send($send_params))
            {
                return true;
            }
            else
            {
                $err_msg = $smtp->error_msg();
                if (empty($err_msg))
                {
                    $GLOBALS['err']->add('Unknown Error');
                }
                else
                {
                    if (strpos($err_msg, 'Failed to connect to server') !== false)
                    {
                        $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['smtp_connect_failure'], $params['host'] . ':' . $params['port']));
                    }
                    else if (strpos($err_msg, 'AUTH command failed') !== false)
                    {
                        $GLOBALS['err']->add($GLOBALS['_LANG']['smtp_login_failure']);
                    }
                    elseif (strpos($err_msg, 'bad sequence of commands') !== false)
                    {
                        $GLOBALS['err']->add($GLOBALS['_LANG']['smtp_refuse']);
                    }
                    else
                    {
                        $GLOBALS['err']->add($err_msg);
                    }
                }

                return false;
            }
        }
    }
}

/**
 * 鑾峰緱鏈嶅姟鍣ㄤ笂鐨 GD 鐗堟湰
 *
 * @access      public
 * @return      int         鍙?兘鐨勫€间负0锛?锛?
 */
function gd_version()
{
    include_once(ROOT_PATH . 'includes/cls_image.php');

    return cls_image::gd_version();
}

if (!function_exists('file_get_contents'))
{
    /**
     * 濡傛灉绯荤粺涓嶅瓨鍦╢ile_get_contents鍑芥暟鍒欏０鏄庤?鍑芥暟
     *
     * @access  public
     * @param   string  $file
     * @return  mix
     */
    function file_get_contents($file)
    {
        if (($fp = @fopen($file, 'rb')) === false)
        {
            return false;
        }
        else
        {
            $fsize = @filesize($file);
            if ($fsize)
            {
                $contents = fread($fp, $fsize);
            }
            else
            {
                $contents = '';
            }
            fclose($fp);

            return $contents;
        }
    }
}

if (!function_exists('file_put_contents'))
{
    define('FILE_APPEND', 'FILE_APPEND');

    /**
     * 濡傛灉绯荤粺涓嶅瓨鍦╢ile_put_contents鍑芥暟鍒欏０鏄庤?鍑芥暟
     *
     * @access  public
     * @param   string  $file
     * @param   mix     $data
     * @return  int
     */
    function file_put_contents($file, $data, $flags = '')
    {
        $contents = (is_array($data)) ? implode('', $data) : $data;

        if ($flags == 'FILE_APPEND')
        {
            $mode = 'ab+';
        }
        else
        {
            $mode = 'wb';
        }

        if (($fp = @fopen($file, $mode)) === false)
        {
            return false;
        }
        else
        {
            $bytes = fwrite($fp, $contents);
            fclose($fp);

            return $bytes;
        }
    }
}

if (!function_exists('floatval'))
{
    /**
     * 濡傛灉绯荤粺涓嶅瓨鍦 floatval 鍑芥暟鍒欏０鏄庤?鍑芥暟
     *
     * @access  public
     * @param   mix     $n
     * @return  float
     */
    function floatval($n)
    {
        return (float) $n;
    }
}

/**
 * 鏂囦欢鎴栫洰褰曟潈闄愭?鏌ュ嚱鏁
 *
 * @access          public
 * @param           string  $file_path   鏂囦欢璺?緞
 * @param           bool    $rename_prv  鏄?惁鍦ㄦ?鏌ヤ慨鏀规潈闄愭椂妫€鏌ユ墽琛宺ename()鍑芥暟鐨勬潈闄
 *
 * @return          int     杩斿洖鍊肩殑鍙栧€艰寖鍥翠负{0 <= x <= 15}锛屾瘡涓?€艰〃绀虹殑鍚?箟鍙?敱鍥涗綅浜岃繘鍒舵暟缁勫悎鎺ㄥ嚭銆
 *                          杩斿洖鍊煎湪浜岃繘鍒惰?鏁版硶涓?紝鍥涗綅鐢遍珮鍒颁綆鍒嗗埆浠ｈ〃
 *                          鍙?墽琛宺ename()鍑芥暟鏉冮檺銆佸彲瀵规枃浠惰拷鍔犲唴瀹规潈闄愩€佸彲鍐欏叆鏂囦欢鏉冮檺銆佸彲璇诲彇鏂囦欢鏉冮檺銆
 */
function file_mode_info($file_path)
{
    /* 濡傛灉涓嶅瓨鍦?紝鍒欎笉鍙??銆佷笉鍙?啓銆佷笉鍙?敼 */
    if (!file_exists($file_path))
    {
        return false;
    }

    $mark = 0;

    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
    {
        /* 娴嬭瘯鏂囦欢 */
        $test_file = $file_path . '/cf_test.txt';

        /* 濡傛灉鏄?洰褰 */
        if (is_dir($file_path))
        {
            /* 妫€鏌ョ洰褰曟槸鍚﹀彲璇 */
            $dir = @opendir($file_path);
            if ($dir === false)
            {
                return $mark; //濡傛灉鐩?綍鎵撳紑澶辫触锛岀洿鎺ヨ繑鍥炵洰褰曚笉鍙?慨鏀广€佷笉鍙?啓銆佷笉鍙??
            }
            if (@readdir($dir) !== false)
            {
                $mark ^= 1; //鐩?綍鍙?? 001锛岀洰褰曚笉鍙?? 000
            }
            @closedir($dir);

            /* 妫€鏌ョ洰褰曟槸鍚﹀彲鍐 */
            $fp = @fopen($test_file, 'wb');
            if ($fp === false)
            {
                return $mark; //濡傛灉鐩?綍涓?殑鏂囦欢鍒涘缓澶辫触锛岃繑鍥炰笉鍙?啓銆
            }
            if (@fwrite($fp, 'directory access testing.') !== false)
            {
                $mark ^= 2; //鐩?綍鍙?啓鍙??011锛岀洰褰曞彲鍐欎笉鍙?? 010
            }
            @fclose($fp);

            @unlink($test_file);

            /* 妫€鏌ョ洰褰曟槸鍚﹀彲淇?敼 */
            $fp = @fopen($test_file, 'ab+');
            if ($fp === false)
            {
                return $mark;
            }
            if (@fwrite($fp, "modify test.\r\n") !== false)
            {
                $mark ^= 4;
            }
            @fclose($fp);

            /* 妫€鏌ョ洰褰曚笅鏄?惁鏈夋墽琛宺ename()鍑芥暟鐨勬潈闄 */
            if (@rename($test_file, $test_file) !== false)
            {
                $mark ^= 8;
            }
            @unlink($test_file);
        }
        /* 濡傛灉鏄?枃浠 */
        elseif (is_file($file_path))
        {
            /* 浠ヨ?鏂瑰紡鎵撳紑 */
            $fp = @fopen($file_path, 'rb');
            if ($fp)
            {
                $mark ^= 1; //鍙?? 001
            }
            @fclose($fp);

            /* 璇曠潃淇?敼鏂囦欢 */
            $fp = @fopen($file_path, 'ab+');
            if ($fp && @fwrite($fp, '') !== false)
            {
                $mark ^= 6; //鍙?慨鏀瑰彲鍐欏彲璇 111锛屼笉鍙?慨鏀瑰彲鍐欏彲璇?11...
            }
            @fclose($fp);

            /* 妫€鏌ョ洰褰曚笅鏄?惁鏈夋墽琛宺ename()鍑芥暟鐨勬潈闄 */
            if (@rename($test_file, $test_file) !== false)
            {
                $mark ^= 8;
            }
        }
    }
    else
    {
        if (@is_readable($file_path))
        {
            $mark ^= 1;
        }

        if (@is_writable($file_path))
        {
            $mark ^= 14;
        }
    }

    return $mark;
}

function log_write($arg, $file = '', $line = '')
{
    if ((DEBUG_MODE & 4) != 4)
    {
        return;
    }

    $str = "\r\n-- ". date('Y-m-d H:i:s'). " --------------------------------------------------------------\r\n";
    $str .= "FILE: $file\r\nLINE: $line\r\n";

    if (is_array($arg))
    {
        $str .= '$arg = array(';
        foreach ($arg AS $val)
        {
            foreach ($val AS $key => $list)
            {
                $str .= "'$key' => '$list'\r\n";
            }
        }
        $str .= ")\r\n";
    }
    else
    {
        $str .= $arg;
    }

    file_put_contents(ROOT_PATH . DATA_DIR . '/log.txt', $str);
}

/**
 * 妫€鏌ョ洰鏍囨枃浠跺す鏄?惁瀛樺湪锛屽?鏋滀笉瀛樺湪鍒欒嚜鍔ㄥ垱寤鸿?鐩?綍
 *
 * @access      public
 * @param       string      folder     鐩?綍璺?緞銆備笉鑳戒娇鐢ㄧ浉瀵逛簬缃戠珯鏍圭洰褰曠殑URL
 *
 * @return      bool
 */
function make_dir($folder)
{
    $reval = false;

    if (!file_exists($folder))
    {
        /* 濡傛灉鐩?綍涓嶅瓨鍦ㄥ垯灏濊瘯鍒涘缓璇ョ洰褰 */
        @umask(0);

        /* 灏嗙洰褰曡矾寰勬媶鍒嗘垚鏁扮粍 */
        preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

        /* 濡傛灉绗?竴涓?瓧绗︿负/鍒欏綋浣滅墿鐞嗚矾寰勫?鐞 */
        $base = ($atmp[0][0] == '/') ? '/' : '';

        /* 閬嶅巻鍖呭惈璺?緞淇℃伅鐨勬暟缁 */
        foreach ($atmp[1] AS $val)
        {
            if ('' != $val)
            {
                $base .= $val;

                if ('..' == $val || '.' == $val)
                {
                    /* 濡傛灉鐩?綍涓?鎴栬€?.鍒欑洿鎺ヨˉ/缁х画涓嬩竴涓?惊鐜 */
                    $base .= '/';

                    continue;
                }
            }
            else
            {
                continue;
            }

            $base .= '/';

            if (!file_exists($base))
            {
                /* 灏濊瘯鍒涘缓鐩?綍锛屽?鏋滃垱寤哄け璐ュ垯缁х画寰?幆 */
                if (@mkdir(rtrim($base, '/'), 0777))
                {
                    @chmod($base, 0777);
                    $reval = true;
                }
            }
        }
    }
    else
    {
        /* 璺?緞宸茬粡瀛樺湪銆傝繑鍥炶?璺?緞鏄?笉鏄?竴涓?洰褰 */
        $reval = is_dir($folder);
    }

    clearstatcache();

    return $reval;
}

/**
 * 鑾峰緱绯荤粺鏄?惁鍚?敤浜 gzip
 *
 * @access  public
 *
 * @return  boolean
 */
function gzip_enabled()
{
    static $enabled_gzip = NULL;

    if ($enabled_gzip === NULL)
    {
        $enabled_gzip = ($GLOBALS['_CFG']['enable_gzip'] && function_exists('ob_gzhandler'));
    }

    return $enabled_gzip;
}

/**
 * 閫掑綊鏂瑰紡鐨勫?鍙橀噺涓?殑鐗规畩瀛楃?杩涜?杞?箟
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function addslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
    }
}

/**
 * 灏嗗?璞℃垚鍛樺彉閲忔垨鑰呮暟缁勭殑鐗规畩瀛楃?杩涜?杞?箟
 *
 * @access   public
 * @param    mix        $obj      瀵硅薄鎴栬€呮暟缁
 * @author   Xuan Yan
 *
 * @return   mix                  瀵硅薄鎴栬€呮暟缁
 */
function addslashes_deep_obj($obj)
{
    if (is_object($obj) == true)
    {
        foreach ($obj AS $key => $val)
        {
            $obj->$key = addslashes_deep($val);
        }
    }
    else
    {
        $obj = addslashes_deep($obj);
    }

    return $obj;
}

/**
 * 閫掑綊鏂瑰紡鐨勫?鍙橀噺涓?殑鐗规畩瀛楃?鍘婚櫎杞?箟
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function stripslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    }
}

/**
 *  灏嗕竴涓?瓧涓蹭腑鍚?湁鍏ㄨ?鐨勬暟瀛楀瓧绗︺€佸瓧姣嶃€佺┖鏍兼垨'%+-()'瀛楃?杞?崲涓虹浉搴斿崐瑙掑瓧绗
 *
 * @access  public
 * @param   string       $str         寰呰浆鎹㈠瓧涓
 *
 * @return  string       $str         澶勭悊鍚庡瓧涓
 */
function make_semiangle($str)
{
    $arr = array('锛? => '0', '锛? => '1', '锛? => '2', '锛? => '3', '锛? => '4',
                 '锛? => '5', '锛? => '6', '锛? => '7', '锛? => '8', '锛? => '9',
                 '锛? => 'A', '锛? => 'B', '锛? => 'C', '锛? => 'D', '锛? => 'E',
                 '锛? => 'F', '锛? => 'G', '锛? => 'H', '锛? => 'I', '锛? => 'J',
                 '锛? => 'K', '锛? => 'L', '锛? => 'M', '锛? => 'N', '锛? => 'O',
                 '锛? => 'P', '锛? => 'Q', '锛? => 'R', '锛? => 'S', '锛? => 'T',
                 '锛? => 'U', '锛? => 'V', '锛? => 'W', '锛? => 'X', '锛? => 'Y',
                 '锛? => 'Z', '锝? => 'a', '锝? => 'b', '锝? => 'c', '锝? => 'd',
                 '锝? => 'e', '锝? => 'f', '锝? => 'g', '锝? => 'h', '锝? => 'i',
                 '锝? => 'j', '锝? => 'k', '锝? => 'l', '锝? => 'm', '锝? => 'n',
                 '锝? => 'o', '锝? => 'p', '锝? => 'q', '锝? => 'r', '锝? => 's',
                 '锝? => 't', '锝? => 'u', '锝? => 'v', '锝? => 'w', '锝? => 'x',
                 '锝? => 'y', '锝? => 'z',
                 '锛? => '(', '锛? => ')', '銆? => '[', '銆? => ']', '銆? => '[',
                 '銆? => ']', '銆? => '[', '銆? => ']', '鈥? => '[', '鈥? => ']',
                 '鈥? => '[', '鈥? => ']', '锝? => '{', '锝? => '}', '銆? => '<',
                 '銆? => '>',
                 '锛? => '%', '锛? => '+', '鈥? => '-', '锛? => '-', '锝? => '-',
                 '锛? => ':', '銆? => '.', '銆? => ',', '锛? => '.', '銆? => '.',
                 '锛? => ',', '锛? => '?', '锛? => '!', '鈥? => '-', '鈥? => '|',
                 '鈥? => '"', '鈥? => '`', '鈥? => '`', '锝? => '|', '銆? => '"',
                 '銆€' => ' ');

    return strtr($str, $arr);
}
/**
 * 杩囨护鐢ㄦ埛杈撳叆鐨勫熀鏈?暟鎹?紝闃叉?script鏀诲嚮
 *
 * @access      public
 * @return      string
 */
function compile_str($str)
{
    $arr = array('<' => '锛?, '>' => '锛?);

    return strtr($str, $arr);
}

/**
 * 妫€鏌ユ枃浠剁被鍨
 *
 * @access      public
 * @param       string      filename            鏂囦欢鍚
 * @param       string      realname            鐪熷疄鏂囦欢鍚
 * @param       string      limit_ext_types     鍏佽?鐨勬枃浠剁被鍨
 * @return      string
 */
function check_file_type($filename, $realname = '', $limit_ext_types = '')
{
    if ($realname)
    {
        $extname = strtolower(substr($realname, strrpos($realname, '.') + 1));
    }
    else
    {
        $extname = strtolower(substr($filename, strrpos($filename, '.') + 1));
    }

    if ($limit_ext_types && stristr($limit_ext_types, '|' . $extname . '|') === false)
    {
        return '';
    }

    $str = $format = '';

    $file = @fopen($filename, 'rb');
    if ($file)
    {
        $str = @fread($file, 0x400); // 璇诲彇鍓 1024 涓?瓧鑺
        @fclose($file);
    }
    else
    {
        if (stristr($filename, ROOT_PATH) === false)
        {
            if ($extname == 'jpg' || $extname == 'jpeg' || $extname == 'gif' || $extname == 'png' || $extname == 'doc' ||
                $extname == 'xls' || $extname == 'txt'  || $extname == 'zip' || $extname == 'rar' || $extname == 'ppt' ||
                $extname == 'pdf' || $extname == 'rm'   || $extname == 'mid' || $extname == 'wav' || $extname == 'bmp' ||
                $extname == 'swf' || $extname == 'chm'  || $extname == 'sql' || $extname == 'cert'|| $extname == 'docx' || 
                $extname == 'pptx'|| $extname == 'xlsx')
            {
                $format = $extname;
            }
        }
        else
        {
            return '';
        }
    }

    if ($format == '' && strlen($str) >= 2 )
    {
        if (substr($str, 0, 4) == 'MThd' && $extname != 'txt')
        {
            $format = 'mid';
        }
        elseif (substr($str, 0, 4) == 'RIFF' && $extname == 'wav')
        {
            $format = 'wav';
        }
        elseif (substr($str ,0, 3) == "\xFF\xD8\xFF")
        {
            $format = 'jpg';
        }
        elseif (substr($str ,0, 4) == 'GIF8' && $extname != 'txt')
        {
            $format = 'gif';
        }
        elseif (substr($str ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A")
        {
            $format = 'png';
        }
        elseif (substr($str ,0, 2) == 'BM' && $extname != 'txt')
        {
            $format = 'bmp';
        }
        elseif ((substr($str ,0, 3) == 'CWS' || substr($str ,0, 3) == 'FWS') && $extname != 'txt')
        {
            $format = 'swf';
        }
        elseif (substr($str ,0, 4) == "\xD0\xCF\x11\xE0")
        {   // D0CF11E == DOCFILE == Microsoft Office Document
            if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'doc')
            {
                $format = 'doc';
            }
            elseif (substr($str,0x200,2) == "\x09\x08" || $extname == 'xls')
            {
                $format = 'xls';
            } elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'ppt')
            {
                $format = 'ppt';
            }
        } 
        elseif (substr($str ,0, 4) == "PK\x03\x04")
        {
            if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'docx')
            {
                $format = 'docx';
            }
            elseif (substr($str,0x200,2) == "\x09\x08" || $extname == 'xlsx')
            {
                $format = 'xlsx';
            } elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'pptx')
            {
                $format = 'pptx';
            }else
            {
                $format = 'zip';
            }
        } elseif (substr($str ,0, 4) == 'Rar!' && $extname != 'txt')
        {
            $format = 'rar';
        } elseif (substr($str ,0, 4) == "\x25PDF")
        {
            $format = 'pdf';
        } elseif (substr($str ,0, 3) == "\x30\x82\x0A")
        {
            $format = 'cert';
        } elseif (substr($str ,0, 4) == 'ITSF' && $extname != 'txt')
        {
            $format = 'chm';
        } elseif (substr($str ,0, 4) == "\x2ERMF")
        {
            $format = 'rm';
        } elseif ($extname == 'sql')
        {
            $format = 'sql';
        } elseif ($extname == 'txt')
        {
            $format = 'txt';
        }
    }

    if ($limit_ext_types && stristr($limit_ext_types, '|' . $format . '|') === false)
    {
        $format = '';
    }

    return $format;
}

/**
 * 瀵 MYSQL LIKE 鐨勫唴瀹硅繘琛岃浆涔
 *
 * @access      public
 * @param       string      string  鍐呭?
 * @return      string
 */
function mysql_like_quote($str)
{
    return strtr($str, array("\\\\" => "\\\\\\\\", '_' => '\_', '%' => '\%', "\'" => "\\\\\'"));
}

/**
 * 鑾峰彇鏈嶅姟鍣ㄧ殑ip
 *
 * @access      public
 *
 * @return string
 **/
function real_server_ip()
{
    static $serverip = NULL;

    if ($serverip !== NULL)
    {
        return $serverip;
    }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['SERVER_ADDR']))
        {
            $serverip = $_SERVER['SERVER_ADDR'];
        }
        else
        {
            $serverip = '0.0.0.0';
        }
    }
    else
    {
        $serverip = getenv('SERVER_ADDR');
    }

    return $serverip;
}

/**
 * 鑷?畾涔 header 鍑芥暟锛岀敤浜庤繃婊ゅ彲鑳藉嚭鐜扮殑瀹夊叏闅愭偅
 *
 * @param   string  string  鍐呭?
 *
 * @return  void
 **/
function ecs_header($string, $replace = true, $http_response_code = 0)
{
    if (strpos($string, '../upgrade/index.php') === 0)
    {
        echo '<script type="text/javascript">window.location.href="' . $string . '";</script>';
    }
    $string = str_replace(array("\r", "\n"), array('', ''), $string);

    if (preg_match('/^\s*location:/is', $string))
    {
        @header($string . "\n", $replace);

        exit();
    }

    if (empty($http_response_code) || PHP_VERSION < '4.3')
    {
        @header($string, $replace);
    }
    else
    {
        @header($string, $replace, $http_response_code);
    }
}

function ecs_iconv($source_lang, $target_lang, $source_string = '')
{
    static $chs = NULL;

    /* 濡傛灉瀛楃?涓蹭负绌烘垨鑰呭瓧绗︿覆涓嶉渶瑕佽浆鎹?紝鐩存帴杩斿洖 */
    if ($source_lang == $target_lang || $source_string == '' || preg_match("/[\x80-\xFF]+/", $source_string) == 0)
    {
        return $source_string;
    }

    if ($chs === NULL)
    {
        require_once(ROOT_PATH . 'includes/cls_iconv.php');
        $chs = new Chinese(ROOT_PATH);
    }

    return $chs->Convert($source_lang, $target_lang, $source_string);
}

function ecs_geoip($ip)
{
    static $fp = NULL, $offset = array(), $index = NULL;

    $ip    = gethostbyname($ip);
    $ipdot = explode('.', $ip);
    $ip    = pack('N', ip2long($ip));

    $ipdot[0] = (int)$ipdot[0];
    $ipdot[1] = (int)$ipdot[1];
    if ($ipdot[0] == 10 || $ipdot[0] == 127 || ($ipdot[0] == 192 && $ipdot[1] == 168) || ($ipdot[0] == 172 && ($ipdot[1] >= 16 && $ipdot[1] <= 31)))
    {
        return 'LAN';
    }

    if ($fp === NULL)
    {
        $fp = fopen(ROOT_PATH . 'includes/codetable/ipdata.dat', 'rb');
        if ($fp === false)
        {
            return 'Invalid IP data file';
        }
        $offset = unpack('Nlen', fread($fp, 4));
        if ($offset['len'] < 4)
        {
            return 'Invalid IP data file';
        }
        $index  = fread($fp, $offset['len'] - 4);
    }

    $length = $offset['len'] - 1028;
    $start  = unpack('Vlen', $index[$ipdot[0] * 4] . $index[$ipdot[0] * 4 + 1] . $index[$ipdot[0] * 4 + 2] . $index[$ipdot[0] * 4 + 3]);
    for ($start = $start['len'] * 8 + 1024; $start < $length; $start += 8)
    {
        if ($index{$start} . $index{$start + 1} . $index{$start + 2} . $index{$start + 3} >= $ip)
        {
            $index_offset = unpack('Vlen', $index{$start + 4} . $index{$start + 5} . $index{$start + 6} . "\x0");
            $index_length = unpack('Clen', $index{$start + 7});
            break;
        }
    }

    fseek($fp, $offset['len'] + $index_offset['len'] - 1024);
    $area = fread($fp, $index_length['len']);

    fclose($fp);
    $fp = NULL;

    return $area;
}

/**
 * 鍘婚櫎瀛楃?涓插彸渚у彲鑳藉嚭鐜扮殑涔辩爜
 *
 * @param   string      $str        瀛楃?涓
 *
 * @return  string
 */
function trim_right($str)
{
    $len = strlen($str);
    /* 涓虹┖鎴栧崟涓?瓧绗︾洿鎺ヨ繑鍥 */
    if ($len == 0 || ord($str{$len-1}) < 127)
    {
        return $str;
    }
    /* 鏈夊墠瀵煎瓧绗︾殑鐩存帴鎶婂墠瀵煎瓧绗﹀幓鎺 */
    if (ord($str{$len-1}) >= 192)
    {
       return substr($str, 0, $len-1);
    }
    /* 鏈夐潪鐙?珛鐨勫瓧绗︼紝鍏堟妸闈炵嫭绔嬪瓧绗﹀幓鎺夛紝鍐嶉獙璇侀潪鐙?珛鐨勫瓧绗︽槸涓嶆槸涓€涓?畬鏁寸殑瀛楋紝涓嶆槸杩炲師鏉ュ墠瀵煎瓧绗︿篃鎴?彇鎺 */
    $r_len = strlen(rtrim($str, "\x80..\xBF"));
    if ($r_len == 0 || ord($str{$r_len-1}) < 127)
    {
        return sub_str($str, 0, $r_len);
    }

    $as_num = ord(~$str{$r_len -1});
    if ($as_num > (1<<(6 + $r_len - $len)))
    {
        return $str;
    }
    else
    {
        return substr($str, 0, $r_len-1);
    }
}

/**
 * 灏嗕笂浼犳枃浠惰浆绉诲埌鎸囧畾浣嶇疆
 *
 * @param string $file_name
 * @param string $target_name
 * @return blog
 */
function move_upload_file($file_name, $target_name = '')
{
    if (function_exists("move_uploaded_file"))
    {
        if (move_uploaded_file($file_name, $target_name))
        {
            @chmod($target_name,0775);
            return true;
        }
        else if (copy($file_name, $target_name))
        {
            @chmod($target_name,0775);
            return true;
        }
    }
    elseif (copy($file_name, $target_name))
    {
        @chmod($target_name,0775);
        return true;
    }
    return false;
}

/**
 * 灏咼SON浼犻€掔殑鍙傛暟杞?爜
 *
 * @param string $str
 * @return string
 */
function json_str_iconv($str)
{
    if (EC_CHARSET != 'utf-8')
    {
        if (is_string($str))
        {
            return ecs_iconv('utf-8', EC_CHARSET, $str);
        }
        elseif (is_array($str))
        {
            foreach ($str as $key => $value)
            {
                $str[$key] = json_str_iconv($value);
            }
            return $str;
        }
        elseif (is_object($str))
        {
            foreach ($str as $key => $value)
            {
                $str->$key = json_str_iconv($value);
            }
            return $str;
        }
        else
        {
            return $str;
        }
    }
    return $str;
}

/**
 * 鑾峰彇鏂囦欢鍚庣紑鍚?骞跺垽鏂?槸鍚﹀悎娉
 *
 * @param string $file_name
 * @param array $allow_type
 * @return blob
 */
function get_file_suffix($file_name, $allow_type = array())
{
    $file_suffix = strtolower(array_pop(explode('.', $file_name)));
    if (empty($allow_type))
    {
        return $file_suffix;
    }
    else
    {
        if (in_array($file_suffix, $allow_type))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

/**
 * 璇荤粨鏋滅紦瀛樻枃浠
 *
 * @params  string  $cache_name
 *
 * @return  array   $data
 */
function read_static_cache($cache_name)
{
    if ((DEBUG_MODE & 2) == 2)
    {
        return false;
    }
    static $result = array();
    if (!empty($result[$cache_name]))
    {
        return $result[$cache_name];
    }
    $cache_file_path = ROOT_PATH . USER_PATH . '/temp/static_caches/' . $cache_name . '.php';
    if (file_exists($cache_file_path))
    {
        if (@filesize($cache_file_path) == 0)
        {
            @unlink($cache_file_path);
            return false;
        }
        include_once($cache_file_path);
        $result[$cache_name] = $data;
        return $result[$cache_name];
    }
    else
    {
        return false;
    }
}

/**
 * 鍐欑粨鏋滅紦瀛樻枃浠
 *
 * @params  string  $cache_name
 * @params  string  $caches
 *
 * @return
 */
function write_static_cache($cache_name, $caches)
{
    if ((DEBUG_MODE & 2) == 2)
    {
        return false;
    }
    $cache_file_path = ROOT_PATH . USER_PATH . 'temp/static_caches/' . $cache_name . '.php';
    $content = "<?php\r\n";
    $content .= "\$data = " . var_export($caches, true) . ";\r\n";
    $content .= "?>";

    file_put_contents($cache_file_path, $content, LOCK_SH);
}


function mb_unserialize($serial_str) {
    $serial_str= preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
    $serial_str= str_replace("\r", "", $serial_str);      
    return @unserialize($serial_str);
}


?>
