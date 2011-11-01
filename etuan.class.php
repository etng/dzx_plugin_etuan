<?php
if(!defined('IN_DISCUZ')){
	exit("Access Denied!");
}
class etuan
{
    var $config = array();
	function __construct()
    {
        global $_G;
        $this->config = $_G['cache']['plugin']['etuan'];
        if($this->config['debug'])
        {
            @error_reporting(E_ALL ^ E_NOTICE);
            @ini_set('display_errors', true);
        }
	}
    function lang($text)
    {
        return lang('plugin/etuan', $text);
    }
    function ajaxOrMsg($msg, $url)
    {
        global $_G;
        if($_G['inajax']) {
            echo 'true';
        }else
        {
            showmessage($msg, $url, array(), array('showdialog' => 1, 'closetime' => true));
        }
    }
	function acceptPayment($order_id, $amount, $gateway='alipay')
	{
		$order = $this->fetchRow('etuan_order', $order_id);
		$status = $order['status'];
		$paid_at = dgmdate(time(), 'Y-m-d H:i:s');
		if(($order['paid_amount']+$amount)>$order['total'])
		{
			$status = 'paid';
		}
		DB::query("UPDATE ".DB::table('etuan_order')."
		SET paid_amount=paid_amount+{$amount}
		,payment_method='{$gateway}'
		,paid_at='{$paid_at}'
		,status='{$status}'
		WHERE order_id={$order_id}");
		//insert payment log
        DB::insert('etuan_payment_log', array(
                'order_id' => $order_id,
                'paid_at' => $paid_at,
                'amount' => $amount,
                'gateway' => $gateway,
                ), true);
	}
	function paymentConf($type='alipay')
	{
		if($type=='alipay')
		{
			return array(
			'api_type' => $this->readConf('alipay_api_type'),
			'partner' => $this->readConf('alipay_partner'),
			'account' => $this->readConf('alipay_account'),
			'key' => $this->readConf('alipay_key'),
			);
		}
		elseif($type=='tenpay')
		{
			return array(
			'account' => $this->readConf('tenpay_account'),
			'key' => $this->readConf('tenpay_key'),
			);
		}
		else
		{
			return array();
		}
	}
    function readConf($var, $default=null)
    {
        if(isset($this->config[$var]))
        {
            if(unserialize($this->config[$var]))
            {
                $this->config[$var] = unserialize($this->config[$var]);
            }
            return $this->config[$var];
        }
        return $default;
    }
    function listProducts($seller_id)
    {
    	$list = array();
        $query = DB::query("SELECT * FROM ".DB::table('etuan_product')." where seller_id='$seller_id'");
        while($row = DB::fetch($query)){
            $list[] = $row;
        }
        return $list;
    }
    function fetchTuan($tuan_id, $reload=false)
    {
        static $cache=array();
        if($reload || !isset($cache[$tuan_id]))
        {
            $cache[$tuan_id] = DB::fetch_first("SELECT * FROM ".DB::table('etuan_tuan')." where id={$tuan_id}");
        }
        return $cache[$tuan_id];
    }
    function fetchPayment($payment_id, $reload=false)
    {
        static $cache=array();
        if($reload || !isset($cache[$payment_id]))
        {
            $cache[$payment_id] = DB::fetch_first("SELECT * FROM ".DB::table('etuan_payment')." where id={$payment_id}");
        }
        return $cache[$payment_id];
    }
    function fetchShipMethod($shipmethod_id, $reload=false)
    {
        static $cache=array();
        if($reload || !isset($cache[$shipmethod_id]))
        {
            $cache[$shipmethod_id] = DB::fetch_first("SELECT * FROM ".DB::table('etuan_shipmethod')." where id={$shipmethod_id}");
        }
        return $cache[$shipmethod_id];
    }
    function fetchProduct($product_id, $reload=false)
    {
        static $cache=array();
        if($reload || !isset($cache[$product_id]))
        {
            $cache[$product_id] = DB::fetch_first("SELECT * FROM ".DB::table('etuan_product')." where id={$product_id}");
        }
        return $cache[$product_id];
    }
    function fetchCol($sql, $idx=0)
    {
        $list = array();
        $query = DB::query($sql);
        while($row = DB::fetch($query, MYSQL_BOTH)){
            $list[] = $row[$idx];
        }
        return $list;
    }
    function fetchOptions($sql, $idx_key=0, $idx_val=1)
    {
        $options = array();
        $query = DB::query($sql);
        while($row = DB::fetch($query, MYSQL_BOTH)){
            $options[$row[$idx_key]] = $row[$idx_val];
        }
        return $options;
    }
    function fetchAll($table, $where=array(), $limit=0, $offset=0)
    {
        $table = trim($table);
        $list = array();
        if(preg_match('/^(show|select)\s+/i', $table))
        {
            $sql = $table;
        }else
        {
            settype($where, 'array');
            foreach($where as $k=>$v)
            {
                if(!is_int($k))
                {
                    $where[]=sprintf('%s=\'%s\'', $k, $v);
                    unset($where[$k]);
                }
            }
            $where = $where?' where ' .implode(' and ', $where):'';
            $limit = $limit?" limit {$offset},{$limit}":'';
            $sql = "SELECT * FROM ".DB::table($table)." {$where}{$limit}";
        }
        $query = DB::query($sql);
        while($row = DB::fetch($query)){
            $list[] = $row;
        }
        return $list;
    }
    function fetchRow($table, $pk_value, $reload=false)
    {
        static $cache=array();
        if($reload || !isset($cache[$table][$pk_value]))
        {
            $cache[$table][$pk_value] = DB::fetch_first($sql="SELECT * FROM ".DB::table($table)." where id={$pk_value}");
        }
        return $cache[$table][$pk_value];
    }
    function fetchSupplier($id, $reload=false)
    {
        return $this->fetchRow('etuan_supplier', $id, $reload);
    }
    function isSeller()
    {
        global $_G;
        return true;
        return in_array($_G['group']['groupid'], $this->readConf('seller_gid'));
    }
}
class plugin_etuan extends etuan
 {

