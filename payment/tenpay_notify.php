<?php
define('CURSCRIPT', 'plugin');

require '../../../../source/class/class_core.php';

$discuz = & discuz_core::instance();
$discuz->init();

$sitepath = substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'));
$sitepath = preg_replace("/\/source\/?.*?$/i", '', $sitepath);
$_G['siteurl'] = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].$sitepath.'/');

require_once DISCUZ_ROOT."./source/plugin/etuan/etuan.func.php";
define('TENPAY_PID', $_C['pay']['tenpay']['pid']);
define('TENPAY_KEY', $_C['pay']['tenpay']['key']);

require_once DISCUZ_ROOT."./source/plugin/etuan/payment/tenpay_api.php";
$tenpay = new PayResponseHandler();
if($tenpay->isTenpaySign()) {

	$orderid = $tenpay->getParameter("sp_billno");
	$paymoney = $tenpay->getParameter("total_fee");
	$paymoney = $paymoney/100;
	$pay_result = $tenpay->getParameter("pay_result");

	if($pay_result == "0") {

		paynotify($orderid, $paymoney, 'tenpay');

		$show = $_G['siteurl'].'plugin.php?id=etuan:return&orderid='.$orderid;
		$tenpay->doShow($show);
	} else {
		showmessage('etuan:pay_fail','plugin.php?id=etuan:pay&orderid={$orderid}');
	}

} else {
	showmessage('etuan:pay_sign_error');
}
?>