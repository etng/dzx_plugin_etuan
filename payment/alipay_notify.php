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
$alipay = new AlipayNotify(alipay_config);
$verify_result = $alipay->verifyNotify();

if($verify_result){

	if(alipay_config->api_type == 1){
		$order_id = $_POST['out_trade_no'];
		$amount = $_POST['total_fee'];

		if($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
			$etuan->acceptPayment($order_id, $amount ,'alipay');
			echo "success";
		} else {
			echo "success";
		}
	}else if(alipay_config->api_type == 2 || alipay_config->api_type == 3){
		$order_id = $_POST['out_trade_no'];
		$amount = $_POST['price'];

		if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {//�����˽��׼�¼û�и���
			echo "success";
		}else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {//����ɹ�û�з���
			$etuan->acceptPayment($order_id, $amount ,'alipay');
			echo "success";
		}else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {//�Ѿ�����û��ȷ���ջ�
			echo "success";
		}else if($_POST['trade_status'] == 'TRADE_FINISHED') {//��ȷ���ջ��������
			echo "success";
		}else {
			echo "success";
		}
	}

}else {
	echo "fail";
}
?>