    function global_usernav_extra1()
    {
        $links = array();
        $sep = ' | ';
        $links []= '<a href="plugin.php?id=etuan:cart">购物车</a>';
        if($this->isSeller())
        {
            $links []= '<a href="plugin.php?id=etuan:my&app=tuan">我的团购</a>';
        }
        return implode($sep, $links);
    }
}


class plugin_etuan_forum extends plugin_etuan
{
    function viewthread_postbottom()
    {
        return array(/*'易团 团购系统已启用，欢迎到团购板块参加团购！'*/);
    }
}

class etuan_cart
{
    protected $tuan;
    protected $products = array();
    function __construct($tuan)
    {
        $this->tuan = $tuan;
        $this->restore();
    }
    function restore()
    {
        $cart = dstripslashes(getcookie('etuan_cart'));
        if($cart && unserialize($cart))
        {
            $this->products = unserialize($cart);
        }
    }
    function persist()
    {
        dsetcookie('etuan_cart', serialize($this->products), 86400);
    }
    function getItemsGroupByTuan($get_detail=false)
    {
        $cart_items_by_tuan = array();
        foreach($this->getTuans() as $tuan_id=>$product_ids)
        {
            $cart_items_by_tuan[$tuan_id] = array(
                'tuan'=>$this->tuan->fetchTuan($tuan_id),
                'items'  => $this->getItems($tuan_id, $get_detail),
                'sub_total'  => $sub_total = $this->getProductFee($tuan_id),
                'ship_fee'  => $ship_fee=0,
                'credit_discount'  => $credit_discount=0,
                'usable_credit'  => $this->getCreditLimit($tuan_id),
                'base_total'  => $base_total=$sub_total-$credit_discount,
                'total'  => $base_total+$ship_fee,
                );
        }
        return $cart_items_by_tuan;
    }
    function getItems($only_tuan_id=0, $get_detail=false)
    {
        $order_items = array();
        foreach($this->products as $tuan_product_id=>$quantity)
        {
            list($tuan_id, $product_id) = explode('_', $tuan_product_id);
            if(!$only_tuan_id || ($only_tuan_id==$tuan_id))
            {
                $tuan = $this->tuan->fetchTuan($tuan_id);
                $product = $this->tuan->fetchProduct($product_id);
                $unit_price = $product['price'];
                $price = $unit_price * $quantity;
                $order_item = compact('tuan_id', 'product_id', 'unit_price', 'quantity', 'price');
                if($get_detail)
                {
                    $order_item['product'] = $product;
                    $order_item['tuan'] = $tuan;
                }
                $order_items[] =  $order_item;
            }
        }
        return $order_items;
    }
    function getProductFee($only_tuan_id=0)
    {
        $product_fee = 0;
        foreach($this->products as $tuan_product_id=>$quantity)
        {
            list($tuan_id, $product_id) = explode('_', $tuan_product_id);
            if(!$only_tuan_id || ($only_tuan_id==$tuan_id))
            {
                $tuan = $this->tuan->fetchTuan($tuan_id);
                $product = $this->tuan->fetchProduct($product_id);
                $unit_price = $product['price'];
                $price = $unit_price * $quantity;

                $product_fee += $price;
            }
        }
        return $product_fee;
    }
    function getCreditLimit($only_tuan_id=0)
    {
        $credit_limit = 0;
        foreach($this->products as $tuan_product_id=>$quantity)
        {
            list($tuan_id, $product_id) = explode('_', $tuan_product_id);
            if(!$only_tuan_id || ($only_tuan_id==$tuan_id))
            {
                $product = $this->tuan->fetchProduct($product_id);
                $credit_limit+=$product['credit_limit'] * $quantity;
            }
        }
        return $credit_limit;
    }
    function getShipFee($tuan_id, $shipmethod)
    {
        $tuan = $this->tuan->fetchRow('etuan_tuan', $tuan_id);
        $field = "ship_fee_{$shipmethod}";
        $fee = 0;
        if(isset($tuan[$field]))
        {
            $fee = $tuan[$field];
        }
        return $fee;
    }
    function submit($tuan_id, $buyer_id, $credit_used, $ship_method, $payment_method, $address, $memo)
    {
        $sn = dgmdate(time(), 'YmdHis').mt_rand(10,99);
        $sub_total = $this->getProductFee($tuan_id);
        $credit_limit = $this->getCreditLimit($tuan_id);
        $ship_fee = $this->getShipFee($tuan_id, $ship_method);
        $credit_used = ($credit_used>$credit_limit)?$credit_limit:0;
        $bought_at = dgmdate(time(), 'Y-m-d H:i:s');
        $credit_discount = $credit_used * $this->tuan->readConf('credit_ratio')/100;
        $total = $sub_total + $ship_fee - $credit_discount;
        $status = $total?'paid':'pending';
        $order_items = $this->getItems($tuan_id);
        $order_data = compact('sn','tuan_id', 'buyer_id', 'bought_at', 'ship_method', 'payment_method', 'address_id', 'sub_total', 'ship_fee', 'credit_used', 'credit_discount', 'total', 'memo');
        $order_data = array_merge($order_data, $address);
        $order_id = DB::insert('etuan_order', $order_data, true);

        if($credit_used)
        {
            $credit_type = $this->tuan->readConf('credit_type');
            updatemembercount($buyer_id, array("extcredits{$credit_type}" => -$credit_used), true,'',0, 'etuan::etuan_used_credit');
        }
        foreach($order_items as $order_item)
        {
            //插入订单产品列表
            DB::insert('etuan_order_product', array_merge($order_item, compact('order_id')));
        }

        $order = $this->tuan->fetchRow('etuan_order', $order_id);
        $order['tuan'] = $this->tuan->fetchRow('etuan_tuan', $order['tuan_id']);
        $this->clear($tuan_id);
        //通知团长
        notification_add($tuan['seller_id'], 'etuan', 'etuan:seller_notify_tuan_bought', array(
            'tuan_name' => $tuan['name'],
            'order_id' => $order['id'],
            'order_sn' => $order['sn'],
        ), 1);
        return $order;
    }
    function detail($tuan_id, $product_id)
    {
        $tuan = $this->tuan->fetchTuan($tuan_id);
        $product = $this->tuan->fetchProduct($product_id);
        $tuan_products = $this->tuan->fetchAll('etuan_tuan_product', compact('tuan_id','product_id'));
        $tuan_product = current($tuan_products);
        return compact('tuan', 'product', 'tuan_product');
    }
    function addItem($tuan_id, $product_id, $quantity=1)
    {
        $this->products["{$tuan_id}_{$product_id}"] = $quantity;
        $this->persist();
    }
    function updateItem($tuan_id, $product_id, $quantity=1)
    {
        if($quantity<=0)
        {
            $this->removeProduct($tuan_id, $product_id);
        }
        $this->products["{$tuan_id}_{$product_id}"] = $quantity;
        $this->persist();
    }
    function getTuans()
    {
        $product_ids_by_tuan_id = array();
        foreach($this->products as $tuan_product_id=>$quantity)
        {
            list($tuan_id, $product_id) = explode('_', $tuan_product_id);
            if(!isset($product_ids_by_tuan_id[$tuan_id]))
            {
                $product_ids_by_tuan_id[$tuan_id] = array();
            }
            $product_ids_by_tuan_id[$tuan_id][]= $product_id;
        }
        return $product_ids_by_tuan_id;
    }
    function clearTuan($tuan_id)
    {
        foreach($this->products as $tuan_product_id=>$quantity)
        {
            if(strpos($tuan_product_id, $tuan_id . '_')===0)
            {
                unset($this->products[$tuan_product_id]);
            }
        }
        $this->persist();
    }
    function removeItem($tuan_id, $product_id)
    {
        unset($this->products["{$tuan_id}_{$product_id}"]);
        $this->persist();
    }
    function isEmpty()
    {
        return empty($this->products);
    }
    function clear()
    {
        $this->products = array();
        $this->persist();
    }
}

?>