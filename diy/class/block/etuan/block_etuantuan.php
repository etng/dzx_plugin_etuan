<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: block_etuantuan.php 19683 2011-01-13 10:03:19Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_etuantuan {

	var $setting = array();

	function block_etuantuan() {
		$this->setting = array(
			'tids' => array(
				'title' => 'etuan_tids',
				'type' => 'text'
			),
			'uids' => array(
				'title' => 'etuan_uids',
				'type' => 'text'
			),
			'keyword' => array(
				'title' => 'etuan_keyword',
				'type' => 'text'
			),
			'fids'	=> array(
				'title' => 'etuan_fids',
				'type' => 'mselect',
				'value' => array()
			),
			'viewmod' => array(
				'title' => 'threadlist_viewmod',
				'type' => 'radio'
			),
			'digest' => array(
				'title' => 'etuan_digest',
				'type' => 'mcheckbox',
				'value' => array(
					array(1, 'etuan_digest_1'),
					array(2, 'etuan_digest_2'),
					array(3, 'etuan_digest_3'),
					array(0, 'etuan_digest_0')
				),
			),
			'stick' => array(
				'title' => 'etuan_stick',
				'type' => 'mcheckbox',
				'value' => array(
					array(1, 'etuan_stick_1'),
					array(2, 'etuan_stick_2'),
					array(3, 'etuan_stick_3'),
					array(0, 'etuan_stick_0')
				),
			),
			'recommend' => array(
				'title' => 'etuan_recommend',
				'type' => 'radio'
			),
			'orderby' => array(
				'title' => 'etuan_orderby',
				'type'=> 'mradio',
				'value' => array(
					array('dateline', 'etuan_orderby_dateline'),
					array('todayhots', 'etuan_orderby_todayhots'),
					array('weekhots', 'etuan_orderby_weekhots'),
					array('monthhots', 'etuan_orderby_monthhots'),
				),
				'default' => 'dateline'
			),
			'highlight' => array(
				'title' => 'etuan_highlight',
				'type' => 'radio',
				'default' => 0,
			),
			'titlelength' => array(
				'title' => 'etuan_titlelength',
				'type' => 'text',
				'default' => 40
			),
			'summarylength' => array(
				'title' => 'etuan_summarylength',
				'type' => 'text',
				'default' => 80
			),
			'startrow' => array(
				'title' => 'etuan_startrow',
				'type' => 'text',
				'default' => 0
			),
		);
	}

	function name() {
		return lang('block/etuan', 'etuan_script_tuan');
	}

	function blockclass() {
		return array('othertuan', lang('block/etuan', 'etuan_tuan'));
	}

	function fields() {
		return array(
					'url' => array('name' => lang('blockclass', 'blockclass_other_tuan_field_url'), 'formtype' => 'text', 'datatype' => 'string'),
					'title' => array('name' => lang('blockclass', 'blockclass_other_tuan_field_title'), 'formtype' => 'title', 'datatype' => 'title'),
					'pic' => array('name' => lang('blockclass', 'blockclass_other_tuan_field_pic'), 'formtype' => 'pic', 'datatype' => 'pic'),
					'summary' => array('name' => lang('blockclass', 'blockclass_other_tuan_field_summary'), 'formtype' => 'summary', 'datatype' => 'summary'),
				);
	}

	function getsetting() {
		return $this->setting;
	}

	function getdata($style, $parameter) {

		$titlelength = isset($parameter['titlelength']) ? intval($parameter['titlelength']) : 40;
		$summarylength = isset($parameter['summarylength']) ? intval($parameter['summarylength']) : 80;
		$type = !empty($parameter['type']) && is_array($parameter['type']) ? $parameter['type'] : array();
		$b = '0000';
		for($i=1;$i<=4;$i++) {
			if(in_array($i, $type)) {
				$b[$i-1] = '1';
			}
		}
		$type = intval($b, '2');
		$list = array();
		$query = DB::query('SELECT * FROM '.DB::table('common_tuan')."  WHERE (`type` & '$type' > 0) ORDER BY displayorder");
		while($data = DB::fetch($query)) {
			$list[] = array(
				'id' => $data['id'],
				'idtype' => 'flid',
				'title' => cutstr($data['name'], $titlelength),
				'url' => $data['url'],
				'pic' => $data['logo'] ? $data['logo'] : $_G['style']['imgdir'].'/nophoto.gif',
				'picflag' => '0',
				'summary' => $data['description'],
				'fields' => array(
					'fulltitle' => $data['name'],
				)
			);
		}
		return array('html' => '', 'data' => $list);
	}
}



?>