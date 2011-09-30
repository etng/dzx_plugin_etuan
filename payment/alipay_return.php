<?php
define('CURSCRIPT', 'plugin');

require '../../../../source/class/class_core.php';

$discuz = & discuz_core::instance();
$discuz->init();

$sitepath = substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'));
$sitepath = preg_replace("/\/source\/?.*?$/i", '', $sitepath);
$_G['siteurl'] = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].$sitepath.'/');

require_once DISCUZ_ROOT."./source/plugin/etuan/etuan.func.php";
require_once DISCUZ_ROOT."./source/plugin/etuan/payment/alipay_api.php";
$alipay_config = $etuan->paymentConf('alipay');
$alipay = new Ali$etuan->acceptPayment(alipay_config);
$verify_result = $alipay->verifyReturn();

if($verify_result){
		$order_id = $_GET['out_trade_no'];
		$amount = $_GET['price'];
	if(ALIPAY_APITYPE == 1){
		if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
			$etuan->acceptPayment($order_id, $amount ,'alipay');
			dheader("Location: ".$_G['siteurl']."plugin.php?id=etuan:my&app=order&op=view&orderid={$order_id}");
		} else {
			showmessage('etuan:pay_fail',$_G['siteurl'].'plugin.php?id=etuan:my&app=order&op=view&orderid={$order_id}');
		}
	}else if(ALIPAY_APITYPE == 2 || ALIPAY_APITYPE == 3){
		if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
			$etuan->acceptPayment($order_id, $amount, 'alipay');
			dheader("Location: ".$_G['siteurl']."plugin.php?id=etuan:return&order_id={$order_id}");
		} else {
			showmessage('etuan:pay_fail',$_G['siteurl'].'plugin.php?id=etuan:my&app=order&op=view&orderid={$order_id}');
		}
	}
}else {
	showmessage('etuan:pay_sign_error');
}

?>