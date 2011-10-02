<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(0){
    require_once libfile('function/post');
    require_once libfile('function/delete');
    $tids = array();
    $query = DB::query("SELECT tid FROM ".DB::table('etuan_tuan')."");
    while($row = DB::fetch($query)) {
        $tids[] = $row['tid'];
    }
    deletethread($tids, true, true);
}
$tables = $etuan->fetchCol("show tables like '".DB::table('etuan_')."%'");
foreach($tables as $table)
{
    runquery(sprintf('DROP TABLE IF EXISTS `%s`;', $table));
}
$finish = TRUE;

?>