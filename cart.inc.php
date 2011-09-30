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
        $etuan->ajaxOrMsg('etuan:cart_add_success', $cart_url);
        break;
    case 'edit'://更新商品
        $ecart->updateItem($_G['gp_tuan_id'], $_G['gp_product_id'], $_G['gp_quantity'] );
        $etuan->ajaxOrMsg('etuan:cart_edit_success', $cart_url);
        break;
    case 'remove'://删除商品
    case 'delete'://删除商品
        $ecart->removeItem($_G['gp_tuan_id'], $_G['gp_product_id']);
        $etuan->ajaxOrMsg('etuan:cart_delete_success', $cart_url);
        break;
     case 'clear'://清空购物车
        $ecart->clear();
        $etuan->ajaxOrMsg('etuan:cart_clear_success', $cart_url);
        break;
    case 'checkout'://填写结账单即选择送货地址、支付方式、发货方式及积分抵扣等
        $cart_items_group_by_tuan = $ecart->getItemsGroupByTuan($get_detail=true);
        $addresses = $etuan->fetchAll('etuan_address', array("buyer_id={$_G['uid']}"));
        include template('etuan:cart_checkout');
        break;
    case 'submit'://生成订单
        $order = $ecart->submit($_G['gp_tuan_id'], $_G['uid'], $_G['gp_credit_used'], $_G['gp_shipmethod_id'], $_G['gp_payment_id'], $_G['gp_address_id'], $_G['gp_memo']);
        $order_view_url = "plugin.php?id=etuan:my&app=order&op=view&orderid={$order['id']}";
        if($_G['gp_payment_id']!='cod')
        {
            require_once(DISCUZ_ROOT.'./source/plugin/etuan/payment/'.$_G['gp_payment_id'].'_api.php');
            $cls = ucfirst($_G['gp_payment_id']) . 'Service';
            /**
              * @todo fill $config
              */
            $gateway = new $cls($config);
            $gateway->doSend($order['id'], $order['tuan']['name'], $order['total'], $_G['siteurl'].$order_view_url);
        }else
        {
            if(!$ecart->isEmpty())
            {
                $etuan->ajaxOrMsg('etuan:order_create_success_continue', "plugin.php?id=etuan:cart&op=checkout");
            }else
            {
                $etuan->ajaxOrMsg('etuan:order_create_success', $order_view_url);
            }
        }
        break;
    case 'list':
    default:
        $cart_items_group_by_tuan = $ecart->getItemsGroupByTuan($get_detail=true);
        include template('etuan:cart_view');
  break;
}
