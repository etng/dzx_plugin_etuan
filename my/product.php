<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$op = trim(@$_G['gp_op']);
$productid = intval(@$_G['gp_productid']);

switch($op){
    case 'delete':
        DB::delete('etuan_product', "seller_id={$_G['uid']} and id={$productid}");
        $etuan->ajaxOrMsg('etuan:product_add_success', "plugin.php?id=etuan:my&app=product");
        break;

    case 'add':
        if(submitcheck('addsubmit')){
            DB::insert('etuan_product', array(
                                        'seller_id' => $_G['uid'],
                                        'supplier_id' => $_G['gp_supplier_id'],
                                        'name' => $_G['gp_name'],
                                        'market_price' => $_G['gp_market_price'],
                                        'supply_price' => $_G['gp_supply_price'],
                                        'price' => $_G['gp_price'],
                                        'spec' => $_G['gp_spec'],
                                        'unit_name' => $_G['gp_unit_name'],
                                        'photo' => $_G['gp_photo'],
                                        'description' => $_G['gp_description'],
                                        'credit_limit' => $_G['gp_credit_limit'],
                        ), true);

            $etuan->ajaxOrMsg('etuan:product_add_success', "plugin.php?id=etuan:my&app=product");
        }else{
            $suppliers = $etuan->fetchAll('etuan_supplier', array("seller_id={$_G['uid']}"));
            $row = array(
                'credit_limit'=>0,
            );
            include template('etuan:my_product_add');
        }
        break;
    case 'edit':
        if(submitcheck('addsubmit')) {
            DB::update('etuan_product', array(
                                    'seller_id' => $_G['uid'],
                                    'supplier_id' => $_G['gp_supplier_id'],
                                    'name' => $_G['gp_name'],
                                    'market_price' => $_G['gp_market_price'],
                                    'supply_price' => $_G['gp_supply_price'],
                                    'price' => $_G['gp_price'],
                                    'spec' => $_G['gp_spec'],
                                    'unit_name' => $_G['gp_unit_name'],
                                    'photo' => $_G['gp_photo'],
                                    'description' => $_G['gp_description'],
                                    'credit_limit' => $_G['gp_credit_limit'],
                                    ),
                                    "seller_id={$_G['uid']} and id={$productid}");
            $etuan->ajaxOrMsg('etuan:product_edit_success', "plugin.php?id=etuan:my&app=product");
        }else{
            $row = DB::fetch_first("SELECT * FROM ".DB::table('etuan_product')." where seller_id={$_G['uid']} and id={$productid}");
            $suppliers = $etuan->fetchAll('etuan_supplier', array("seller_id={$_G['uid']}"));
            include template('etuan:my_product_add');
        }
        break;

    case 'list':
    default:
        $extra_sql  ='';
        $page = max(1, intval($_G['page']));
        $limit = 10;
        $offset = $limit * ($page - 1);
        $totalnum = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_product')." where seller_id={$_G['uid']} $extra_sql");

        $list = array();
        $query = DB::query("SELECT * FROM ".DB::table('etuan_product')." where seller_id={$_G['uid']} $extra_sql LIMIT {$offset},{$limit}");

        while($row = DB::fetch($query)){
            $row['supplier'] = $etuan->fetchSupplier($row['supplier_id']);
            $list[] = $row;
        }
        $multi = multi($totalnum, $limit, $page, "plugin.php?id=etuan:my&app=product");
        include template('etuan:my_product');
        break;
}
