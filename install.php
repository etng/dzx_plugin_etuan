<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once DISCUZ_ROOT.'./config/config_ucenter.php';
$sql = file_get_contents(dirname(__file__) . '/schema.sql');
runquery($sql);
$finish = TRUE;
?>