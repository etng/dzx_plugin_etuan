<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
showtableheader();
showtips(lang('plugin/etuan', 'admin_community_tips', array()) ,'tips' ,true, lang('plugin/etuan', 'admin_community'));
showtablefooter();


$op = trim($_G['gp_op']);
$communityid = intval($_G['gp_communityid']);

if(empty($op)) {

	showformheader("plugins&operation=config&do={$pluginid}&identifier=etuan&pmod=admin_community&op=delete", 'community_submit');
	showtableheader();

	showsubtitle(array(
        '',
        'display_order',
        lang('plugin/etuan', 'admin_community_name'),
        lang('plugin/etuan', 'admin_community_contact_name'),
        lang('plugin/etuan', 'admin_community_contact_address'),
        lang('plugin/etuan', 'admin_community_contact_phone'),
        'operation'
    ));

	$query = DB::query("SELECT * FROM ".DB::table('etuan_community')." ORDER BY displayorder ASC");
	while($row = DB::fetch($query)) {
		showtablerow('', array('class="td25"', 'class="td25"', '', '', '',''), array(
						"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$row[id]\">",
						"<input type=\"text\" class=\"txt\" name=\"displayorder[$row[id]]\" value=\"$row[displayorder]\">",
						$row['name'],
						$row['contact_name'],
						$row['contact_address'],
						$row['contact_phone'],
						"<a href=\"".ADMINSCRIPT."?action=plugins&operation=config&do={$pluginid}&identifier=etuan&pmod=admin_community&op=edit&communityid={$row[id]}\">".cplang('edit')."</a>&nbsp;&nbsp;<a href=\"".ADMINSCRIPT."?action=plugins&operation=config&do={$pluginid}&identifier=etuan&pmod=admin_community&op=delete&delete[]={$row[id]}\">".cplang('delete')."</a>"
					));
	}
	showtablerow('', array('class="td25"', 'colspan="5"'), array(
						'',
						'<a class="addtr" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=etuan&pmod=admin_community&app='.$app.'&op=add">'.lang('plugin/etuan', 'admin_community_add').'</a>'
					));
	showsubmit('delsubmit','submit', 'del');
	showtablefooter();
	showformfooter();

}else if($op=='add' || $op=='edit'){
	$_G['gp_memo'] = dhtmlspecialchars(trim($_G['gp_memo']));
	if(submitcheck('addsubmit')) {

		DB::insert('etuan_community', array(
            'name' => $_G['gp_community_name'],
            'contact_name' => $_G['gp_community_contact_name'],
            'contact_phone' => $_G['gp_community_contact_phone'],
            'contact_address' => $_G['gp_community_contact_address'],
            'memo' => $_G['gp_community_memo'],
        ));
		cpmsg('etuan:admin_community_add_succeed', "action=plugins&operation=config&do={$pluginid}&identifier=etuan&pmod=admin_community", 'succeed');

	}else if(submitcheck('editsubmit')) {

		DB::update('etuan_community', array(
            'name' => $_G['gp_community_name'],
            'contact_name' => $_G['gp_community_contact_name'],
            'contact_phone' => $_G['gp_community_contact_phone'],
            'contact_address' => $_G['gp_community_contact_address'],
            'memo' => $_G['gp_community_memo'],
            ), "id='$communityid'");
		cpmsg('etuan:admin_community_edit_succeed', "action=plugins&operation=config&do={$pluginid}&identifier=etuan&pmod=admin_community", 'succeed');

	}else{
		showformheader("plugins&operation=config&do={$pluginid}&identifier=etuan&pmod=admin_community&op=add", 'addsubmit');
		showtableheader();
		if($op=='edit'){
			showhiddenfields(array('communityid' => $communityid));
			$row = DB::fetch_first($sql="SELECT * FROM ".DB::table('etuan_community')." WHERE id='$communityid'");
            var_dump($sql,$row);
		}

		showsetting(lang('plugin/etuan', 'admin_community_name'), 'community_name', $row['name'], 'text' );
        showsetting(lang('plugin/etuan', 'admin_community_contact_name'), 'community_contact_name', $row['contact_name'], 'text' );
        showsetting(lang('plugin/etuan', 'admin_community_contact_address'), 'community_contact_address', $row['contact_address'], 'text' );

		showsetting(lang('plugin/etuan', 'admin_community_contact_phone'), 'community_contact_phone', $row['contact_phone'], 'text' );
		showsetting(lang('plugin/etuan', 'admin_community_memo'), 'community_memo', $row['memo'], 'textarea');

		if($op=='edit'){
			showsubmit('editsubmit', 'submit', '');
		}else{
			showsubmit('addsubmit', 'submit', '');
		}
		showtablefooter();
		showformfooter();
	}

}else if($op=='delete'){
	//删除
	if($ids = dimplode($_G['gp_delete'])) {
		DB::delete('etuan_community', "id IN ($ids)");
	}
	//更新
	if(is_array($_G['gp_displayorder']))  {
		foreach($_G['gp_displayorder'] as $k => $displayorder) {
			DB::update('etuan_community',array('displayorder' => $displayorder), "id='$k'");
		}
	}
	cpmsg('etuan:admin_community_update_succeed', "action=plugins&operation=config&do={$pluginid}&identifier=etuan&pmod=admin_community", 'succeed');

}