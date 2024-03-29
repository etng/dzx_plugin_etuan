<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$op = trim(@$_G['gp_op']);
$supplierid = intval(@$_G['gp_supplierid']);

switch($op){
    case 'delete':
        DB::delete('etuan_supplier', "seller_id={$_G['uid']} and id={$supplierid}");
        $etuan->ajaxOrMsg('etuan:supplier_add_success', "plugin.php?id=etuan:my&app=supplier");
        break;

    case 'add':
        if(submitcheck('addsubmit')){
            $etuan->upload('logo');
            DB::insert('etuan_supplier', array(
                                'seller_id' => $_G['uid'],
                                'name' => $_G['gp_name'],
                                'phone' => $_G['gp_phone'],
                                'address' => $_G['gp_address'],
                                'logo' => $_G['gp_logo'],
                                'contact_name' => $_G['gp_contact_name'],
                                'contact_gender' => $_G['gp_contact_gender'],
                                'contact_phone' => $_G['gp_contact_phone'],
                                'contact_qq' => $_G['gp_contact_qq'],
                                'cate' => $_G['gp_cate'],
                                'website' => $_G['gp_website'],
                                'tax_license' => $_G['gp_tax_license'],
                                'speed_rating' => $_G['gp_speed_rating'],
                                'quality_rating' => $_G['gp_quality_rating'],
                                'service_rating' => $_G['gp_service_rating'],
                                'memo' => $_G['gp_memo'],
                        ), true);
            $etuan->ajaxOrMsg('etuan:supplier_add_success', "plugin.php?id=etuan:my&app=supplier");
        }else{
            include template('etuan:my_supplier_add');
        }
        break;
    case 'edit':
        if(submitcheck('addsubmit')) {
            $etuan->upload('logo');
            DB::update('etuan_supplier', array(
                                    'seller_id' => $_G['uid'],
                                    'name' => $_G['gp_name'],
                                    'phone' => $_G['gp_phone'],
                                    'address' => $_G['gp_address'],
                                    'logo' => $_G['gp_logo'],
                                    'contact_name' => $_G['gp_contact_name'],
                                    'second_contact_name' => $_G['gp_second_contact_name'],
                                    'contact_gender' => $_G['gp_contact_gender'],
                                    'contact_phone' => $_G['gp_contact_phone'],
                                    'second_contact_phone' => $_G['gp_second_contact_phone'],
                                    'contact_qq' => $_G['gp_contact_qq'],
                                    'cate' => $_G['gp_cate'],
                                    'website' => $_G['gp_website'],
                                    'tax_license' => $_G['gp_tax_license'],
                                    'speed_rating' => $_G['gp_speed_rating'],
                                    'quality_rating' => $_G['gp_quality_rating'],
                                    'service_rating' => $_G['gp_service_rating'],
                                    'memo' => $_G['gp_memo'],
                                    ),
                                    "seller_id={$_G['uid']} and id={$supplierid}");
            $etuan->ajaxOrMsg('etuan:supplier_edit_success', "plugin.php?id=etuan:my&app=supplier");
        }else{
            $row = DB::fetch_first("SELECT * FROM ".DB::table('etuan_supplier')." where seller_id={$_G['uid']} and id={$supplierid}");
            include template('etuan:my_supplier_add');
        }
        break;
    case 'view':
        $supplier = DB::fetch_first("SELECT * FROM ".DB::table('etuan_supplier')." where seller_id={$_G['uid']} and id={$supplierid}");
        include template('etuan:my_supplier_view');
     break;
    case 'list':
    default:
        $extra_sql  ='';
        $page = max(1, intval($_G['page']));
        $limit = 10;
        $offset = $limit * ($page - 1);
        $totalnum = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_supplier')." where seller_id={$_G['uid']} $extra_sql");

        $list = array();
        $query = DB::query("SELECT * FROM ".DB::table('etuan_supplier')." where seller_id={$_G['uid']} $extra_sql order by id desc LIMIT {$offset},{$limit}");

        while($row = DB::fetch($query)){
            $list[] = $row;
        }
        $multi = multi($totalnum, $limit, $page, "plugin.php?id=etuan:my&app=supplier");
        include template('etuan:my_supplier');
        break;
}
