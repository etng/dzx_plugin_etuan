<?php
define('CURSCRIPT', 'plugin');

require '../../../../source/class/class_core.php';

$discuz = & discuz_core::instance();
$discuz->init();

$sitepath = substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'));
$sitepath = preg_replace("/\/source\/?.*?$/i", '', $sitepath);
$_G['siteurl'] = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].$sitepath.'/');

require_once DISCUZ_ROOT."./source/plugin/etuan/etuan.func.php";
define('ALIPAY_PID', $_C['pay']['alipay']['pid']);
define('ALIPAY_KEY', $_C['pay']['alipay']['key']);
define('ALIPAY_ACCOUNT', $_C['pay']['alipay']['account']);
define('ALIPAY_APITYPE', $_C['pay']['alipay']['apitype']);

require_once DISCUZ_ROOT."./source/plugin/etuan/payment/alipay_api.php";
$alipay = new AlipayNotify();
$verify_result = $alipay->verifyReturn();

if($verify_result){

	if(ALIPAY_APITYPE == 1){
		$orderid = $_GET['out_trade_no'];
		$paymoney = $_GET['total_fee'];

		if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
			paynotify($orderid, $paymoney ,'alipay');
			dheader("Location: ".$_G['siteurl']."plugin.php?id=etuan:return&orderid={$orderid}");
		} else {
			showmessage('etuan:pay_fail',$_G['siteurl'].'plugin.php?id=etuan:pay&orderid={$orderid}');
		}
	}else if(ALIPAY_APITYPE == 2 || ALIPAY_APITYPE == 3){
		$orderid = $_GET['out_trade_no'];
		$paymoney = $_GET['price'];

		if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
			paynotify($orderid, $paymoney, 'alipay');
			dheader("Location: ".$_G['siteurl']."plugin.php?id=etuan:return&orderid={$orderid}");
		} else {
			showmessage('etuan:pay_fail',$_G['siteurl'].'plugin.php?id=etuan:pay&orderid={$orderid}');
		}
	}
}else {
	showmessage('etuan:pay_sign_error');
}

?>