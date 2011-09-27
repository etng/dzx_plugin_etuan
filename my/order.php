<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(empty($_G['gp_closeorder']) && empty($_G['gp_receorder'])) {
	$filter = trim($_G['gp_filter']);
	$filter = in_array($filter, array('all', 'ok', 'alpay', 'aldeliver', 'validate', 'place', 'nopay', 'close')) ? $filter : 'all';
	if($filter == 'ok'){
		$actives['ok'] = 'class="a"';
		$extra_sql = 'and status = 7';

	}else if($filter == 'alpay'){
		$actives['alpay'] = 'class="a"';
		$extra_sql = 'and status = 1';
	}else if($filter == 'aldeliver'){
		$actives['aldeliver'] = 'class="a"';
		$extra_sql = 'and status = 6';
	}else if($filter == 'nopay'){
		$actives['nopay'] = 'class="a"';
		$extra_sql = 'and status = 0';

	}else if($filter == 'close'){
		$actives['close'] = 'class="a"';
		$extra_sql = 'and status = 2';
	}else if($filter == 'validate'){
		$actives['validate'] = 'class="a"';
		$extra_sql = 'and (status = 5)';
	}else if($filter == 'place'){
		$actives['place'] = 'class="a"';
		$extra_sql = 'and (status = 3 or status = 4)';
	}else{
		$actives['all'] = 'class="a"';
	}

	$page = max(1, intval($_G['page']));
	$limit = 10;
	$offset = $limit * ($page - 1);
	$total = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_order')." where buyer_id='$_G[uid]' $extra_sql");

	$list = array();
	$query = DB::query("SELECT * FROM ".DB::table('etuan_order')." where buyer_id='$_G[uid]' $extra_sql ORDER BY bought_at DESC LIMIT $offset,$limit");

	while($row = DB::fetch($query)){
		$list[] = $row;
	}
	$multi = multi($total, $limit, $page, "plugin.php?id=etuan:my&filter={$filter}");
	include template('etuan:my_order');

}else if(!empty($_G['gp_closeorder'])){
	$id=trim($_G['gp_orderid']);
	DB::update('etuan_order', array('status' => '2'), "buyer_id='$_G[uid]' and id='$orderid'");

	echo 'true';
}else if(!empty($_G['gp_receorder'])){
	$id=trim($_G['gp_orderid']);
	DB::update('etuan_order', array('status' => '7'), "buyer_id='$_G[uid]' and id='$orderid'");

	echo 'true';
}

?>