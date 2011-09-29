<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$op = trim($_G['gp_op']);
switch($op){
    case 'delete':
        $tid = intval($_G['gp_tid']);
        DB::delete('etuan_tuan', "seller_id={$_G['uid']} and tid={$tid}");
        if($_G['inajax']) {
            echo 'true';
        }else
        {
            showmessage('etuan:tuan_delete_success', "plugin.php?id=etuan:my&app=tuan", array(), array('showdialog' => 1, 'closetime' => true));
        }
        break;
    case 'view':
        $tid = intval($_G['gp_tid']);
        $tuan_thread = DB::fetch_first("SELECT *,tuan.id as tuan_id FROM ".DB::table('etuan_tuan')." as tuan
        LEFT JOIN ".DB::table('forum_thread')." as thread ON tuan.tid=thread.tid
        where tuan.tid={$tid}");
        $tuan_orders = array();
        $query = DB::query("SELECT * FROM ".DB::table('etuan_order')."
        where tuan_id={$tuan_thread['tuan_id']}");
        while($row = DB::fetch($query)){
            $row['items'] = array();
            $item_query = DB::query("SELECT * FROM ".DB::table('etuan_order_product')."
        where order_id={$row['id']}");
            while($item_row = DB::fetch($item_query)){
                $row['items'][] = $item_row;
            }
            $tuan_orders[] = $row;
        }
		include template('etuan:my_tuan_view');
        break;
    case 'list':
    default:
        $extra_sql = array("tuan.seller_id = '$_G[uid]'");

        $filter = trim($_G['gp_filter']);
        $filter = in_array($filter, array('all', 'ing', 'over')) ? $filter : 'all';
        if($filter!=='all')
        {
            $extra_sql []= "tuan.status ='{$filter}'";
        }

        $extra_sql = implode(' and ', $extra_sql);

        $page = max(1, intval($_G['page']));
        $limit = 5;
        $offset = $limit * ($page - 1);
        $total = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_tuan')." as tuan where $extra_sql");

        $tuan_threads = array();
        $query = DB::query("SELECT * FROM ".DB::table('etuan_tuan')." as tuan
        LEFT JOIN ".DB::table('forum_thread')." as thread ON tuan.tid=thread.tid
        where $extra_sql ORDER BY thread.dateline DESC LIMIT $offset,$limit");
        while($row = DB::fetch($query)){
            $tuan_threads[] = $row;
        }
        $multi = multi($total, $limit, $page, "plugin.php?id=etuan:my&app=tuan&filter={$filter}");

        include template('etuan:my_tuan');
        break;
}
