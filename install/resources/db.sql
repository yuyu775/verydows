DROP TABLE IF EXISTS `#tablepre#admin`;
CREATE TABLE `#tablepre#admin` (
  `user_id` smallint(5) unsigned NOT NULL auto_increment,
  `username` char(16) NOT NULL,
  `password` char(32) NOT NULL,
  `name` varchar(60) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `last_ip` char(15) NOT NULL default '',
  `last_date` int(10) unsigned NOT NULL default '0',
  `created_date` int(10) unsigned NOT NULL default '0',
  `hash` char(40) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#admin_active`;
CREATE TABLE `#tablepre#admin_active` (
  `sess_id` char(32) NOT NULL default '',
  `user_id` smallint(5) unsigned NOT NULL,
  `ip` char(15) NOT NULL default '0.0.0.0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `expires` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#admin_role`;
CREATE TABLE `#tablepre#admin_role` (
  `user_id` smallint(5) unsigned NOT NULL,
  `role_id` smallint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#adv`;
CREATE TABLE `#tablepre#adv` (
  `adv_id` mediumint(8) unsigned NOT NULL auto_increment,
  `position_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `type` char(5) NOT NULL default '1',
  `params` text NOT NULL,
  `codes` text NOT NULL,
  `start_date` int(10) unsigned NOT NULL default '0',
  `end_date` int(10) unsigned NOT NULL default '0',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`adv_id`),
  KEY `position_id` (`position_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#adv_position`;
CREATE TABLE `#tablepre#adv_position` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `width` smallint(4) unsigned NOT NULL default '0',
  `height` smallint(4) unsigned NOT NULL default '0',
  `codes` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#aftersales`;
CREATE TABLE `#tablepre#aftersales` (
  `as_id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `order_id` char(15) NOT NULL default '',
  `goods_id` mediumint(8) unsigned NOT NULL default '0',
  `goods_qty` smallint(5) unsigned NOT NULL default '1',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `cause` text NOT NULL,
  `mobile_no` char(11) NOT NULL default '',
  `created_date` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`as_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#aftersales_message`;
CREATE TABLE `#tablepre#aftersales_message` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `as_id` mediumint(8) unsigned NOT NULL default '0',
  `admin_id` smallint(5) unsigned NOT NULL default '0',
  `content` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `as_id` (`as_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#article`;
CREATE TABLE `#tablepre#article` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `cate_id` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(180) NOT NULL,
  `picture` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `brief` varchar(240) NOT NULL default '',
  `meta_keywords` varchar(240) NOT NULL default '',
  `meta_description` varchar(240) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `created_date` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cate_id` USING BTREE (`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#article_cate`;
CREATE TABLE `#tablepre#article_cate` (
  `cate_id` smallint(5) unsigned NOT NULL auto_increment,
  `cate_name` varchar(60) character set utf8 NOT NULL,
  `meta_keywords` varchar(240) character set utf8 NOT NULL default '',
  `meta_description` varchar(240) character set utf8 NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  PRIMARY KEY  (`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `#tablepre#brand`;
CREATE TABLE `#tablepre#brand` (
  `brand_id` smallint(5) unsigned NOT NULL auto_increment,
  `brand_name` varchar(60) NOT NULL default '',
  `brand_logo` varchar(255) NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  PRIMARY KEY  (`brand_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#email_queue`;
CREATE TABLE `#tablepre#email_queue` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(60) NOT NULL default '',
  `tpl_id` char(30) NOT NULL default '',
  `subject` varchar(240) NOT NULL default '',
  `body` text NOT NULL,
  `is_html` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `last_err` varchar(255) NOT NULL default '',
  `err_count` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#email_subscription`;
CREATE TABLE `#tablepre#email_subscription` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(60) NOT NULL default '',
  `created_date` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#email_template`;
CREATE TABLE `#tablepre#email_template` (
  `id` char(30) NOT NULL,
  `name` varchar(50) NOT NULL default '',
  `subject` varchar(240) NOT NULL default '',
  `is_html` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#tablepre#email_template` VALUES ('retrieve_user_password', '用户密码找回', '通过您的邮箱找回密码', '1');
INSERT INTO `#tablepre#email_template` VALUES ('validate_user_email', '用户邮箱地址验证', '邮箱地址验证', '1');

DROP TABLE IF EXISTS `#tablepre#feedback`;
CREATE TABLE `#tablepre#feedback` (
  `fb_id` mediumint(8) unsigned NOT NULL auto_increment,
  `type` tinyint(1) unsigned NOT NULL default '0',
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `subject` varchar(120) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `content` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `mobile_no` char(11) NOT NULL default '',
  `created_date` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fb_id`),
  KEY `user_id` USING BTREE (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#feedback_message`;
CREATE TABLE `#tablepre#feedback_message` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fb_id` mediumint(8) unsigned NOT NULL default '0',
  `admin_id` smallint(5) unsigned NOT NULL default '0',
  `content` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fb_id` USING BTREE (`fb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#friendlink`;
CREATE TABLE `#tablepre#friendlink` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `logo` varchar(255) NOT NULL default '',
  `url` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `created_date` int(10) unsigned NOT NULL default '0',
  `seq` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#goods`;
CREATE TABLE `#tablepre#goods` (
  `goods_id` mediumint(8) unsigned NOT NULL auto_increment,
  `cate_id` smallint(5) unsigned NOT NULL default '0',
  `brand_id` smallint(5) unsigned NOT NULL default '0',
  `goods_name` varchar(180) NOT NULL default '',
  `goods_sn` char(20) NOT NULL default '',
  `now_price` decimal(10,2) unsigned NOT NULL default '0.00',
  `original_price` decimal(10,2) unsigned NOT NULL default '0.00',
  `goods_image` varchar(30) NOT NULL default '',
  `goods_brief` text NOT NULL,
  `goods_content` text NOT NULL,
  `goods_weight` decimal(10,2) unsigned NOT NULL default '0.00',
  `stock_qty` smallint(4) unsigned NOT NULL default '0',
  `meta_keywords` varchar(240) NOT NULL default '',
  `meta_description` varchar(240) NOT NULL default '',
  `created_date` int(10) unsigned NOT NULL default '0',
  `newarrival` tinyint(1) unsigned NOT NULL default '0',
  `recommend` tinyint(1) unsigned NOT NULL default '0',
  `bargain` tinyint(1) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`goods_id`),
  KEY `cate_id` (`cate_id`),
  FULLTEXT KEY `indexing` (`goods_name`,`meta_keywords`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#goods_album`;
CREATE TABLE `#tablepre#goods_album` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `goods_id` mediumint(8) unsigned NOT NULL,
  `image` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#goods_attr`;
CREATE TABLE `#tablepre#goods_attr` (
  `goods_id` mediumint(8) unsigned NOT NULL,
  `attr_id` mediumint(8) NOT NULL,
  `value` varchar(160) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#goods_cate`;
CREATE TABLE `#tablepre#goods_cate` (
  `cate_id` smallint(5) unsigned NOT NULL auto_increment,
  `parent_id` smallint(5) unsigned NOT NULL default '0',
  `cate_name` varchar(60) NOT NULL default '',
  `meta_keywords` varchar(240) NOT NULL default '',
  `meta_description` varchar(240) NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  PRIMARY KEY  (`cate_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#goods_cate_attr`;
CREATE TABLE `#tablepre#goods_cate_attr` (
  `attr_id` mediumint(8) unsigned NOT NULL auto_increment,
  `cate_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(60) NOT NULL default '',
  `opts` text NOT NULL,
  `uom` varchar(20) NOT NULL default '',
  `filtrate` tinyint(1) unsigned NOT NULL default '0',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  PRIMARY KEY  (`attr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#goods_cate_brand`;
CREATE TABLE `#tablepre#goods_cate_brand` (
  `cate_id` smallint(5) unsigned NOT NULL,
  `brand_id` smallint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#goods_optional`;
CREATE TABLE `#tablepre#goods_optional` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type_id` smallint(5) unsigned NOT NULL default '0',
  `goods_id` mediumint(8) unsigned NOT NULL default '0',
  `opt_text` varchar(80) NOT NULL default '',
  `opt_price` decimal(10,2) unsigned NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#goods_optional_type`;
CREATE TABLE `#tablepre#goods_optional_type` (
  `type_id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#goods_related`;
CREATE TABLE `#tablepre#goods_related` (
  `goods_id` mediumint(8) NOT NULL,
  `related_id` mediumint(8) NOT NULL,
  `direction` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`goods_id`,`related_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#goods_review`;
CREATE TABLE `#tablepre#goods_review` (
  `review_id` int(10) unsigned NOT NULL auto_increment,
  `order_id` char(15) NOT NULL default '',
  `goods_id` mediumint(8) unsigned NOT NULL default '0',
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `rating` tinyint(1) unsigned NOT NULL default '1',
  `content` text NOT NULL,
  `created_date` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `replied` text NOT NULL,
  PRIMARY KEY  (`review_id`),
  KEY `goods_id` (`goods_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#help`;
CREATE TABLE `#tablepre#help` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `cate_id` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `content` text NOT NULL,
  `meta_keywords` varchar(240) NOT NULL default '',
  `meta_description` varchar(240) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cate_id` (`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#help_cate`;
CREATE TABLE `#tablepre#help_cate` (
  `cate_id` smallint(5) unsigned NOT NULL auto_increment,
  `cate_name` varchar(60) NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#login_security`;
CREATE TABLE `#tablepre#login_security` (
  `ip` char(15) NOT NULL,
  `err_count` tinyint(1) unsigned NOT NULL default '1',
  `expires` int(10) unsigned NOT NULL default '0',
  `lock_expires` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `ip_expires` (`ip`,`expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#navigation`;
CREATE TABLE `#tablepre#navigation` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `position` tinyint(1) unsigned NOT NULL default '0',
  `target` tinyint(1) unsigned NOT NULL default '0',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  `visible` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#tablepre#navigation` VALUES
('1','后台管理','#http_host#/index.php?m=backend&c=main&a=index','0','1','1','1'),
('2','官方网站','http://www.verydows.com/','0','1','2','1'),
('3','我的订单','#http_host#/order/index.html','1','1','1','1'),
('4','收藏夹','#http_host#/favorite/index.html','1','1','2','1'),
('5','社区论坛','http://bbs.verydows.com/','2','1','1','1'),
('6','帮助文档','http://www.verydows.com/manual/starting.html','2','1','2','1'),
('7','相关下载','http://www.verydows.com/download/index.html','2','1','3','1'),
('8','Github','https://github.com/Verytops/verydows','2','1','4','1');

DROP TABLE IF EXISTS `#tablepre#oauth`;
CREATE TABLE `#tablepre#oauth` (
  `party` char(10) NOT NULL default '',
  `name` varchar(30) NOT NULL,
  `params` text NOT NULL,
  `instruction` varchar(240) NOT NULL default '',
  `enable` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`party`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#tablepre#oauth` VALUES ('qq', 'QQ', '{\"app_id\":\"\",\"app_key\":\"\"}', 'QQ互联开放平台为第三方网站提供了丰富的API。第三方网站接入QQ互联开放平台后，即可通过调用平台提供的API实现用户使用QQ帐号登录网站功能，且可以获取到腾讯QQ用户的相关信息。', '0');
INSERT INTO `#tablepre#oauth` VALUES ('weibo', '新浪微博', '{\"app_key\":\"\",\"app_secret\":\"\"}', '网站接入是微博针对第三方网站提供的社会化网络接入方案。接入微连接让您的网站支持用微博帐号登录，基于OAuth2.0协议，使用微博 Open API 进行开发， 即可用微博帐号登录你的网站， 让你的网站降低新用户注册成本，快速获取大量用户。', '0');

DROP TABLE IF EXISTS `#tablepre#order`;
CREATE TABLE `#tablepre#order` (
  `order_id` char(15) NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `consignee` text NOT NULL,
  `shipping_method` smallint(5) unsigned NOT NULL default '0',
  `payment_method` smallint(5) unsigned NOT NULL default '0',
  `order_status` tinyint(1) unsigned NOT NULL default '1',
  `goods_amount` decimal(10,2) unsigned NOT NULL default '0.00',
  `shipping_amount` decimal(10,2) unsigned NOT NULL default '0.00',
  `order_amount` decimal(10,2) unsigned NOT NULL default '0.00',
  `memos` varchar(240) NOT NULL default '',
  `created_date` int(10) unsigned NOT NULL default '0',
  `payment_date` int(10) unsigned NOT NULL default '0',
  `trade_no` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#order_goods`;
CREATE TABLE `#tablepre#order_goods` (
  `order_id` char(15) NOT NULL default '',
  `goods_id` mediumint(8) unsigned NOT NULL default '0',
  `goods_name` varchar(180) NOT NULL default '',
  `goods_image` varchar(30) NOT NULL default '',
  `goods_opts` varchar(255) NOT NULL default '',
  `goods_qty` smallint(5) NOT NULL default '1',
  `goods_price` decimal(10,2) unsigned NOT NULL default '0.00',
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#order_log`;
CREATE TABLE `#tablepre#order_log` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `order_id` char(15) NOT NULL default '',
  `admin_id` smallint(5) unsigned NOT NULL default '0',
  `operate` char(10) NOT NULL,
  `cause` varchar(240) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#order_shipping`;
CREATE TABLE `#tablepre#order_shipping` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `order_id` char(15) NOT NULL default '',
  `carrier_id` smallint(5) unsigned NOT NULL default '0',
  `tracking_no` varchar(20) NOT NULL default '',
  `memos` varchar(240) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#payment_method`;
CREATE TABLE `#tablepre#payment_method` (
  `id` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) NOT NULL default '',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `pcode` varchar(20) NOT NULL default '',
  `params` text NOT NULL,
  `instruction` varchar(240) NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  `enable` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#tablepre#payment_method` VALUES ('1', '余额支付', '0', 'balance', '', '', '1', '1');
INSERT INTO `#tablepre#payment_method` VALUES ('2', '货到付款', '1', 'cod', '', '', '2', '1');
INSERT INTO `#tablepre#payment_method` VALUES ('3', '支付宝', '0', 'alipay', '{\"seller_email\":\"",\"partner\":\"\",\"key\":\"\"}', '', '3', '1');

DROP TABLE IF EXISTS `#tablepre#role`;
CREATE TABLE `#tablepre#role` (
  `role_id` smallint(5) unsigned NOT NULL auto_increment,
  `role_name` varchar(50) NOT NULL default '',
  `role_brief` varchar(240) NOT NULL default '',
  `role_acl` text NOT NULL,
  PRIMARY KEY  (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#sendmail_limit`;
CREATE TABLE `#tablepre#sendmail_limit` (
  `ip` char(15) NOT NULL default '',
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `type` char(30) NOT NULL default '',
  `count` tinyint(1) unsigned NOT NULL default '1',
  `dateline` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#setting`;
CREATE TABLE `#tablepre#setting` (
  `sk` varchar(30) NOT NULL,
  `sv` text NOT NULL,
  PRIMARY KEY  (`sk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#tablepre#setting` VALUES ('site_name', 'Verydows 开源电商系统');
INSERT INTO `#tablepre#setting` VALUES ('home_title', 'Verydows 开源电子商务系统 | 轻松开启电商之旅');
INSERT INTO `#tablepre#setting` VALUES ('home_keywords', '');
INSERT INTO `#tablepre#setting` VALUES ('home_description', '');
INSERT INTO `#tablepre#setting` VALUES ('footer_info', '<div class=\"copyright mt10\"><p>联系邮箱：service@verydows.com<span class=\"sep\">|</span>QQ交流群：372701906<span class=\"sep\">|</span>Copyright © 2016 Verydows.com 版权所有</p></div>');
INSERT INTO `#tablepre#setting` VALUES ('goods_search_per_num', '20');
INSERT INTO `#tablepre#setting` VALUES ('upload_filetype', '.jpg|.jpeg|.gif|.png|.bmp|.swf|.flv|.avi|.rmvb');
INSERT INTO `#tablepre#setting` VALUES ('upload_filesize', '2MB');
INSERT INTO `#tablepre#setting` VALUES ('captcha_admin_login', '2');
INSERT INTO `#tablepre#setting` VALUES ('captcha_user_login', '2');
INSERT INTO `#tablepre#setting` VALUES ('captcha_user_register', '1');
INSERT INTO `#tablepre#setting` VALUES ('captcha_feedback', '1');
INSERT INTO `#tablepre#setting` VALUES ('smtp_server', '');
INSERT INTO `#tablepre#setting` VALUES ('smtp_user', '');
INSERT INTO `#tablepre#setting` VALUES ('smtp_password', '');
INSERT INTO `#tablepre#setting` VALUES ('smtp_port', '');
INSERT INTO `#tablepre#setting` VALUES ('smtp_secure', '');
INSERT INTO `#tablepre#setting` VALUES ('admin_mult_ip_login', '0');
INSERT INTO `#tablepre#setting` VALUES ('upload_goods_filesize', '300KB');
INSERT INTO `#tablepre#setting` VALUES ('visitor_stats', '1');
INSERT INTO `#tablepre#setting` VALUES ('goods_hot_searches', '');
INSERT INTO `#tablepre#setting` VALUES ('cate_goods_per_num', '20');
INSERT INTO `#tablepre#setting` VALUES ('goods_history_num', '5');
INSERT INTO `#tablepre#setting` VALUES ('goods_related_num', '5');
INSERT INTO `#tablepre#setting` VALUES ('goods_review_per_num', '10');
INSERT INTO `#tablepre#setting` VALUES ('upload_goods_filetype', '.jpg|.png|.gif');
INSERT INTO `#tablepre#setting` VALUES ('show_goods_stock', '0');
INSERT INTO `#tablepre#setting` VALUES ('order_cancel_expires', '5');
INSERT INTO `#tablepre#setting` VALUES ('goods_img_thumb', '[{\"w\":350,\"h\":350},{\"w\":150,\"h\":150},{\"w\":100,\"h\":100},{\"w\":50,\"h\":50}]');
INSERT INTO `#tablepre#setting` VALUES ('goods_album_thumb', '[{\"w\":350,\"h\":350},{\"w\":50,\"h\":50}]');
INSERT INTO `#tablepre#setting` VALUES ('enabled_theme', 'default');
INSERT INTO `#tablepre#setting` VALUES ('user_consignee_limits', '15');
INSERT INTO `#tablepre#setting` VALUES ('upload_avatar_filesize', '200KB');
INSERT INTO `#tablepre#setting` VALUES ('order_delivery_expires', '7');
INSERT INTO `#tablepre#setting` VALUES ('user_register_email_verify', '0');
INSERT INTO `#tablepre#setting` VALUES ('user_review_approve', '0');
INSERT INTO `#tablepre#setting` VALUES ('rewrite_enable', '1');
INSERT INTO `#tablepre#setting` VALUES ('home_newarrival_num', '5');
INSERT INTO `#tablepre#setting` VALUES ('home_recommend_num', '5');
INSERT INTO `#tablepre#setting` VALUES ('home_bargain_num', '5');
INSERT INTO `#tablepre#setting` VALUES ('home_article_num', '4');
INSERT INTO `#tablepre#setting` VALUES ('data_cache_lifetime', '7200');
INSERT INTO `#tablepre#setting` VALUES ('goods_fulltext_query', '0');
INSERT INTO `#tablepre#setting` VALUES ('debug', '1');
INSERT INTO `#tablepre#setting` VALUES ('rewrite_rule', '{\"404.html\":\"main\\/404\",\"search.html\":\"goods\\/search\",\"item\\/<id>.html\":\"goods\\/index\",\"cate\\/<id>.html\":\"category\\/index\",\"<a>\\/img\":\"image\\/<a>\",\"index.html\":\"main\\/index\",\"<c>\\/<a>.html\":\"<c>\\/<a>\"}');

DROP TABLE IF EXISTS `#tablepre#shipping_carrier`;
CREATE TABLE `#tablepre#shipping_carrier` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `tracking_url` varchar(255) NOT NULL default '',
  `service_tel` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#tablepre#shipping_carrier` VALUES ('1', '顺丰速运', 'http://www.sf-express.com/cn/sc/dynamic_functions/waybill/#search/bill-number/', '95338');
INSERT INTO `#tablepre#shipping_carrier` VALUES ('2', '中通快递', 'http://www.zto.cn/GuestService/Bill?txtbill=', '95311');

DROP TABLE IF EXISTS `#tablepre#shipping_method`;
CREATE TABLE `#tablepre#shipping_method` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `params` text NOT NULL,
  `instruction` varchar(240) NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  `enable` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#tablepre#shipping_method` VALUES ('1', '普通快递', '{\"0\":{\"type\":\"fixed\",\"area\":\"0\",\"charges\":\"10\"}}', '全国范围统一价10元', '1', '1');

DROP TABLE IF EXISTS `#tablepre#user`;
CREATE TABLE `#tablepre#user` (
  `user_id` mediumint(8) unsigned NOT NULL auto_increment,
  `username` char(16) NOT NULL default '',
  `password` char(32) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `email_status` tinyint(1) unsigned NOT NULL default '0',
  `hash` char(40) NOT NULL default '',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#user_account`;
CREATE TABLE `#tablepre#user_account` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `balance` decimal(10,2) unsigned NOT NULL default '0.00',
  `points` int(10) unsigned NOT NULL default '0',
  `exp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#user_account_log`;
CREATE TABLE `#tablepre#user_account_log` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `admin_id` smallint(5) unsigned NOT NULL default '0',
  `balance` decimal(10,2) NOT NULL default '0.00',
  `points` int(10) NOT NULL default '0',
  `exp` int(10) NOT NULL,
  `cause` varchar(255) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#user_actinfo`;
CREATE TABLE `#tablepre#user_actinfo` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `created_date` int(10) unsigned NOT NULL default '0',
  `created_ip` char(15) NOT NULL default '0.0.0.0',
  `last_date` int(10) unsigned NOT NULL default '0',
  `last_ip` char(15) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#user_consignee`;
CREATE TABLE `#tablepre#user_consignee` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `name` varchar(60) NOT NULL default '',
  `province` tinyint(2) unsigned NOT NULL default '0',
  `city` tinyint(2) unsigned NOT NULL default '0',
  `borough` tinyint(2) unsigned NOT NULL default '0',
  `address` varchar(240) NOT NULL default '',
  `zip` char(6) NOT NULL default '',
  `mobile_no` char(11) NOT NULL default '',
  `tel_no` varchar(20) NOT NULL default '',
  `is_default` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#user_favorite`;
CREATE TABLE `#tablepre#user_favorite` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `goods_id` mediumint(8) unsigned NOT NULL,
  `created_date` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#user_group`;
CREATE TABLE `#tablepre#user_group` (
  `group_id` smallint(5) unsigned NOT NULL auto_increment,
  `group_name` varchar(60) collate utf8_unicode_ci NOT NULL,
  `min_exp` int(10) unsigned NOT NULL default '0',
  `discount_rate` tinyint(3) unsigned NOT NULL default '100',
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#tablepre#user_group` VALUES ('1', '初级会员', '0', '100');
INSERT INTO `#tablepre#user_group` VALUES ('2', '中级会员', '2000', '100');
INSERT INTO `#tablepre#user_group` VALUES ('3', '高级会员', '7000', '100');

DROP TABLE IF EXISTS `#tablepre#user_oauth`;
CREATE TABLE `#tablepre#user_oauth` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `party` char(10) NOT NULL default '',
  `oauth_key` char(32) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#user_profile`;
CREATE TABLE `#tablepre#user_profile` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `name` varchar(30) NOT NULL default '',
  `avatar` varchar(40) NOT NULL default '',
  `gender` tinyint(1) unsigned NOT NULL default '0',
  `birth_year` smallint(4) unsigned NOT NULL default '0',
  `birth_month` tinyint(2) unsigned NOT NULL default '0',
  `birth_day` tinyint(2) unsigned NOT NULL default '0',
  `mobile_no` char(11) NOT NULL default '',
  `qq` varchar(15) NOT NULL default '',
  `signature` varchar(240) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#tablepre#visitor_stats`;
CREATE TABLE `#tablepre#visitor_stats` (
  `sessid` char(32) NOT NULL default '',
  `ip` char(15) NOT NULL default '0.0.0.0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `pv` smallint(5) unsigned NOT NULL default '1',
  `referrer` varchar(80) NOT NULL default '',
  `browser` tinyint(2) unsigned NOT NULL default '0',
  `platform` tinyint(2) unsigned NOT NULL default '0',
  `area` char(10) NOT NULL default '',
  KEY `sessid` USING BTREE (`sessid`),
  KEY `ip` (`ip`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
