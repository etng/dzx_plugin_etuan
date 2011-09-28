<?php
if(!defined('IN_DISCUZ')){
	exit("Access Denied!");
}
require_once DISCUZ_ROOT.'./source/plugin/etuan/etuan.class.php';
class threadplugin_etuan  extends etuan
{

	var $name = '';//主题类型名称
	var $buttontext = '';//发帖时按钮文字
	var $iconfile = '';//发布主题链接中的前缀图标

	function __construct()
    {
        parent::__construct();
		$this->name =lang('plugin/etuan', 'thread_name');
		$this->buttontext = lang('plugin/etuan', 'thread_buttontext');
		$this->iconfile = 'source/plugin/etuan/static/icon_post.gif';
//		$this->name = $this->lang('thread_name');
//		$this->buttontext = $this->lang('thread_buttontext');
//		$this->iconfile = 'source/plugin/etuan/static/icon_post.gif';
	}
    //发主题时页面新增的表单项目，通过 return 返回即可输出到发帖页面中
	function newthread($fid) {
        global $_G;
        $my_products = $this->listProducts($_G['uid']);
		include template('etuan:tuan_newthread');
		return $return;
	}
    //主题发布后的数据判断
	function newthread_submit($fid) {
        $this->validData();
	}
    function validData()
    {
        global $_G;
    }
    function formatData()
    {
        global $_G;
    }
    //主题发布后的数据处理
	function newthread_submit_end($fid, $tid) {
        global $_G;
        $this->formatData();
        $tuan_id = DB::insert('etuan_tuan', array(
            'tid' => $tid,
            'seller_id' => $_G['uid'],
            'begin_date' => $_G['gp_start_time'],
            'end_date' => $_G['gp_stop_time'],
            'intro' => $_G['gp_intro'],
			'name' => $_G['gp_name'],
        ), true);
        foreach($_G['gp_tuan_product_enable'] as $product_id=>$dummy)
        {
            DB::insert('etuan_tuan_product', array(
                'tuan_id' => $tuan_id,
                'product_id' => $product_id,
                'unit_price' => $_G['gp_product_unit_price'][$product_id],
                'user_limit' => $_G['gp_product_user_limit'][$product_id],
                'total_limit' => $_G['gp_product_total_limit'][$product_id],
            ));
        }
	}
    //编辑主题时页面新增的表单项目，通过 return 返回即可输出到编辑主题页面中
	function editpost($fid, $tid) {
	}
    //主题编辑后的数据判断
	function editpost_submit($fid, $tid) {

	}
    //主题编辑后的数据处理
	function editpost_submit_end($fid, $tid) {

	}
    //回帖后的数据处理
	function newreply_submit_end($fid, $tid) {

	}
    //查看主题时页面新增的内容，通过 return 返回即可输出到主题首贴页面中
	function viewthread($tid) {
        $tuan = DB::fetch_first("SELECT * FROM ".DB::table('etuan_tuan')." where  tid={$tid}");
    	$products = array();
        $query = DB::query("SELECT
            p.*
            FROM ".DB::table('etuan_tuan_product')." AS tp
            LEFT JOIN ".DB::table('etuan_product')." AS p on tp.product_id=p.id
            WHERE tp.tuan_id='{$tuan['id']}'");
        while($row = DB::fetch($query)){
            $products[] = $row;
        }
		include template('etuan:tuan_viewthread');
		return $return;
    }
}