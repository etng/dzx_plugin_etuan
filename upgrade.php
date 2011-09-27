<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if (version_compare($fromversion, '0.1', '<')) {
    //升级到0.1


    $cur_version='0.1';
}
if (version_compare($fromversion, '0.2', '<')) {
    //升级到0.2


    $cur_version='0.2';
}
if($toversion = $cur_version)
{
    $finish = TRUE;
}