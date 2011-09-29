<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$op = trim(@$_G['gp_op']);
switch($op){
    case 'confirm_received':
        $id=intval(trim($_G['gp_orderid']));
        DB::update('etuan_order', array('status' => 'finished', 'received_at'=>dgmdate(time(), 'Y-m-d H:i:s')), "id='$id'");
        $etuan->ajaxOrMsg('etuan:order_confirm_received', "plugin.php?id=etuan:my&app=order");
        break;
    case 'cancel':
        $id=intval(trim($_G['gp_orderid']));
        DB::update('etuan_order', array('status' => 'canceled'), "id='$id'");
        $etuan->ajaxOrMsg('etuan:order_canceled', "plugin.php?id=etuan:my&app=order");
        break;
    case 'view':
        $id=intval(trim($_G['gp_orderid']));
        $order = $etuan->fetchRow('etuan_order', $id);
        $order['tuan'] = $etuan->fetchTuan($order['tuan_id']);
        $order['payment'] = $etuan->fetchPayment($order['payment_id']);
        $order['shipmethod'] = $etuan->fetchShipMethod($order['shipmethod_id']);
        include template('etuan:my_order_view');
         break;
     case 'list':
     default:
        $filter = trim(@$_G['gp_filter']);
        $filter = in_array($filter, array('all', 'pending', 'paid', 'shipped','finished', 'canceled', 'invalid')) ? $filter : 'all';
        $actives[$filter] = 'class="a"';
        if($filter!=='all')
        {
            $extra_sql = "and status ='{$filter}'";
        }
        $page = max(1, intval($_G['page']));
        $limit = 10;
        $offset = $limit * ($page - 1);
        $total = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_order')." where buyer_id='$_G[uid]' $extra_sql");

        $list = array();
        $query = DB::query("SELECT * FROM ".DB::table('etuan_order')." where buyer_id='$_G[uid]' $extra_sql ORDER BY bought_at DESC LIMIT $offset,$limit");

        while($row = DB::fetch($query)){
            $row['tuan'] = $etuan->fetchTuan($row['tuan_id']);
            $row['payment'] = $etuan->fetchPayment($row['payment_id']);
            $row['shipmethod'] = $etuan->fetchShipMethod($row['shipmethod_id']);
            $list[] = $row;
        }
        $multi = multi($total, $limit, $page, "plugin.php?id=etuan:my&filter={$filter}");
        include template('etuan:my_order');
        break;
}