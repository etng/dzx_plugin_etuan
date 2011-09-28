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
}
class plugin_etuan extends etuan
 {

    function global_usernav_extra1()
    {
        return '<a href="plugin.php?id=etuan:cart">购物车</a>';
    }
}


class plugin_etuan_forum extends plugin_etuan
{
    function viewthread_postbottom()
    {
        return array('易团 团购系统已启用，欢迎到团购板块参加团购！');
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
    function getItemsGroupByTuan()
    {
        $cart_items_by_tuan = array();
        foreach($this->getTuans() as $tuan_id=>$product_ids)
        {
            $cart_items_by_tuan[$tuan_id] = array(
                'tuan'=>$this->tuan->fetchTuan($tuan_id),
                'items'  => $this->getItems(),
                );
        }
        return $cart_items_by_tuan;
    }
    function getItems($only_tuan_id=0)
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
                $order_items[] = compact('tuan_id', 'product_id', 'unit_price', 'quantity', 'price');
            }
        }
        return $order_items;
    }
    function getProductFee($only_tuan_id=0)
    {
        $product_fee = 0;
        foreach($this->products() as $tuan_product_id=>$quantity)
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
        foreach($this->products() as $tuan_product_id=>$quantity)
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
    function getShipFee($tuan_id, $shipmethod_id)
    {
        return 0;
    }
    function submit($tuan_id, $buyer_id, $credit_used, $shipmethod_id, $payment_id, $address_id, $memo)
    {
        $sn = dgmdate(time(), 'YmdHis').mt_rand(10,99);
        $product_fee = $this->getProductFee($tuan_id);
        $credit_limit = $this->getCreditLimit($tuan_id);
        $ship_fee = $this->getShipFee($tuan_id, $shipmethod_id);
        if($credit_used>$credit_limit)
        {
            $credit_used = $credit_limit;
        }
        $bought_at = dgmdate(time(), 'Y-m-d H:i:s');
        $actual_fee = $product_fee + $ship_fee - $credit_used * $etuan->readConf('credit_ratio')/100;
        $order_id = DB::insert('etuan_order', compact('sn','tuan_id',  'buyer_id', 'bought_at', 'credit_used', 'shipmethod_id', 'payment_id', 'address_id', 'actual_fee', 'product_fee', 'ship_fee', 'credit_used', 'memo'), true);
        $credit_type = $etuan->readConf('credit_type');
        updatemembercount($buyer_id, array("extcredits{$credit_type}" => -$credit_used), true,'',0, 'etuan::etuan_used_credit');
        foreach($order_items as $order_item)
        {
            //插入订单产品列表
            DB::insert('etuan_order_products', array_merge($order_item, compact('order_id')));
            //通知团长
            $tuan = $this->tuan->fetchTuan($order_item['tuan_id']);
            $notify_var = array_merge($order_item, compact('tuan_name', 'product_name', 'order_id'));
            notification_add($tuan['seller_id'], 'etuan', 'etuan:seller_notify_tuan_bought', $notify_var, 1);
        }
        $this->clear($tuan_id);
        return $sn;
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