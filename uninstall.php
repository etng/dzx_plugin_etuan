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
$sql = <<<EOF

DROP TABLE IF EXISTS `pre_etuan_address`;
DROP TABLE IF EXISTS `pre_etuan_order`;
DROP TABLE IF EXISTS `pre_etuan_order_product`;
DROP TABLE IF EXISTS `pre_etuan_payment`;
DROP TABLE IF EXISTS `pre_etuan_product`;
DROP TABLE IF EXISTS `pre_etuan_shipmethod`;
DROP TABLE IF EXISTS `pre_etuan_supplier`;
DROP TABLE IF EXISTS `pre_etuan_tuan`;
DROP TABLE IF EXISTS `pre_etuan_tuan_product`;

EOF;

runquery($sql);

$finish = TRUE;

?>