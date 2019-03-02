<?php
/* *
 * 绫诲悕锛欰lipayNotify
 * 鍔熻兘锛氭敮浠樺疂閫氱煡澶勭悊绫
 * 璇︾粏锛氬?鐞嗘敮浠樺疂鍚勬帴鍙ｉ€氱煡杩斿洖
 * 鐗堟湰锛?.2
 * 鏃ユ湡锛?011-03-25
 * 璇存槑锛
 * 浠ヤ笅浠ｇ爜鍙?槸涓轰簡鏂逛究鍟嗘埛娴嬭瘯鑰屾彁渚涚殑鏍蜂緥浠ｇ爜锛屽晢鎴峰彲浠ユ牴鎹?嚜宸辩綉绔欑殑闇€瑕侊紝鎸夌収鎶€鏈?枃妗ｇ紪鍐?骞堕潪涓€瀹氳?浣跨敤璇ヤ唬鐮併€
 * 璇ヤ唬鐮佷粎渚涘?涔犲拰鐮旂┒鏀?粯瀹濇帴鍙ｄ娇鐢?紝鍙?槸鎻愪緵涓€涓?弬鑰

 *************************娉ㄦ剰*************************
 * 璋冭瘯閫氱煡杩斿洖鏃讹紝鍙?煡鐪嬫垨鏀瑰啓log鏃ュ織鐨勫啓鍏?XT閲岀殑鏁版嵁锛屾潵妫€鏌ラ€氱煡杩斿洖鏄?惁姝ｅ父
 */

require_once("alipay_core.function.php");
require_once("alipay_rsa.function.php");
require_once("alipay_md5.function.php");

class AlipayNotify {
    /**
     * HTTPS褰㈠紡娑堟伅楠岃瘉鍦板潃
     */
	var $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	/**
     * HTTP褰㈠紡娑堟伅楠岃瘉鍦板潃
     */
	var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
	var $alipay_config;

	function __construct($alipay_config){
		$this->alipay_config = $alipay_config;
	}
    function AlipayNotify($alipay_config) {
    	$this->__construct($alipay_config);
    }
    /**
     * 閽堝?notify_url楠岃瘉娑堟伅鏄?惁鏄?敮浠樺疂鍙戝嚭鐨勫悎娉曟秷鎭
     * @return 楠岃瘉缁撴灉
     */
	function verifyNotify(){
		if(empty($_POST)) {//鍒ゆ柇POST鏉ョ殑鏁扮粍鏄?惁涓虹┖
			return false;
		}
		else {
			
			//瀵筺otify_data瑙ｅ瘑
			$decrypt_post_para = $_POST;
			if ($this->alipay_config['sign_type'] == '0001') {
				$decrypt_post_para['notify_data'] = rsaDecrypt($decrypt_post_para['notify_data'], $this->alipay_config['private_key_path']);
			} 
			
			//notify_id浠巇ecrypt_post_para涓?В鏋愬嚭鏉ワ紙涔熷氨鏄??decrypt_post_para涓?凡缁忓寘鍚玭otify_id鐨勫唴瀹癸級
			$doc = new DOMDocument();
			$doc->loadXML($decrypt_post_para['notify_data']);
			$notify_id = $doc->getElementsByTagName( "notify_id" )->item(0)->nodeValue;
			
			
			
			//鑾峰彇鏀?粯瀹濊繙绋嬫湇鍔″櫒ATN缁撴灉锛堥獙璇佹槸鍚︽槸鏀?粯瀹濆彂鏉ョ殑娑堟伅锛
			$responseTxt = 'true';
			if (! empty($notify_id)) {$responseTxt = $this->getResponse($notify_id);}
			//logResult("responseTxt:".$responseTxt);
			
			//鐢熸垚绛惧悕缁撴灉
			$isSign = $this->getSignVeryfy($decrypt_post_para, $_POST["sign"],false);
			//logResult("isSign:".$isSign);
			return;
			
			//鍐欐棩蹇楄?褰
			if ($isSign) {
				$isSignStr = 'true';
			}
			else {
				$isSignStr = 'false';
			}
			$log_text = "responseTxt=".$responseTxt."\n notify_url_log:isSign=".$isSignStr.",";
			$log_text = $log_text.createLinkString($_POST);
			//logResult($log_text);
			
			//楠岃瘉
			//$responsetTxt鐨勭粨鏋滀笉鏄痶rue锛屼笌鏈嶅姟鍣ㄨ?缃?棶棰樸€佸悎浣滆韩浠借€匢D銆乶otify_id涓€鍒嗛挓澶辨晥鏈夊叧
			//isSign鐨勭粨鏋滀笉鏄痶rue锛屼笌瀹夊叏鏍￠獙鐮併€佽?姹傛椂鐨勫弬鏁版牸寮忥紙濡傦細甯﹁嚜瀹氫箟鍙傛暟绛夛級銆佺紪鐮佹牸寮忔湁鍏
			if (preg_match("/true$/i",$responseTxt) && $isSign) {
				return true;
			} else {
				return false;
			}
		}
	}
	
