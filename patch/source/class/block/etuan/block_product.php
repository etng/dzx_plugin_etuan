<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class block_product {
	var $setting = array();

	function block_product(){
		$this->setting = array(
			'tids' => array(
				'title' => 'etuan_product_tids',
				'type' => 'text'
			),
			'uids' => array(
				'title' => 'etuan_product_uids',
				'type' => 'text'
			),
			'keyword' => array(
				'title' => 'etuan_product_keyword',
				'type' => 'text'
			),
			'fids'	=> array(
				'title' => 'etuan_product_fids',
				'type' => 'mselect',
				'value' => array()
			),
			'viewmod' => array(
				'title' => 'threadlist_viewmod',
				'type' => 'radio'
			),
			'digest' => array(
				'title' => 'etuan_product_digest',
				'type' => 'mcheckbox',
				'value' => array(
					array(1, 'etuan_product_digest_1'),
					array(2, 'etuan_product_digest_2'),
					array(3, 'etuan_product_digest_3'),
					array(0, 'etuan_product_digest_0')
				),
			),
			'stick' => array(
				'title' => 'etuan_product_stick',
				'type' => 'mcheckbox',
				'value' => array(
					array(1, 'etuan_product_stick_1'),
					array(2, 'etuan_product_stick_2'),
					array(3, 'etuan_product_stick_3'),
					array(0, 'etuan_product_stick_0')
				),
			),
			'recommend' => array(
				'title' => 'etuan_product_recommend',
				'type' => 'radio'
			),
			'orderby' => array(
				'title' => 'etuan_product_orderby',
				'type'=> 'mradio',
				'value' => array(
					array('tu.begin_date', 'etuan_product_orderby_dateline'),
					array('todayhots', 'etuan_product_orderby_todayhots'),
					array('weekhots', 'etuan_product_orderby_weekhots'),
					array('monthhots', 'etuan_product_orderby_monthhots'),
				),
				'default' => 'tu.begin_date'
			),
			'highlight' => array(
				'title' => 'etuan_product_highlight',
				'type' => 'radio',
				'default' => 0,
			),
			'titlelength' => array(
				'title' => 'etuan_product_titlelength',
				'type' => 'text',
				'default' => 40
			),
			'summarylength' => array(
				'title' => 'etuan_product_summarylength',
				'type' => 'text',
				'default' => 80
			),
			'startrow' => array(
				'title' => 'etuan_product_startrow',
				'type' => 'text',
				'default' => 0
			),
		);
	}

	function name() {
		return lang('block/etuan',  'etuan_script_product');
	}

	function blockclass() {
		return array('product', lang('block/etuan',  'etuan_tuan'));
	}

	function fields() {
		return array(
					'url' => array('name' => lang('block/etuan',  'etuan_product_field_url'), 'formtype' => 'text', 'datatype' => 'string'),
					'title' => array('name' => lang('block/etuan',  'etuan_product_field_title'), 'formtype' => 'title', 'datatype' => 'title'),
					'pic' => array('name' => lang('block/etuan',  'etuan_product_field_pic'), 'formtype' => 'pic', 'datatype' => 'pic'),
					'summary' => array('name' => lang('block/etuan',  'etuan_product_field_summary'), 'formtype' => 'summary', 'datatype' => 'summary'),
					'totalitems' => array('name' => lang('block/etuan',  'etuan_product_field_totalitems'), 'formtype' => 'text', 'datatype' => 'int'),
					'author' => array('name' => lang('block/etuan',  'etuan_product_field_author'), 'formtype' => 'text', 'datatype' => 'text'),
					'authorid' => array('name' => lang('block/etuan',  'etuan_product_field_authorid'), 'formtype' => 'text', 'datatype' => 'int'),
					'price' => array('name' => lang('block/etuan',  'etuan_product_field_price'), 'formtype' => 'text', 'datatype' => 'text'),
				);
	}

	function fieldsconvert() {
		return array(
//				'group_product' => array(
//					'name' => lang('block/etuan',  'group_product'),
//					'script' => 'groupproduct',
//					'searchkeys' => array(),
//					'replacekeys' => array(),
//				),
			);
	}

	function getsetting() {
		global $_G;
		$settings = $this->setting;

		if($settings['fids']) {
			loadcache('forums');
			$settings['fids']['value'][] = array(0, lang('portalcp', 'block_all_forum'));
			foreach($_G['cache']['forums'] as $fid => $forum) {
				$settings['fids']['value'][] = array($fid, ($forum['type'] == 'forum' ? str_repeat('&nbsp;', 4) : ($forum['type'] == 'sub' ? str_repeat('&nbsp;', 8) : '')).$forum['name']);
			}
		}
		return $settings;
	}

	function cookparameter($parameter) {
		return $parameter;
	}

	function getdata($style, $parameter) {
		global $_G;

		$parameter = $this->cookparameter($parameter);

		loadcache('forums');
		$tids		= !empty($parameter['tids']) ? explode(',', $parameter['tids']) : array();
		$uids		= !empty($parameter['uids']) ? explode(',', $parameter['uids']) : array();
		$startrow	= isset($parameter['startrow']) ? intval($parameter['startrow']) : 0;
		$items		= isset($parameter['items']) ? intval($parameter['items']) : 10;
		$digest		= isset($parameter['digest']) ? $parameter['digest'] : 0;
		$stick		= isset($parameter['stick']) ? $parameter['stick'] : 0;
		$orderby	= isset($parameter['orderby']) ? (in_array($parameter['orderby'],array('dateline','todayhots','weekhots','monthhots')) ? $parameter['orderby'] : 'dateline') : 'dateline';
		$titlelength	= !empty($parameter['titlelength']) ? intval($parameter['titlelength']) : 40;
		$summarylength	= !empty($parameter['summarylength']) ? intval($parameter['summarylength']) : 80;
		$recommend	= !empty($parameter['recommend']) ? 1 : 0;
		$keyword	= !empty($parameter['keyword']) ? $parameter['keyword'] : '';
		$viewmod	= !empty($parameter['viewmod']) ? 1 : 0;
		$highlight = !empty($parameter['highlight']) ? 1 : 0;

		$fids = array();
		if(!empty($parameter['fids'])) {
			if($parameter['fids'][0] == '0') {
				unset($parameter['fids'][0]);
			}
			$fids = $parameter['fids'];
		}

		$bannedids = !empty($parameter['bannedids']) ? explode(',', $parameter['bannedids']) : array();

		require_once libfile('function/post');
		require_once libfile('function/search');
        $where = array();
        if($keyword)
        {
            $wherer[]=searchkey($keyword, "p.name LIKE '%{text}%'");
        }
		if(in_array($orderby, array('todayhots','weekhots','monthhots'))) {
			$historytime = 0;
			switch($orderby) {
				case 'todayhots':
					$historytime = mktime(0, 0, 0, date('m', TIMESTAMP), date('d', TIMESTAMP), date('Y', TIMESTAMP));
				break;
				case 'weekhots':
					$week = gmdate('w', TIMESTAMP) - 1;
					$week = $week != -1 ? $week : 6;
					$historytime = mktime(0, 0, 0, date('m', TIMESTAMP), date('d', TIMESTAMP) - $week, date('Y', TIMESTAMP));
				break;
				case 'monthhots':
					$historytime = mktime(0, 0, 0, date('m', TIMESTAMP), 1, date('Y', TIMESTAMP));
				break;
			}
			$where[]= 'tu.begin_date>='.$historytime;
			$orderby = 'tp.sold_cnt';
		}
        if($uids)
        {
		    $where []= 'tu.seller_id IN ('.dimplode($uids).')';
        }
        if($bannedids)
        {
		    $where []= 'p.id NOT IN ('.dimplode($bannedids).')';
        }
        if(!$orderby || $orderby=='dateline')
        {
            $orderby = 'tu.begin_date';
        }
        $where[]="tu.`status`='ing'";
        $where = $where?implode(' AND ', $where):'1=1';
        $datalist = array();
        $query = DB::query($sql="SELECT
                                        p.*,
                                        tp.sold_cnt AS totalitems,
                                        tu.name AS tuan_name,
                                        tu.status AS tuan_status,
                                        tu.tid AS thread_id,
                                        tu.begin_date AS begin_date,
                                        tu.end_date AS end_date,
                                        tp.unit_price AS price,
                                        s.name AS supplier_name
                                FROM " .DB::table('etuan_tuan_product') . " AS tp
                                LEFT JOIN " .DB::table('etuan_tuan') . " AS tu ON tp.tuan_id=tu.id
                                LEFT JOIN " .DB::table('etuan_product') . " AS p ON tp.product_id=p.id
                                LEFT JOIN " .DB::table('etuan_supplier') . " AS s ON p.supplier_id=s.id
                                WHERE {$where}
                                ORDER BY {$orderby} DESC
                                LIMIT $startrow,$items;");
        while($data = DB::fetch($query)) {
            $datalist[]= array(
				'id' => $data['id'],
				'idtype' => 'pid',
				'title' => cutstr(str_replace('\\\'', '&#39;', addslashes($data['name'])), $titlelength, ''),
				'url' => 'forum.php?mod=viewthread&tid='.$data['thread_id'],
				'pic' => ($data['photo'] ? '' : $_G['style']['imgdir'].'/nophoto.gif'),
				'picflag' => '0',
                'fields' => array(
					'fulltitle' => str_replace('\\\'', '&#39;', addslashes($data['name'])),
					'totalitems' => $data['totalitems'],
					'author' => $data['seller'] ? $data['seller'] : $_G['setting']['anonymoustext'],
					'authorid' => $data['seller_id'] ? $data['seller_id'] : 0,
					'price' =>$data['price'],
					'summary' =>$data['description'],
                ),
            );
        }
		return array('html' => '', 'data' => $datalist);
	}
}


?>