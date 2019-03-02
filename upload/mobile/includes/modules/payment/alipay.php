<?php

/**
 * ECSHOP 鏀?粯瀹濇彃浠
 * ============================================================================
 * * 鐗堟潈鎵€鏈 2005-2012 涓婃捣鍟嗘淳缃戠粶绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: douqinghua $
 * $Id: alipay.php 17217 2011-01-19 06:29:08Z douqinghua $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/mobile/alipay.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 妯″潡鐨勫熀鏈?俊鎭 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 浠ｇ爜 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 鎻忚堪瀵瑰簲鐨勮?瑷€椤 */
    $modules[$i]['desc']    = 'alipay_desc';

    /* 鏄?惁鏀?寔璐у埌浠樻? */
    $modules[$i]['is_cod']  = '0';

    /* 鏄?惁鏀?寔鍦ㄧ嚎鏀?粯 */
    $modules[$i]['is_online']  = '1';

    /* 浣滆€ */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* 缃戝潃 */
    $modules[$i]['website'] = 'http://www.alipay.com';

    /* 鐗堟湰鍙 */
    $modules[$i]['version'] = '1.0.2';

    /* 閰嶇疆淇℃伅 */
    $modules[$i]['config']  = array(
        array('name' => 'alipay_account',           'type' => 'text',   'value' => ''),
        array('name' => 'alipay_key',               'type' => 'text',   'value' => ''),
        array('name' => 'alipay_partner',           'type' => 'text',   'value' => '')
    );

    return;
}

/**
 * 绫
 */
class alipay
{

