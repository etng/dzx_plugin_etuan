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
                'address' => $_G['gp_address'],
                'community_id' => $_G['gp_community_id'],
                'phone' => $_G['gp_phone'],
                'email' => $_G['gp_email'],
               'contact_name' => $_G['gp_contact_name'],
                ), true);
		$etuan->ajaxOrMsg('etuan:address_add_success', "plugin.php?id=etuan:my&app=address");
	}else{
        require_once libfile('function/group');
		$groupselect = get_groupselect(0, 0, 0);
		include template('etuan:my_address_add');
	}

}else if($op == 'edit') {
	if(submitcheck('addsubmit')) {
		DB::update('etuan_address', array(
                'address' => $_G['gp_address'],
                'community_id' => $_G['gp_community_id'],
                'phone' => $_G['gp_phone'],
                'email' => $_G['gp_email'],
                'contact_name' => $_G['gp_contact_name'],
                ), "buyer_id=$_G[uid] and id = $id");
		$etuan->ajaxOrMsg('etuan:address_edit_success', "plugin.php?id=etuan:my&app=address");
	}else{
		$row = DB::fetch_first("SELECT * FROM ".DB::table('etuan_address')." where buyer_id=$_G[uid] and id = '$id'");
        require_once libfile('function/group');
		$groupselect = get_groupselect(0, 0, 0);
        include template('etuan:my_address_add');
	}

}else if($op == 'delete') {
	DB::delete('etuan_address', "buyer_id=$_G[uid] and id = $id");
	$etuan->ajaxOrMsg('etuan:address_delete_success', "plugin.php?id=etuan:my&app=address");
}else if($op == 'ajax') {
	include template('common/header_ajax');
    $sub_op = trim(@$_G['gp_subop']);
	include template('etuan:ajax_address_group2');
	include template('common/footer_ajax');
	dexit();
}else{

	$page = max(1, intval($_G['page']));
	$pagenum = 10;
	$limit_start = $pagenum * ($page - 1);
	$totalnum = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_address')." where buyer_id='$_G[uid]'");
    $sql = "SELECT * FROM ".DB::table('etuan_address')." where buyer_id='$_G[uid]' Order By id DESC";
	$list = $etuan->fetchAll($sql);
	$multi = multi($totalnum, $pagenum, $page, "plugin.php?id=etuan:my&app=address");
	include template('etuan:my_address');
}

?>