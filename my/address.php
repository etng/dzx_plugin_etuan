<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$op = trim(@$_G['gp_op']);
$id = intval(@$_G['gp_addressid']);

function getAddressData($uid)
{
    global $_G;
    $data= array('buyer_id' => $uid,);
    $fields = array('address', 'zip', 'building', 'unit', 'room', 'name', 'mobile', 'phone', 'email', 'memo', 'community_id', 'community_name');
    foreach($fields as $field)
    {
         $data[$field]= $_G['gp_'.$field];
    }
    return $data;
}
if($op == 'add') {
	if(submitcheck('addsubmit')) {
		DB::insert('etuan_address', getAddressData($_G['uid']), true);
		$etuan->ajaxOrMsg('地址添加成功', "plugin.php?id=etuan:my&app=address");
	}else{
		include template('etuan:my_address_add');
	}

}else if($op == 'edit') {
	if(submitcheck('addsubmit')) {
		DB::update('etuan_address', getAddressData($_G['uid']), "buyer_id=$_G[uid] and id = $id");
		$etuan->ajaxOrMsg('地址修改成功', "plugin.php?id=etuan:my&app=address");
	}else{
		$row = DB::fetch_first("SELECT * FROM ".DB::table('etuan_address')." where buyer_id=$_G[uid] and id = '$id'");
        include template('etuan:my_address_add');
	}

}else if($op == 'delete') {
	DB::delete('etuan_address', "buyer_id=$_G[uid] and id = $id");
	$etuan->ajaxOrMsg('地址删除成功', "plugin.php?id=etuan:my&app=address");
}else{

	$page = max(1, intval($_G['page']));
	$pagenum = 10;
	$limit_start = $pagenum * ($page - 1);
	$totalnum = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_address')." where buyer_id='$_G[uid]'");
    $sql = "SELECT a.*,f.name as community_name FROM ".DB::table('etuan_address')." as a left join ".DB::table('forum_forum')." as f on a.community_id=f.fid where a.buyer_id='$_G[uid]' ORDER BY a.id DESC";
	$list = $etuan->fetchAll($sql);
	$multi = multi($totalnum, $pagenum, $page, "plugin.php?id=etuan:my&app=address");
	include template('etuan:my_address');
}

?>