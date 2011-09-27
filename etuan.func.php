<?php
if(!defined('IN_DISCUZ')){
	exit("Access Denied!");
}
require_once DISCUZ_ROOT.'./source/plugin/etuan/etuan.class.php';
$etuan = new etuan();
$ecart = new etuan_cart($etuan);