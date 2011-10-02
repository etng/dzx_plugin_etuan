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
    case 'pay':
        $id=intval(trim($_G['gp_orderid']));
        $order = $etuan->fetchRow('etuan_order', $id);
        if($order['payment_method']!='cod')
        {
            $order_view_url = "plugin.php?id=etuan:my&app=order&op=view&orderid={$order['id']}";
            require_once(DISCUZ_ROOT.'./source/plugin/etuan/payment/'.$order['payment_method'].'_api.php');
            $cls = ucfirst($order['payment_method']) . 'Service';
            $config = $etuan->paymentConf($order['payment_method']);
            $gateway = new $cls($config);
            $gateway->doSend($order['id'], $order['tuan']['name'], $order['total'], $_G['siteurl'].$order_view_url);
        }
        else
        {
            // not need to pay
        }
        break;
    case 'view':
        $id=intval(trim($_G['gp_orderid']));
        $order = $etuan->fetchRow('etuan_order', $id);
        $order['tuan'] = $etuan->fetchTuan($order['tuan_id']);
        $order['ship_community'] = $etuan->fetchRow('etuan_community', $order['ship_community_id']);
        $order['items'] = $etuan->fetchAll($sql="SELECT
            op.*, p.name AS product_name,p.spec AS product_spec
        FROM ".DB::table('etuan_order_product')." AS op
        LEFT JOIN ".DB::table('etuan_product')." AS p ON op.product_id=p.id
        WHERE op.order_id= {$order['id']}
        ");
        $order['payment_log'] = $etuan->fetchAll('etuan_payment_log', array("order_id= {$order['id']}"));
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
            $list[] = $row;
        }
        $multi = multi($total, $limit, $page, "plugin.php?id=etuan:my&filter={$filter}");
        include template('etuan:my_order');
        break;
}