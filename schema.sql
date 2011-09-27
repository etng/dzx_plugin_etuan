# --------------------------------------------------------
# Host:                         127.0.0.1
# Server version:               5.1.48-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3920
# Date/time:                    2011-09-19 07:44:37
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table myxiaoqu_bbs.pre_etuan
CREATE TABLE IF NOT EXISTS `pre_etuan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `begin_date` date NOT NULL,
  `end_date` date NOT NULL,
  `product_ids` varchar(255) NOT NULL,
  `seller_id` int(10) unsigned NOT NULL,
  `status` varchar(55) NOT NULL,
  `category` varchar(55) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_address
CREATE TABLE IF NOT EXISTS `pre_etuan_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `buyer_id` int(10) unsigned NOT NULL,
  `province` varchar(55) NOT NULL,
  `city` varchar(55) NOT NULL,
  `town` varchar(55) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `buyer_id` (`buyer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_order
CREATE TABLE IF NOT EXISTS `pre_etuan_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `buyer_id` int(10) unsigned NOT NULL,
  `bought_at` datetime NOT NULL,
  `payment_id` int(10) unsigned NOT NULL,
  `address_id` int(10) unsigned NOT NULL,
  `paid_at` datetime NOT NULL,
  `paid_amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `shipmethod_id` int(10) unsigned NOT NULL,
  `shipped_at` datetime NOT NULL,
  `received_at` datetime NOT NULL,
  `product_fee` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `ship_fee` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `memo` varchar(255) NOT NULL,
  `credit_used` int(10) unsigned NOT NULL DEFAULT '0',
  `actual_fee` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `fk_pre_etuan_order_payment1` (`payment_id`),
  KEY `fk_pre_etuan_order_pre_etuan_address1` (`address_id`),
  KEY `fk_pre_etuan_order_pre_tuan_shipmethod1` (`shipmethod_id`),
  KEY `buyer_id` (`buyer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_order_products
CREATE TABLE IF NOT EXISTS `pre_etuan_order_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `price` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `fk_pre_etuan_order_products_pre_etuan_order1` (`order_id`),
  KEY `fk_pre_etuan_order_products_pre_etuan_product1` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_payment
CREATE TABLE IF NOT EXISTS `pre_etuan_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seller_id` int(10) unsigned NOT NULL,
  `alipay_partener_id` varchar(55) NOT NULL,
  `alipay_key` varchar(55) NOT NULL,
  `alipay_account` varchar(55) NOT NULL,
  `alipay_api_type` varchar(55) NOT NULL,
  `tenpay_account` varchar(55) NOT NULL,
  `tenpay_key` varchar(55) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_product
CREATE TABLE IF NOT EXISTS `pre_etuan_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seller_id` int(10) unsigned NOT NULL,
  `supplier_id` int(10) unsigned NOT NULL,
  `name` varchar(55) NOT NULL,
  `market_price` decimal(8,2) NOT NULL,
  `supply_price` decimal(8,2) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `spec` varchar(55) NOT NULL,
  `unit_name` varchar(55) NOT NULL,
  `photo` varchar(55) NOT NULL,
  `description` text NOT NULL,
  `credit_limit` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_product_supplier` (`supplier_id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_shipmethod
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table myxiaoqu_bbs.pre_etuan_supplier
CREATE TABLE IF NOT EXISTS `pre_etuan_supplier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seller_id` int(10) unsigned NOT NULL,
  `name` varchar(55) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `address` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `contact_name` varchar(55) NOT NULL,
  `contact_gender` enum('m','f','x') NOT NULL DEFAULT 'x',
  `contact_phone` varchar(15) NOT NULL,
  `contact_qq` varchar(15) NOT NULL,
  `cate` varchar(55) NOT NULL,
  `website` varchar(255) NOT NULL,
  `tax_license` varchar(55) NOT NULL,
  `speed_rating` int(10) unsigned NOT NULL DEFAULT '3',
  `quality_rating` int(10) unsigned NOT NULL DEFAULT '3',
  `service_rating` int(10) unsigned NOT NULL DEFAULT '3',
  `memo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
