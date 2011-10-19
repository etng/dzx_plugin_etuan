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
					array('dateline', 'etuan_product_orderby_dateline'),
					array('todayhots', 'etuan_product_orderby_todayhots'),
					array('weekhots', 'etuan_product_orderby_weekhots'),
					array('monthhots', 'etuan_product_orderby_monthhots'),
				),
				'default' => 'dateline'
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

//		$datalist = $list = $listpids = $threadpids = $aid2pid = $attachtables = array();
//		$keyword = $keyword ? searchkey($keyword, "t.subject LIKE '%{text}%'") : '';
//		$sql = ($fids ? ' AND t.fid IN ('.dimplode($fids).')' : '')
//			.($tids ? ' AND t.tid IN ('.dimplode($tids).')' : '')
//			.($digest ? ' AND t.digest IN ('.dimplode($digest).')' : '')
//			.($stick ? ' AND t.displayorder IN ('.dimplode($stick).')' : '')
//			." AND t.isgroup='0'";
//		$where = '';
//		if(in_array($orderby, array('todayhots','weekhots','monthhots'))) {
//			$historytime = 0;
//			switch($orderby) {
//				case 'todayhots':
//					$historytime = mktime(0, 0, 0, date('m', TIMESTAMP), date('d', TIMESTAMP), date('Y', TIMESTAMP));
//				break;
//				case 'weekhots':
//					$week = gmdate('w', TIMESTAMP) - 1;
//					$week = $week != -1 ? $week : 6;
//					$historytime = mktime(0, 0, 0, date('m', TIMESTAMP), date('d', TIMESTAMP) - $week, date('Y', TIMESTAMP));
//				break;
//				case 'monthhots':
//					$historytime = mktime(0, 0, 0, date('m', TIMESTAMP), 1, date('Y', TIMESTAMP));
//				break;
//			}
//			$where = ' WHERE tr.dateline>='.$historytime;
//			$orderby = 'totalitems';
//		}
//		$where .= ($uids ? ' AND tr.sellerid IN ('.dimplode($uids).')' : '').$keyword;
//		$where .= ($bannedids ? ' AND tr.pid NOT IN ('.dimplode($bannedids).')' : '');
//		$sqlfrom = " INNER JOIN `".DB::table('forum_thread')."` t ON t.tid=tr.tid $sql AND t.displayorder>='0'";
//		$joinmethod = empty($tids) ? 'INNER' : 'LEFT';
//		if($recommend) {
//			$sqlfrom .= " $joinmethod JOIN `".DB::table('forum_forumrecommend')."` fc ON fc.tid=tr.tid";
//		}
//		$sqlfield = $highlight ? ', t.highlight' : '';
//		$query = DB::query("SELECT tr.pid, tr.tid, tr.aid, tr.price, tr.credit, tr.subject, tr.totalitems, tr.seller, tr.sellerid$sqlfield
//			FROM ".DB::table('forum_product')." tr $sqlfrom $where
//			ORDER BY tr.$orderby DESC
//			LIMIT $startrow,$items;"
//			);
//		require_once libfile('block_thread', 'class/block/forum');
//		$bt = new block_thread();
//		while($data = DB::fetch($query)) {
//			if($style['getsummary']) {
//				$threadpids[$data['posttableid']][] = $data['pid'];
//			}
//			if($data['aid']) {
//				$aid2pid[$data['aid']] = $data['pid'];
//				$attachtable = getattachtableid($data['tid']);
//				$attachtables[$attachtable][] = $data['aid'];
//			}
//			$listpids[] = $data['pid'];
//			$list[$data['pid']] = array(
//				'id' => $data['pid'],
//				'idtype' => 'pid',
//				'title' => cutstr(str_replace('\\\'', '&#39;', addslashes($data['subject'])), $titlelength, ''),
//				'url' => 'forum.php?mod=viewthread&do=productinfo&tid='.$data['tid'].'&pid='.$data['pid'].($viewmod ? '&from=portal' : ''),
//				'pic' => ($data['aid'] ? '' : $_G['style']['imgdir'].'/nophoto.gif'),
//				'picflag' => '0',
//				'fields' => array(
//					'fulltitle' => str_replace('\\\'', '&#39;', addslashes($data['subject'])),
//					'totalitems' => $data['totalitems'],
//					'author' => $data['seller'] ? $data['seller'] : $_G['setting']['anonymoustext'],
//					'authorid' => $data['sellerid'] ? $data['sellerid'] : 0,
//					'price' => ($data['price'] > 0 ? '&yen; '.$data['price'] : '').($data['credit'] > 0 ? ($data['price'] > 0 ? lang('block/productlist', 'etuan_product_price_add') : '').$data['credit'].' '.$_G['setting']['extcredits'][$_G['setting']['creditstransextra'][5]]['unit'].$_G['setting']['extcredits'][$_G['setting']['creditstransextra'][5]]['title'] : ''),
//				)
//			);
//			if($highlight && $data['highlight']) {
//				$list[$data['tid']]['fields']['showstyle'] = $bt->getthreadstyle($data['highlight']);
//			}
//		}
//		if(!empty($listpids)) {
//			foreach($threadpids as $key => $var) {
//				$posttable = $key == 0 ? 'forum_post' : 'forum_post_'.$key;
//				$query = DB::query("SELECT pid, message FROM ".DB::table($posttable)." WHERE pid IN  (".dimplode($var).")");
//				while($result = DB::fetch($query)) {
//					$list[$result['pid']]['summary'] = messagecutstr($result['message'], $messagelength);
//				}
//			}
//
//			foreach($attachtables as $tableid => $taids) {
//				$query = DB::query('SELECT aid, attachment, remote FROM '.DB::table('forum_attachment_'.$tableid).' WHERE aid IN ('.dimplode($taids).')');
//				while($avalue = DB::fetch($query)) {
//					$list[$aid2pid[$avalue['aid']]]['pic'] = 'forum/'.$avalue['attachment'];
//					$list[$aid2pid[$avalue['aid']]]['picflag'] = $avalue['remote'] ? '2' : '1';
//				}
//			}
//
//			foreach($listpids as $key => $value) {
//				$datalist[] = $list[$value];
//			}
//		}
        $datalist = array();
        $query = DB::query($sql="select * from " .DB::table('etuan_product') . " LIMIT $startrow,$items;");
        while($data = DB::fetch($query)) {
            $datalist[]= array(
				'id' => $data['id'],
				'idtype' => 'pid',
				'title' => cutstr(str_replace('\\\'', '&#39;', addslashes($data['name'])), $titlelength, ''),
				'url' => 'forum.php?mod=viewthread&do=productinfo&tid='.$data['id'].'&pid='.$data['id'],
				'pic' => ($data['photo'] ? '' : $_G['style']['imgdir'].'/nophoto.gif'),
				'picflag' => '0',
                'fields' => array(
					'fulltitle' => str_replace('\\\'', '&#39;', addslashes($data['name'])),
//					'totalitems' => $data['totalitems'],
//					'author' => $data['seller'] ? $data['seller'] : $_G['setting']['anonymoustext'],
					'authorid' => $data['seller_id'] ? $data['seller_id'] : 0,
					'price' =>$data['price'],
                        ),
            );
        }
		return array('html' => '', 'data' => $datalist);
	}
}


?>