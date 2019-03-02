<?php
/* *
 * MD5
 * 璇︾粏锛歁D5鍔犲瘑
 * 鐗堟湰锛?.3
 * 鏃ユ湡锛?012-07-19
 * 璇存槑锛
 * 浠ヤ笅浠ｇ爜鍙?槸涓轰簡鏂逛究鍟嗘埛娴嬭瘯鑰屾彁渚涚殑鏍蜂緥浠ｇ爜锛屽晢鎴峰彲浠ユ牴鎹?嚜宸辩綉绔欑殑闇€瑕侊紝鎸夌収鎶€鏈?枃妗ｇ紪鍐?骞堕潪涓€瀹氳?浣跨敤璇ヤ唬鐮併€
 * 璇ヤ唬鐮佷粎渚涘?涔犲拰鐮旂┒鏀?粯瀹濇帴鍙ｄ娇鐢?紝鍙?槸鎻愪緵涓€涓?弬鑰冦€
 */

/**
 * 绛惧悕瀛楃?涓
 * @param $prestr 闇€瑕佺?鍚嶇殑瀛楃?涓
 * @param $key 绉侀挜
 * return 绛惧悕缁撴灉
 */
function md5Sign($prestr, $key) {
	$prestr = $prestr . $key;
	return md5($prestr);
}

/**
 * 楠岃瘉绛惧悕
 * @param $prestr 闇€瑕佺?鍚嶇殑瀛楃?涓
 * @param $sign 绛惧悕缁撴灉
 * @param $key 绉侀挜
 * return 绛惧悕缁撴灉
 */
function md5Verify($prestr, $sign, $key) {
	$prestr = $prestr . $key;
	$mysgin = md5($prestr);
	logResult("sign:".$sign);
	logResult("mysgin:".$mysgin);
	if($mysgin == $sign) {
		return true;
	}
	else {
		return false;
	}
}
?>