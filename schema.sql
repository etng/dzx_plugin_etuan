# --------------------------------------------------------
# Host:                         127.0.0.1
# Server version:               5.1.48-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3920
# Date/time:                    2011-10-19 08:13:05
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table myxiaoqu_bbs.pre_etuan_address
DROP TABLE IF EXISTS `pre_etuan_address`;
CREATE TABLE IF NOT EXISTS `pre_etuan_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `buyer_id` int(10) unsigned NOT NULL COMMENT '买家',
  `community_id` int(10) unsigned NOT NULL COMMENT '小区',
  `province` varchar(55) NOT NULL COMMENT '省',
  `city` varchar(55) NOT NULL COMMENT '市',
  `town` varchar(55) NOT NULL COMMENT '城镇',
  `district` varchar(55) NOT NULL COMMENT '小区',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `zip` varchar(10) NOT NULL COMMENT '邮编',
  `contact_name` varchar(10) NOT NULL COMMENT '姓名',
  `phone` varchar(25) NOT NULL COMMENT '电话',
  `email` varchar(255) NOT NULL COMMENT 'Email',
  PRIMARY KEY (`id`),
  KEY `buyer_id` (`buyer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收货地址';

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_community
DROP TABLE IF EXISTS `pre_etuan_community`;
CREATE TABLE IF NOT EXISTS `pre_etuan_community` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `contact_name` varchar(255) NOT NULL DEFAULT '' COMMENT '联系人',
  `contact_address` varchar(255) NOT NULL DEFAULT '' COMMENT '联系人地址',
  `contact_phone` varchar(255) NOT NULL DEFAULT '' COMMENT '联系电话',
  `level` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `usetype` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `upid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `displayorder` smallint(6) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `memo` mediumtext NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `upid` (`upid`,`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_order
