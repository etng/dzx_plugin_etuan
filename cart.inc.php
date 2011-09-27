<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once DISCUZ_ROOT.'./source/plugin/etuan/etuan.func.php';
//购物车在此
$op = trim(@$_G['gp_op']);
$cart_url = 'plugin.php?id=etuan:cart&op=list';
switch($op){
    case 'add': //加入购物车
        $ecart->addItem($_G['gp_tuan_id'], $_G['gp_product_id'], empty($_G['gp_quantity'])?1:$_G['gp_quantity']);
        if($_G['inajax']) {
            echo 'true';
        }else
        {
            showmessage('etuan:cart_add_success', $cart_url, array(), array('showdialog' => 1, 'closetime' => true));
        }
        break;
    case 'edit'://更新商品
        $ecart->updateItem($_G['gp_tuan_id'], $_G['gp_product_id'], $_G['gp_quantity'] );
        if($_G['inajax']) {
            echo 'true';
        }else
        {
            showmessage('etuan:cart_edit_success', $cart_url, array(), array('showdialog' => 1, 'closetime' => true));
        }        break;
    case 'remove'://删除商品
    case 'delete'://删除商品
        $ecart->removeItem($_G['gp_tuan_id'], $_G['gp_product_id']);
        if($_G['inajax']) {
            echo 'true';
        }else
        {
            showmessage('etuan:cart_delete_success', $cart_url, array(), array('showdialog' => 1, 'closetime' => true));
        }
        break;
     case 'clear'://清空购物车
        $ecart->clear();
        if($_G['inajax']) {
            echo 'true';
        }else
        {
            showmessage('etuan:cart_clear_success', $cart_url, array(), array('showdialog' => 1, 'closetime' => true));
        }
        break;
    case 'checkout'://填写结账单即选择送货地址、支付方式、发货方式及积分抵扣等
        include template('etuan:cart_checkout');
        break;
    case 'submit'://生成订单
        $sn = $ecart->submit($_G['uid'], $_G['gp_credit_used'], $_G['gp_shipmethod_id'], $_G['gp_payment_id'], $_G['gp_address_id'], $_G['gp_memo']);

        showmessage('etuan:order_create_success', "plugin.php?id=etuan:my&app=order&sn={$sn}", array(), array('showdialog' => 1, 'closetime' => true));
        break;
    case 'list':
    default:
        $cart_items = $ecart->getItems();
        include template('etuan:cart_view');
  break;
}
