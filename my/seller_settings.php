<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

	if(isset($_G['gp_ship_fee_pick'])) {
		DB::update('etuan_seller', array(
            'ship_fee_deliver' =>$_G['gp_ship_fee_deliver'],
            'ship_fee_pick' =>$_G['gp_ship_fee_pick'],
            'ship_only_pick' =>$_G['gp_ship_only_pick'],
        ), "uid=$_G[uid]");
		$etuan->ajaxOrMsg('etuan:seller_settings_update_success', "plugin.php?id=etuan:my&app=settings");
	}else{
		$settings = DB::fetch_first("SELECT * FROM ".DB::table('etuan_seller')." where uid=$_G[uid]");
        if(!$settings)
        {
            DB::insert('etuan_seller', array(
            'uid' => $_G['uid'],
            'ship_fee_deliver' =>0,
            'ship_fee_pick' =>0,
            'ship_only_pick' =>false,
            ), true);
            $settings = DB::fetch_first("SELECT * FROM ".DB::table('etuan_seller')." where uid=$_G[uid]");
        }
		include template('etuan:my_seller_settings');
	}

?>