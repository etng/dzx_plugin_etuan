<?php
if(!defined('IN_DISCUZ')){
	exit("Access Denied!");
}
require_once DISCUZ_ROOT.'./source/plugin/etuan/etuan.class.php';
$etuan = new etuan();
$ecart = new etuan_cart($etuan);
$ship_methods = array(
    'pick'=>'自提',
    'deliver'=>'送货上门',
);
$order_statuss = array(
    'pending'=>'未处理',
    'paid'=>'已支付，等待发货',
    'shipped'=>'已发货，等待确认收货',
    'finished'=>'已收货，订单完成',
    'canceled'=>'已取消',
    'invalid'=>'无效',
);
$payment_method = array(
    'alipay'=>'支付宝',
    'cod'=>'货到付款',
    'tenpay'=>'财付通',
);
$tuan_statuss = array(
    'ing'=>'进行中',
    'over'=>'已结束',
    'pre'=>'未开始',
);