    /**
     * 閽堝?return_url楠岃瘉娑堟伅鏄?惁鏄?敮浠樺疂鍙戝嚭鐨勫悎娉曟秷鎭
     * @return 楠岃瘉缁撴灉
     */
	function verifyReturn(){
		if(empty($_GET)) {//鍒ゆ柇GET鏉ョ殑鏁扮粍鏄?惁涓虹┖
			return false;
		}
		else {
			//鐢熸垚绛惧悕缁撴灉
			$isSign = $this->getSignVeryfy($_GET, $_GET["sign"],true);
			
			//鍐欐棩蹇楄?褰
			if ($isSign) {
				$isSignStr = 'true';
			}
			else {	$isSignStr = 'false';
			}
			$log_text = "return_url_log:isSign=".$isSignStr.",";
			$log_text = $log_text.createLinkString($_GET);
			//logResult($log_text);
			
			//楠岃瘉
			//$responsetTxt鐨勭粨鏋滀笉鏄痶rue锛屼笌鏈嶅姟鍣ㄨ?缃?棶棰樸€佸悎浣滆韩浠借€匢D銆乶otify_id涓€鍒嗛挓澶辨晥鏈夊叧
			//isSign鐨勭粨鏋滀笉鏄痶rue锛屼笌瀹夊叏鏍￠獙鐮併€佽?姹傛椂鐨勫弬鏁版牸寮忥紙濡傦細甯﹁嚜瀹氫箟鍙傛暟绛夛級銆佺紪鐮佹牸寮忔湁鍏
			if ($isSign) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
     * 瑙ｅ瘑
     * @param $input_para 瑕佽В瀵嗘暟鎹
     * @return 瑙ｅ瘑鍚庣粨鏋
     */
	function decrypt($prestr) {
		return rsaDecrypt($prestr, trim($this->alipay_config['private_key_path']));
	}
	
	/**
     * 寮傛?閫氱煡鏃讹紝瀵瑰弬鏁板仛鍥哄畾鎺掑簭
     * @param $para 鎺掑簭鍓嶇殑鍙傛暟缁
     * @return 鎺掑簭鍚庣殑鍙傛暟缁
     */
	function sortNotifyPara($para) {
		$para_sort['service'] = $para['service'];
		$para_sort['v'] = $para['v'];
		$para_sort['sec_id'] = $para['sec_id'];
		$para_sort['notify_data'] = $para['notify_data'];
		return $para_sort;
	}
	
    /**
     * 鑾峰彇杩斿洖鏃剁殑绛惧悕楠岃瘉缁撴灉
     * @param $para_temp 閫氱煡杩斿洖鏉ョ殑鍙傛暟鏁扮粍
     * @param $sign 杩斿洖鐨勭?鍚嶇粨鏋
     * @param $isSort 鏄?惁瀵瑰緟绛惧悕鏁扮粍鎺掑簭
     * @return 绛惧悕楠岃瘉缁撴灉
     */
	function getSignVeryfy($para_temp, $sign, $isSort) {
		//闄ゅ幓寰呯?鍚嶅弬鏁版暟缁勪腑鐨勭┖鍊煎拰绛惧悕鍙傛暟
		$para = paraFilter($para_temp);
		
		//瀵瑰緟绛惧悕鍙傛暟鏁扮粍鎺掑簭
		if($isSort) {
			$para = argSort($para);
		} else {
			$para = sortNotifyPara($para);
		}
		
		//鎶婃暟缁勬墍鏈夊厓绱狅紝鎸夌収鈥滃弬鏁?鍙傛暟鍊尖€濈殑妯″紡鐢ㄢ€?鈥濆瓧绗︽嫾鎺ユ垚瀛楃?涓
		$prestr = createLinkstring($para);
		
		$isSgin = false;
		

		switch (strtoupper(trim($this->alipay_config['sign_type']))) {
			case "MD5" :
				$isSgin = md5Verify($prestr, $sign, $this->alipay_config['key']);
				break;
			case "RSA" :
				$isSgin = rsaVerify($prestr, trim($this->alipay_config['ali_public_key_path']), $sign);
				break;
			case "0001" :
				$isSgin = rsaVerify($prestr, trim($this->alipay_config['ali_public_key_path']), $sign);
				break;
			default :
				$isSgin = false;
		}
		
		return $isSgin;
	}

    /**
     * 鑾峰彇杩滅▼鏈嶅姟鍣ˋTN缁撴灉,楠岃瘉杩斿洖URL
     * @param $notify_id 閫氱煡鏍￠獙ID
     * @return 鏈嶅姟鍣ˋTN缁撴灉
     * 楠岃瘉缁撴灉闆嗭細
     * invalid鍛戒护鍙傛暟涓嶅? 鍑虹幇杩欎釜閿欒?锛岃?妫€娴嬭繑鍥炲?鐞嗕腑partner鍜宬ey鏄?惁涓虹┖ 
     * true 杩斿洖姝ｇ‘淇℃伅
     * false 璇锋?鏌ラ槻鐏??鎴栬€呮槸鏈嶅姟鍣ㄩ樆姝㈢?鍙ｉ棶棰樹互鍙婇獙璇佹椂闂存槸鍚﹁秴杩囦竴鍒嗛挓
     */
	function getResponse($notify_id) {
		$transport = strtolower(trim($this->alipay_config['transport']));
		$partner = trim($this->alipay_config['partner']);
		$veryfy_url = '';
		if($transport == 'https') {
			$veryfy_url = $this->https_verify_url;
		}
		else {
			$veryfy_url = $this->http_verify_url;
		}
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = getHttpResponseGET($veryfy_url, $this->alipay_config['cacert']);
		
		return $responseTxt;
	}
}
?>
