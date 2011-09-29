<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$op = trim(@$_G['gp_op']);
$id = intval(@$_G['gp_id']);

if($op == 'add') {

	if(submitcheck('addsubmit')) {

		DB::insert('etuan_address', array(
                'buyer_id' => $_G['uid'],
                'province' => $_G['gp_province'],
                'city' => $_G['gp_city'],
                'town' => $_G['gp_town'],
                'address' => $_G['gp_address'],
                'zip' => $_G['gp_zip'],
                'phone' => $_G['gp_phone'],
                'email' => $_G['gp_email'],
                ), true);

		$etuan->ajaxOrMsg('etuan:address_add_success', "plugin.php?id=etuan:my&app=address");
	}else{
		include template('etuan:my_address_add');
	}

}else if($op == 'edit') {
	if(submitcheck('addsubmit')) {
		DB::update('etuan_address', array(
                'province' => $_G['gp_province'],
                'city' => $_G['gp_city'],
                'town' => $_G['gp_town'],
                'address' => $_G['gp_address'],
                'zip' => $_G['gp_zip'],
                'phone' => $_G['gp_phone'],
                'email' => $_G['gp_email'],
                ), "buyer_id=$_G[uid] and id = $id");
		$etuan->ajaxOrMsg('etuan:address_edit_success', "plugin.php?id=etuan:my&app=address");
	}else{
		$row = DB::fetch_first("SELECT * FROM ".DB::table('etuan_address')." where buyer_id=$_G[uid] and id = '$id'");
		include template('etuan:my_address_add');
	}

}else if($op == 'delete') {
	DB::delete('etuan_address', "buyer_id=$_G[uid] and id = $id");
	$etuan->ajaxOrMsg('etuan:address_delete_success', "plugin.php?id=etuan:my&app=address");
}else{

	$page = max(1, intval($_G['page']));
	$pagenum = 10;
	$limit_start = $pagenum * ($page - 1);
	$totalnum = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_address')." where buyer_id='$_G[uid]'");

	$list = array();
	$query = DB::query("SELECT * FROM ".DB::table('etuan_address')." where buyer_id='$_G[uid]' Order By id DESC");

	while($row = DB::fetch($query)){
		$list[] = $row;
	}

	$multi = multi($totalnum, $pagenum, $page, "plugin.php?id=etuan:my&app=address");

	include template('etuan:my_address');
}

?>