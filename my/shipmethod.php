<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$op = trim(@$_G['gp_op']);
$shipmethodid = intval(@$_G['gp_shipmethodid']);

switch($op){
    case 'delete':
        DB::delete('etuan_shipmethod', "seller_id={$_G['uid']} and id={$shipmethodid}");
        $etuan->ajaxOrMsg('etuan:shipmethod_add_success', "plugin.php?id=etuan:my&app=shipmethod");
        break;

    case 'add':
        if(submitcheck('addsubmit')){
            DB::insert('etuan_shipmethod', array(
                            'seller_id' => $_G['uid'],
                            'is_enabled' => $_G['gp_is_enabled'],
                            'name' => $_G['gp_name'],
                            'first_unit' => $_G['gp_first_unit'],
                            'first_price' => $_G['gp_first_price'],
                            'continue_unit' => $_G['gp_continue_unit'],
                            'continue_price' => $_G['gp_continue_price'],
                            'intro' => $_G['gp_intro'],
                        ), true);

            $etuan->ajaxOrMsg('etuan:shipmethod_add_success', "plugin.php?id=etuan:my&app=shipmethod");
        }else{
            include template('etuan:my_shipmethod_add');
        }
        break;
    case 'edit':
        if(submitcheck('addsubmit')) {
            DB::update('etuan_shipmethod', array(
                                    'seller_id' => $_G['uid'],
                                    'is_enabled' => $_G['gp_is_enabled'],
                                    'name' => $_G['gp_name'],
                                    'first_unit' => $_G['gp_first_unit'],
                                    'first_price' => $_G['gp_first_price'],
                                    'continue_unit' => $_G['gp_continue_unit'],
                                    'continue_price' => $_G['gp_continue_price'],
                                    'intro' => $_G['gp_intro'],
                                    ),
                                    "seller_id={$_G['uid']} and id={$shipmethodid}");
            $etuan->ajaxOrMsg('etuan:shipmethod_edit_success', "plugin.php?id=etuan:my&app=shipmethod");
        }else{
            $row = DB::fetch_first("SELECT * FROM ".DB::table('etuan_shipmethod')." where seller_id={$_G['uid']} and id={$shipmethodid}");
            include template('etuan:my_shipmethod_add');
        }
        break;

    case 'list':
    default:
        $extra_sql  ='';
        $page = max(1, intval($_G['page']));
        $limit = 10;
        $offset = $limit * ($page - 1);
        $totalnum = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_shipmethod')." where seller_id={$_G['uid']} $extra_sql");

        $list = array();
        $query = DB::query("SELECT * FROM ".DB::table('etuan_shipmethod')." where seller_id={$_G['uid']} $extra_sql LIMIT {$offset},{$limit}");

        while($row = DB::fetch($query)){
            $list[] = $row;
        }
        $multi = multi($totalnum, $limit, $page, "plugin.php?id=etuan:my&app=shipmethod");
        include template('etuan:my_shipmethod');
        break;
}