    /**
     * 鏋勯€犲嚱鏁
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function alipay()
    {
    }

    function __construct()
    {
        $this->alipay();
    }

    /**
     * 鐢熸垚鏀?粯浠ｇ爜
     * @param   array   $order      璁㈠崟淇℃伅
     * @param   array   $payment    鏀?粯鏂瑰紡淇℃伅
     */
    function get_code($order, $payment)
    {
        if (!defined('EC_CHARSET'))
        {
            $charset = 'utf-8';
        }
        else
        {
            $charset = EC_CHARSET;
        }

    $alipay_config=array();
    $alipay_config['partner']		= $payment['alipay_partner'];
    //瀹夊叏妫€楠岀爜锛屼互鏁板瓧鍜屽瓧姣嶇粍鎴愮殑32浣嶅瓧绗
    //濡傛灉绛惧悕鏂瑰紡璁剧疆涓衡€淢D5鈥濇椂锛岃?璁剧疆璇ュ弬鏁
    $alipay_config['key']			= $payment['alipay_key'];
    //鍟嗘埛鐨勭?閽ワ紙鍚庣紑鏄?pen锛夋枃浠剁浉瀵硅矾寰
    //濡傛灉绛惧悕鏂瑰紡璁剧疆涓衡€?001鈥濇椂锛岃?璁剧疆璇ュ弬鏁
    //$alipay_config['private_key_path']	= 'key/rsa_private_key.pem';
    //鏀?粯瀹濆叕閽ワ紙鍚庣紑鏄?pen锛夋枃浠剁浉瀵硅矾寰
    //濡傛灉绛惧悕鏂瑰紡璁剧疆涓衡€?001鈥濇椂锛岃?璁剧疆璇ュ弬鏁
   // $alipay_config['ali_public_key_path']= 'key/alipay_public_key.pem';
    //绛惧悕鏂瑰紡 涓嶉渶淇?敼
    $alipay_config['sign_type']    = 'MD5';

    //瀛楃?缂栫爜鏍煎紡 鐩?墠鏀?寔 gbk 鎴 utf-8
    $alipay_config['input_charset']= 'utf-8';
    $alipay_config['cacert']='';
   // $alipay_config['cacert']    = ROOT_PATH .'mobile/includes/modules/cacert.pem';

    //ca璇佷功璺?緞鍦板潃锛岀敤浜巆url涓璼sl鏍￠獙
    //璇蜂繚璇乧acert.pem鏂囦欢鍦ㄥ綋鍓嶆枃浠跺す鐩?綍涓
    //$alipay_config['cacert']    = getcwd().'\\cacert.pem';

    //璁块棶妯″紡,鏍规嵁鑷?繁鐨勬湇鍔″櫒鏄?惁鏀?寔ssl璁块棶锛岃嫢鏀?寔璇烽€夋嫨https锛涜嫢涓嶆敮鎸佽?閫夋嫨http
    $alipay_config['transport']    = 'http';

    require_once(ROOT_PATH ."mobile/includes/modules/lib/alipay_submit.class.php");


$format = "xml";
//蹇呭～锛屼笉闇€瑕佷慨鏀

//杩斿洖鏍煎紡
$v = "2.0";
//蹇呭～锛屼笉闇€瑕佷慨鏀

//璇锋眰鍙
$req_id = date('Ymdhis');
//蹇呭～锛岄』淇濊瘉姣忔?璇锋眰閮芥槸鍞?竴

//**req_data璇︾粏淇℃伅**
//鏈嶅姟鍣ㄥ紓姝ラ€氱煡椤甸潰璺?緞
$notify_url ='';
//闇€http://鏍煎紡鐨勫畬鏁磋矾寰勶紝涓嶅厑璁稿姞?id=123杩欑被鑷?畾涔夊弬鏁
//椤甸潰璺宠浆鍚屾?閫氱煡椤甸潰璺?緞
$call_back_url = $GLOBALS['ecs']->url().'alipay.php';


$seller_email = $payment['alipay_account'];
//蹇呭～
//鍟嗘埛璁㈠崟鍙
$out_trade_no = $order['order_sn'];
//鍟嗘埛缃戠珯璁㈠崟绯荤粺涓?敮涓€璁㈠崟鍙凤紝蹇呭～

//璁㈠崟鍚嶇О
$subject = 'ecshop';
//蹇呭～

//浠樻?閲戦?
$total_fee = $order['order_amount'];
//蹇呭～

//璇锋眰涓氬姟鍙傛暟璇︾粏
$req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee></direct_trade_create_req>';

//鏋勯€犺?璇锋眰鐨勫弬鏁版暟缁勶紝鏃犻渶鏀瑰姩
$para_token = array(
		"service" => "alipay.wap.trade.create.direct",
		"partner" => trim($alipay_config['partner']),
		"sec_id" => trim($alipay_config['sign_type']),
		"format"	=> $format,
		"v"	=> $v,
		"req_id"	=> $req_id,
		"req_data"	=> $req_data,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//寤虹珛璇锋眰
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestHttp($para_token);

//URLDECODE杩斿洖鐨勪俊鎭
$html_text = urldecode($html_text);

//瑙ｆ瀽杩滅▼妯℃嫙鎻愪氦鍚庤繑鍥炵殑淇℃伅
$para_html_text = $alipaySubmit->parseResponse($html_text);


//鑾峰彇request_token
$request_token = $para_html_text['request_token'];


/**************************鏍规嵁鎺堟潈鐮乼oken璋冪敤浜ゆ槗鎺ュ彛alipay.wap.auth.authAndExecute**************************/

//涓氬姟璇︾粏
$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
//蹇呭～

//鏋勯€犺?璇锋眰鐨勫弬鏁版暟缁勶紝鏃犻渶鏀瑰姩
$parameter = array(
		"service" => "alipay.wap.auth.authAndExecute",
		"partner" => trim($alipay_config['partner']),
		"v"	=> $v,
		"sec_id" => trim($alipay_config['sign_type']),
		"format"	=> $format,
		"req_id"	=> $req_id,
		"req_data"	=> $req_data,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//寤虹珛璇锋眰
$alipaySubmit = new AlipaySubmit($alipay_config);

$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '纭??');

//var_dump($html_text);
return $html_text; 


        return $html_text; 
    }

    /**
     * 鍝嶅簲鎿嶄綔
     */
    function respond()
    {
        if (!empty($_POST))
        {
            foreach($_POST as $key => $data)
            {
                $_GET[$key] = $data;
            }
        }

    $payment  = get_mobile_payment('alipay');
    //var_dump( $payment);exit;


    $alipay_config=array();
    $alipay_config['partner']		= $payment['alipay_partner'];
    //瀹夊叏妫€楠岀爜锛屼互鏁板瓧鍜屽瓧姣嶇粍鎴愮殑32浣嶅瓧绗
    //濡傛灉绛惧悕鏂瑰紡璁剧疆涓衡€淢D5鈥濇椂锛岃?璁剧疆璇ュ弬鏁
    $alipay_config['key']			= $payment['alipay_key'];
    //鍟嗘埛鐨勭?閽ワ紙鍚庣紑鏄?pen锛夋枃浠剁浉瀵硅矾寰
    //濡傛灉绛惧悕鏂瑰紡璁剧疆涓衡€?001鈥濇椂锛岃?璁剧疆璇ュ弬鏁
    $alipay_config['private_key_path']	= '';
     //$alipay_config['private_key_path']	= 'key/rsa_private_key.pem';
    //鏀?粯瀹濆叕閽ワ紙鍚庣紑鏄?pen锛夋枃浠剁浉瀵硅矾寰
    //濡傛灉绛惧悕鏂瑰紡璁剧疆涓衡€?001鈥濇椂锛岃?璁剧疆璇ュ弬鏁
    $alipay_config['ali_public_key_path']= '';   
    //$alipay_config['ali_public_key_path']= 'key/alipay_public_key.pem';
    //绛惧悕鏂瑰紡 涓嶉渶淇?敼
    $alipay_config['sign_type']    = 'MD5';

    //瀛楃?缂栫爜鏍煎紡 鐩?墠鏀?寔 gbk 鎴 utf-8
    $alipay_config['input_charset']= 'utf-8';
    //$alipay_config['cacert']    = ROOT_PATH .'mobile/includes/modules/cacert.pem';
     $alipay_config['cacert']    =''; 
    //ca璇佷功璺?緞鍦板潃锛岀敤浜巆url涓璼sl鏍￠獙
    //璇蜂繚璇乧acert.pem鏂囦欢鍦ㄥ綋鍓嶆枃浠跺す鐩?綍涓
    //$alipay_config['cacert']    = getcwd().'\\cacert.pem';

    //璁块棶妯″紡,鏍规嵁鑷?繁鐨勬湇鍔″櫒鏄?惁鏀?寔ssl璁块棶锛岃嫢鏀?寔璇烽€夋嫨https锛涜嫢涓嶆敮鎸佽?閫夋嫨http

      $alipay_config['transport']    = 'http';

      require_once(ROOT_PATH ."mobile/includes/modules/lib/alipay_notify.class.php");



$alipayNotify = new AlipayNotify($alipay_config);

$verify_result = $alipayNotify->verifyReturn();


if($verify_result)
{

    $order_sn = trim($_GET['out_trade_no']);

    $sql = "SELECT l.`log_id` FROM " . $GLOBALS['ecs']->table('order_info')." as info LEFT JOIN ". $GLOBALS['ecs']->table('pay_log') ." as l  ON l.order_id=info.order_id        WHERE info.order_sn = '$order_sn'";
    $order_log_id = $GLOBALS['db']->getOne($sql);

    order_paid($order_log_id, 2);
    return true;
}
else
{
    return false;
}



       
        $seller_email = rawurldecode($payment['alipay_account']);

        $order_sn = str_replace($_GET['subject'], '', $_GET['out_trade_no']);
        $order_sn = trim($order_sn);

        /* 妫€鏌ユ暟瀛楃?鍚嶆槸鍚︽?纭 */
        ksort($_GET);
        reset($_GET);

        $sign = '';
        foreach ($_GET AS $key=>$val)
        {
            if ($key != 'sign' && $key != 'sign_type' && $key != 'code')
            {
                $sign .= "$key=$val&";
            }
        }

        $sign = substr($sign, 0, -1) . $payment['alipay_key'];
        //$sign = substr($sign, 0, -1) . ALIPAY_AUTH;
        if (md5($sign) != $_GET['sign'])
        {
            return false;
        }

        /* 妫€鏌ユ敮浠樼殑閲戦?鏄?惁鐩哥? */
        if (!check_money($order_sn, $_GET['total_fee']))
        {
            return false;
        }

        if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS')
        {
            /* 鏀瑰彉璁㈠崟鐘舵€ */
            order_paid($order_sn, 2);

            return true;
        }
        elseif ($_GET['trade_status'] == 'TRADE_FINISHED')
        {
            /* 鏀瑰彉璁㈠崟鐘舵€ */
            order_paid($order_sn);

            return true;
        }
        elseif ($_GET['trade_status'] == 'TRADE_SUCCESS')
        {
            /* 鏀瑰彉璁㈠崟鐘舵€ */
            order_paid($order_sn, 2);

            return true;
        }
        else
        {
            return false;
        }
    }
}

?>