<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(empty($_G['gp_closeorder']) && empty($_G['gp_receorder'])) {
	$filter = trim($_G['gp_filter']);
	$filter = in_array($filter, array('all', 'pending', 'paid', 'shipped','finished', 'canceled', 'invalid')) ? $filter : 'all';
	$actives[$filter] = 'class="a"';
	if($filter!=='all')
	{		
		$extra_sql = 'and status ="'.$filter.'"';
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
}else if(!empty($_G['gp_confirm_received'])){
	$id=intval(trim($_G['gp_orderid']));
	DB::update('etuan_order', array('status' => 'finished', 'received_at'=>dgmdate(time(), 'Y-m-d H:i:s');), "id='$id'");
	echo 'true';
}else if(!empty($_G['gp_cancel'])){
	$id=intval(trim($_G['gp_orderid']));
	DB::update('etuan_order', array('status' => 'canceled'), "id='$id'");
	echo 'true';
}

?>