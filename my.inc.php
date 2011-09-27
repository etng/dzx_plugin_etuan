<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//ละถฯำรปงสวท๑ตวยผ
if(!$_G['uid']){
	showmessage('etuan:buy_login_nopermission', NULL, array(), array('login' => 1));
}
require_once DISCUZ_ROOT.'./source/plugin/etuan/etuan.func.php';
$app = $_G['gp_app'];
$allowed_apps=$common_apps = array('order', 'address', 'balance');
$seller_apps = array('supplier', 'product', 'tuan', 'shipmethod', 'payment');
$is_seller = in_array($_G['group']['groupid'], $etuan->readConf('seller_gid'));
$is_seller = true;
if($is_seller)
{

    $allowed_apps = $seller_apps;
    array_splice($allowed_apps, count($allowed_apps), 0, $common_apps);
}
$app = in_array($app, $allowed_apps)?$app:reset($allowed_apps);
$opactives[$app] = 'class="a"';
require_once DISCUZ_ROOT."./source/plugin/etuan/my/{$app}.php";

?>