DROP TABLE IF EXISTS `pre_etuan_order`;
CREATE TABLE IF NOT EXISTS `pre_etuan_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `sn` char(16) NOT NULL COMMENT '外部订单号',
  `tuan_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '团购',
  `buyer_id` int(10) unsigned NOT NULL COMMENT '买家',
  `bought_at` datetime NOT NULL COMMENT '购买时间',
  `sub_total` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '小计',
  `ship_fee` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费',
  `credit_used` int(10) unsigned NOT NULL COMMENT '使用积分',
  `credit_discount` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '积分抵扣',
  `total` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '合计',
  `address_id` int(10) unsigned NOT NULL COMMENT '配送地址',
  `ship_community_id` int(10) unsigned NOT NULL COMMENT '配送小区',
  `ship_address` varchar(100) NOT NULL COMMENT '配送楼层房号',
  `ship_name` varchar(50) NOT NULL COMMENT '收货人姓名',
  `ship_phone` varchar(50) NOT NULL COMMENT '收货人手机',
  `ship_email` varchar(50) NOT NULL COMMENT '收货人email',
  `payment_method` enum('alipay','tenpay','cod') NOT NULL DEFAULT 'cod' COMMENT '支付方式',
  `paid_amount` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已支付金额',
  `paid_at` datetime NOT NULL,
  `received_at` datetime NOT NULL COMMENT '收货时间',
  `ship_method` enum('pick','deliver') NOT NULL DEFAULT 'pick' COMMENT '配送方式',
  `shipped_at` datetime NOT NULL COMMENT '配送时间',
  `status` enum('pending','paid','shipped','finished','canceled','invalid') NOT NULL DEFAULT 'pending' COMMENT '状态',
  `memo` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `buyer_id` (`buyer_id`),
  KEY `tuan_id` (`tuan_id`),
  KEY `address_id` (`address_id`),
  KEY `payment_method` (`payment_method`),
  KEY `status` (`status`),
  KEY `sn` (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单';

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_order_product
DROP TABLE IF EXISTS `pre_etuan_order_product`;
CREATE TABLE IF NOT EXISTS `pre_etuan_order_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `tuan_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '团购',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单',
  `community_id` int(10) unsigned NOT NULL COMMENT '小区',
  `product_id` int(10) unsigned NOT NULL COMMENT '产品',
  `quantity` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '数量',
  `unit_price` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单价',
  `price` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '合计',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单产品';

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_payment
DROP TABLE IF EXISTS `pre_etuan_payment`;
CREATE TABLE IF NOT EXISTS `pre_etuan_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `seller_id` int(10) unsigned NOT NULL COMMENT '卖家',
  `alipay_partener_id` varchar(55) NOT NULL COMMENT '支付宝合作者ID',
  `alipay_key` varchar(55) NOT NULL COMMENT '支付宝key',
  `alipay_account` varchar(55) NOT NULL COMMENT '支付号账号',
  `alipay_api_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '支付宝接口类型',
  `tenpay_account` varchar(55) NOT NULL COMMENT '财付通帐号',
  `tenpay_key` varchar(55) NOT NULL COMMENT '财付通key',
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='支付方式';

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_payment_log
DROP TABLE IF EXISTS `pre_etuan_payment_log`;
CREATE TABLE IF NOT EXISTS `pre_etuan_payment_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `order_id` int(10) unsigned DEFAULT '0' COMMENT '订单',
  `amount` decimal(8,2) unsigned DEFAULT '0.00' COMMENT '金额',
  `gateway` enum('alipay','tenpay','cod') DEFAULT 'cod' COMMENT '支付方式',
  `paid_at` datetime DEFAULT NULL COMMENT '支付时间',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付记录';

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_product
DROP TABLE IF EXISTS `pre_etuan_product`;
CREATE TABLE IF NOT EXISTS `pre_etuan_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `seller_id` int(10) unsigned NOT NULL COMMENT '商家',
  `supplier_id` int(10) unsigned NOT NULL COMMENT '供应商',
  `name` varchar(55) NOT NULL COMMENT '名称',
  `market_price` decimal(8,2) unsigned NOT NULL COMMENT '市场价',
  `supply_price` decimal(8,2) unsigned NOT NULL COMMENT '供应价',
  `price` decimal(8,2) unsigned NOT NULL COMMENT '卖价',
  `spec` varchar(55) NOT NULL COMMENT '规格',
  `unit_name` varchar(55) NOT NULL COMMENT '单位',
  `photo` varchar(255) NOT NULL COMMENT '图片',
  `description` text NOT NULL COMMENT '介绍',
  `credit_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '可用积分数',
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品';

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_seller
DROP TABLE IF EXISTS `pre_etuan_seller`;
CREATE TABLE IF NOT EXISTS `pre_etuan_seller` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `ship_only_pick` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否只能自提',
  `ship_fee_pick` decimal(8,2) unsigned NOT NULL COMMENT '自提运费',
  `ship_fee_deliver` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '送货上门运费',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家';

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_shipmethod
DROP TABLE IF EXISTS `pre_etuan_shipmethod`;
CREATE TABLE IF NOT EXISTS `pre_etuan_shipmethod` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seller_id` int(10) unsigned NOT NULL,
  `is_enabled` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL,
  `first_unit` int(11) NOT NULL DEFAULT '1',
  `first_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `continue_unit` int(11) NOT NULL DEFAULT '1',
  `continue_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `intro` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_id_and_is_enabled` (`seller_id`,`is_enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='配送方式';

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_supplier
DROP TABLE IF EXISTS `pre_etuan_supplier`;
CREATE TABLE IF NOT EXISTS `pre_etuan_supplier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `seller_id` int(10) unsigned NOT NULL COMMENT '商家',
  `name` varchar(55) NOT NULL COMMENT '名称',
  `phone` varchar(25) NOT NULL COMMENT '电话',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `logo` varchar(255) NOT NULL COMMENT 'Logo',
  `contact_name` varchar(55) NOT NULL COMMENT '联系人姓名',
  `second_contact_name` varchar(55) NOT NULL COMMENT '后备联系人姓名',
  `contact_gender` enum('m','f','x') NOT NULL DEFAULT 'x' COMMENT '联系人性别',
  `contact_phone` varchar(15) NOT NULL COMMENT '联系电话',
  `second_contact_phone` varchar(15) NOT NULL COMMENT '后备联系电话',
  `contact_qq` varchar(15) NOT NULL COMMENT '联系人QQ',
  `cate` varchar(55) NOT NULL COMMENT '分类',
  `website` varchar(255) NOT NULL COMMENT '网站',
  `tax_license` varchar(55) NOT NULL COMMENT '税号',
  `speed_rating` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '送货速度评价',
  `quality_rating` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '产品质量评价',
  `service_rating` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '服务态度评价',
  `memo` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='供应商';

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_tuan
DROP TABLE IF EXISTS `pre_etuan_tuan`;
CREATE TABLE IF NOT EXISTS `pre_etuan_tuan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `tid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '帖子编号',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `begin_date` date NOT NULL COMMENT '开始日期',
  `end_date` date NOT NULL COMMENT '结束日期',
  `seller_id` int(10) unsigned NOT NULL COMMENT '商家',
  `category` varchar(55) NOT NULL COMMENT '分类',
  `ship_only_pick` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否只能自提',
  `ship_fee_pick` decimal(8,2) unsigned NOT NULL COMMENT '自提运费',
  `ship_fee_deliver` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '送货上门运费',
  `intro` text NOT NULL COMMENT '介绍',
  `status` enum('pre','ing','over') NOT NULL DEFAULT 'pre' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `seller_id_and_status` (`seller_id`,`status`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='团购';

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_tuan_product
DROP TABLE IF EXISTS `pre_etuan_tuan_product`;
CREATE TABLE IF NOT EXISTS `pre_etuan_tuan_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `tuan_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '团购',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品',
  `unit_price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '单价',
  `user_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '单用户限购',
  `total_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总限购',
  `sold_cnt` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已卖出数量',
  PRIMARY KEY (`id`),
  KEY `tuan_id` (`tuan_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='团购中的商品';

# Data exporting was unselected.
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
