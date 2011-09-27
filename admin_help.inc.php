<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$V_INFO['identifier'] = trim($_G['gp_identifier']);
$V_INFO['version'] = $_G['setting']['plugins']['version'][$V_INFO['identifier']];
$lang_cate = 'plugin/'.$V_INFO['identifier'];
showtableheader();
showtips(lang($lang_cate, 'admin_help_check_desc', array('version' => $V_INFO['version'])) ,'version' ,true, lang($lang_cate, 'admin_help_check'));
showtips(lang($lang_cate, 'admin_help_faq_desc'),'version' ,true, lang($lang_cate, 'admin_help_faq'));
showtablefooter();
?>