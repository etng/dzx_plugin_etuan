<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$op = trim(@$_G['gp_op']);
$paymentid = intval(@$_G['gp_paymentid']);

switch($op){
    case 'delete':
        DB::delete('etuan_payment', "seller_id={$_G['uid']} and id={$paymentid}");
        $etuan->ajaxOrMsg('etuan:payment_delete_success', "plugin.php?id=etuan:my&app=payment");
        break;

    case 'add':
        if(submitcheck('addsubmit')){
            DB::insert('etuan_payment', array(
                                        'seller_id' => $_G['uid'],
                                        'alipay_partener_id' => $_G['gp_alipay_partener_id'],
                                        'alipay_key' => $_G['gp_alipay_key'],
                                        'alipay_account' => $_G['gp_alipay_account'],
                                        'alipay_api_type' => $_G['gp_alipay_api_type'],
                                        'tenpay_account' => $_G['gp_tenpay_account'],
                                        'tenpay_key' => $_G['gp_tenpay_key'],
                        ), true);

            $etuan->ajaxOrMsg('etuan:payment_add_success', "plugin.php?id=etuan:my&app=payment");
        }else{
            include template('etuan:my_payment_add');
        }
        break;
    case 'edit':
        if(submitcheck('addsubmit')) {
            DB::update('etuan_payment', array(
                                    'seller_id' => $_G['uid'],
                                    'alipay_partener_id' => $_G['gp_alipay_partener_id'],
                                    'alipay_key' => $_G['gp_alipay_key'],
                                    'alipay_account' => $_G['gp_alipay_account'],
                                    'alipay_api_type' => $_G['gp_alipay_api_type'],
                                    'tenpay_account' => $_G['gp_tenpay_account'],
                                    'tenpay_key' => $_G['gp_tenpay_key'],
                                    ),
                                    "seller_id={$_G['uid']} and id={$paymentid}");
            $etuan->ajaxOrMsg('etuan:payment_edit_success', "plugin.php?id=etuan:my&app=payment");
        }else{
            $row = DB::fetch_first("SELECT * FROM ".DB::table('etuan_payment')." where seller_id={$_G['uid']} and id={$paymentid}");
            include template('etuan:my_payment_add');
        }
        break;

    case 'list':
    default:
        $extra_sql  ='';
        $page = max(1, intval($_G['page']));
        $limit = 10;
        $offset = $limit * ($page - 1);
        $totalnum = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_payment')." where seller_id={$_G['uid']} $extra_sql");

        $list = array();
        $query = DB::query("SELECT * FROM ".DB::table('etuan_payment')." where seller_id={$_G['uid']} $extra_sql LIMIT {$offset},{$limit}");

        while($row = DB::fetch($query)){
            $list[] = $row;
        }
        $multi = multi($totalnum, $limit, $page, "plugin.php?id=etuan:my&app=payment");
        include template('etuan:my_payment');
        break;
}
