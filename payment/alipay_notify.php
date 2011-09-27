<?php
define('CURSCRIPT', 'plugin');

require '../../../../source/class/class_core.php';

$discuz = & discuz_core::instance();
$discuz->init();

$sitepath = substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'));
$sitepath = preg_replace("/\/source\/?.*?$/i", '', $sitepath);
$_G['siteurl'] = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].$sitepath.'/');

require_once DISCUZ_ROOT."./source/plugin/lodov_tuan_dzx/lodov_tuan.func.php";
define('ALIPAY_PID', $_C['pay']['alipay']['pid']);
define('ALIPAY_KEY', $_C['pay']['alipay']['key']);
define('ALIPAY_ACCOUNT', $_C['pay']['alipay']['account']);
define('ALIPAY_APITYPE', $_C['pay']['alipay']['apitype']);

require_once DISCUZ_ROOT."./source/plugin/lodov_tuan_dzx/payment/alipay_api.php";
$alipay = new AlipayNotify();
$verify_result = $alipay->verifyNotify();

if($verify_result){

	if(ALIPAY_APITYPE == 1){
		$orderid = $_POST['out_trade_no'];
		$paymoney = $_POST['total_fee'];

		if($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
			paynotify($orderid, $paymoney ,'alipay');
			echo "success";

		} else {
			echo "success";
		}
	}else if(ALIPAY_APITYPE == 2 || ALIPAY_APITYPE == 3){
		$orderid = $_POST['out_trade_no'];
		$paymoney = $_POST['price'];

		if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {//产生了交易记录没有付款
			echo "success";
		}else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {//付款成功没有发货
			paynotify($orderid, $paymoney ,'alipay');
			echo "success";
		}else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {//已经发货没有确认收货
			echo "success";
		}else if($_POST['trade_status'] == 'TRADE_FINISHED') {//已确认收货交易完成
			echo "success";
		}else {
			echo "success";
		}
	}

}else {
	echo "fail";
}
?>