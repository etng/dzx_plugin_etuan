<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$op = trim($_G['gp_op']);
switch($op){
    case 'delete':
        $tid = intval($_G['gp_tid']);
        DB::delete('etuan_tuan', "seller_id={$_G['uid']} and tid={$tid}");
        $etuan->ajaxOrMsg('etuan:tuan_delete_success', "plugin.php?id=etuan:my&app=tuan");
        break;
    case 'view':
        $tid = intval($_G['gp_tid']);
        $tuan_thread = DB::fetch_first("SELECT *,tuan.id as tuan_id,tuan.status as tuan_status FROM ".DB::table('etuan_tuan')." as tuan
        LEFT JOIN ".DB::table('forum_thread')." as thread ON tuan.tid=thread.tid
        where tuan.tid={$tid}");

        $report = trim(@$_G['gp_report']);
        $report = in_array($report, array('orders','supplies','community_supplies','community_shipment','profit_and_loss')) ? $report : 'orders';
        if($report=='orders')
        {
            $tuan_orders = array();
            $query = DB::query("SELECT * FROM ".DB::table('etuan_order')."
            where tuan_id={$tuan_thread['tuan_id']}");
            while($row = DB::fetch($query)){
                $row['items'] = $etuan->fetchAll("SELECT * FROM ".DB::table('etuan_order_product')." where order_id={$row['id']}");
                $tuan_orders[] = $row;
            }
        }else if($report=='supplies')
        {
            $rows =  $etuan->fetchAll("SELECT top.product_id, SUM(quantity) AS quantity_sum
            FROM ".DB::table('etuan_order_product')." AS top
            WHERE top.tuan_id={$tuan_thread['tuan_id']}
            GROUP BY top.product_id");
            $stat = array();
            foreach($rows as $row)
            {
                $stat[$row['product_id']]=$row['quantity_sum'];
            }
            $products = $etuan->fetchAll('select
            p.supplier_id, p.id AS product_id,p.name AS product_name,p.spec AS product_spec,
            p.supply_price AS product_unit_price,p.unit_name as product_unit_name from '.DB::table('etuan_product').' as p where p.id in ('.implode(',', array_keys($stat)).')');
            $supplies = array();

            foreach($products as $product)
            {
                $product['quantity'] = $stat[$product['product_id']];
                $product['price'] = $product['quantity']*$product['product_unit_price'];
                $price+=$product['price'];
                $supplies[$product['supplier_id']]['items'][]=$product;
            }
            foreach($supplies as $supplier_id=>$data)
            {
                $price = 0;
                $supplies[$supplier_id]['supplier'] = $etuan->fetchRow('etuan_supplier', $supplier_id);
                foreach($data['items'] as $product)
                {
                    $price+=$product['price'];
                }
                $supplies[$supplier_id]['price'] =$price;
            }
        }else if($report=='profit_and_loss')
        {
             $product_profit_and_loss_list = array();
            $commuinty_profit_and_loss_list = array();
            $rows =  $etuan->fetchAll("SELECT top.product_id, SUM(quantity) AS quantity_sum
            FROM ".DB::table('etuan_order_product')." AS top
            WHERE top.tuan_id={$tuan_thread['tuan_id']}
            GROUP BY top.product_id");
            $stat = array();
            foreach($rows as $row)
            {
                $stat[$row['product_id']]=$row['quantity_sum'];
            }
            $products = $etuan->fetchAll('select
            p.id AS product_id,p.name AS name,p.spec AS spec,
            p.supply_price AS unit_price,p.unit_name as unit_name,tp.unit_price as unit_cost from '.DB::table('etuan_product').' as p left join '.DB::table('etuan_tuan_product').' as tp on tp.product_id=p.id where tp.tuan_id='. $tuan_thread['tuan_id'].' and p.id in ('.implode(',', array_keys($stat)).')');
            foreach($products as $product)
            {
                $product['quantity'] = $stat[$product['product_id']];
                $product['cost'] = $product['quantity']*$product['unit_price'];
                $product['price'] = $product['quantity']*$product['unit_cost'];
                $product['gross_profit'] = $product['price']-$product['cost'];
                $product['gross_profit_ratio'] = intval($product['gross_profit']*10000/$product['price'])/100 . '%';
                $product_profit_and_loss_list[]=$product;
            }
        }else if($report=='community_shipment')
        {
            $community_shipments = array();
            $rows = $etuan->fetchAll('SELECT o.id as order_id, o.ship_community_id,c.name as ship_community_name, o.ship_address,o.ship_name, o.ship_phone, o.ship_method, m.username AS buyer_name, o.total
            FROM '.DB::table('etuan_order').' AS o
            left join '.DB::table('etuan_community').' as c on o.ship_community_id=c.id
            LEFT JOIN '.DB::table('common_member').' AS m ON o.buyer_id=m.uid
            WHERE o.tuan_id='.$tuan_thread['tuan_id']);
            foreach($rows as $row)
            {
                $community_shipments[$row['ship_community_id']]['name'] = $row['ship_community_name'];
                $row['items'] = $etuan->fetchAll('
                SELECT
                p.name AS product_name,
                p.spec AS product_spec,
                p.unit_name AS product_unit_name,
                op.quantity,
                op.unit_price,
                op.price
                FROM '.DB::table('etuan_order_product').' AS op
                LEFT JOIN '.DB::table('etuan_product').' AS p ON p.id=op.product_id
                WHERE op.order_id='.$row['order_id']);
                $row['items_count'] = count($row['items']);
                $row['first_item'] = $row['items'][0];
                $row['other_items'] = array_slice($row['items'], 1);
                $community_shipments[$row['ship_community_id']]['orders'][] = $row;
            }
        }else if($report=='community_supplies')
        {
            $community_supplies = array();
            $community_ids = $etuan->fetchCol('select distinct community_id from '.DB::table('etuan_order_product').' where tuan_id='.$tuan_thread['tuan_id']);
            foreach($etuan->fetchAll('select * from '.DB::table('etuan_community').' where id in ('.implode(',', $community_ids) . ')') as $community)
            {
                $community_supply['community'] = $community;
                $community_supply['items'] = array();
                $sql = "
                SELECT
                    product_id,
                    SUM(quantity) AS sum_quantity,
                    SUM(price) AS sum_price,
                    COUNT(order_id) AS buyer_count
                FROM ".DB::table('etuan_order_product')." AS op
                WHERE community_id={$community['id']}
                GROUP BY product_id";
                foreach($etuan->fetchAll($sql) as $row)
                {
                    $row['product'] = $etuan->fetchRow('etuan_product', $row['product_id']);
                    $community_supply['items'][]=$row;
                }
                $community_supplies[]=$community_supply;
            }
        }
		include template('etuan:my_tuan_view_'.$report);

        break;
    case 'list':
    default:
        $extra_sql = array("tuan.seller_id = '$_G[uid]'");

        $filter = trim($_G['gp_filter']);
        $filter = in_array($filter, array('all', 'ing', 'over')) ? $filter : 'all';
        if($filter!=='all')
        {
            $extra_sql []= "tuan.status ='{$filter}'";
        }

        $extra_sql = implode(' and ', $extra_sql);

        $page = max(1, intval($_G['page']));
        $limit = 5;
        $offset = $limit * ($page - 1);
        $total = DB::result_first("SELECT COUNT(*) FROM ".DB::table('etuan_tuan')." as tuan where $extra_sql");

        $tuan_threads = array();
        $query = DB::query("SELECT *,tuan.status as tuan_status FROM ".DB::table('etuan_tuan')." as tuan
        LEFT JOIN ".DB::table('forum_thread')." as thread ON tuan.tid=thread.tid
        where $extra_sql ORDER BY thread.dateline DESC LIMIT $offset,$limit");
        while($row = DB::fetch($query)){
            $tuan_threads[] = $row;
        }
        $multi = multi($total, $limit, $page, "plugin.php?id=etuan:my&app=tuan&filter={$filter}");

        include template('etuan:my_tuan');
        break;
}
