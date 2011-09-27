<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(0){
    require_once libfile('function/post');
    require_once libfile('function/delete');
    $tids = array();
    $query = DB::query("SELECT tid FROM ".DB::table('etuan')."");
    while($row = DB::fetch($query)) {
        $tids[] = $row['tid'];
    }
    deletethread($tids, true, true);
}
$sql = <<<EOF

---- DROP TABLE IF EXISTS cdb_etuan;

EOF;

runquery($sql);

$finish = TRUE;

?>