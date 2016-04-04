# -------------------------------------------------------------
# <?php die();?>
# Verydows Database Backup
# Program: Verydows  Release 
# MySql: 5.0.45-community-nt-log 
# Database: vdstest0001 
# Creation: 2016-04-04 16:12:42
# Official: http://www.verydows.com
# -------------------------------------------------------------

DROP TABLE IF EXISTS `verydows_admin`;
CREATE TABLE `verydows_admin` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_admin` VALUES
('1','admin','707b5637abd9b8ab5ebb95a6b65494dc','','yuyu775@qq.com','127.0.0.1','1459735757','1458746451','ac8a79580f76ec642160ece9f6aff971a1acc7d3');


DROP TABLE IF EXISTS `verydows_admin_active`;
CREATE TABLE `verydows_admin_active` (
  `sess_id` char(32) NOT NULL default '',
  `user_id` smallint(5) unsigned NOT NULL,
  `ip` char(15) NOT NULL default '0.0.0.0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `expires` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `verydows_admin_active` VALUES
('3f85f45df839bd5ecb72b7b8acdbc1b8','1','127.0.0.1','1459735757','1459737425');


DROP TABLE IF EXISTS `verydows_admin_role`;
CREATE TABLE `verydows_admin_role` (
  `user_id` smallint(5) unsigned NOT NULL,
  `role_id` smallint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `verydows_adv`;
CREATE TABLE `verydows_adv` (
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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_adv` VALUES
('1','1','VIVO Xplay5 手机广告','image','{\"src\":\"http:\\/\\/demo.verydows.com\\/upload\\/adv\\/image\\/56e6a9d26d76d3GZ7ISlt5y10455.jpg\",\"width\":\"630\",\"height\":\"240\",\"title\":\"VIVO Xplay5 \\u5feb\\u65e0\\u8fb9\\u754c\",\"link\":\"http:\\/\\/demo.verydows.com\\/item\\/13.html\"}','<a href=\"http://demo.verydows.com/item/13.html\"><img src=\"http://demo.verydows.com/upload/adv/image/56e6a9d26d76d3GZ7ISlt5y10455.jpg\" width=\"630\" height=\"240\" alt=\"VIVO Xplay5 快无边界\" border=\"0\" /></a>','0','0','1','1'),
('2','2','微软 Surface Pro 3 广告','image','{\"src\":\"http:\\/\\/demo.verydows.com\\/upload\\/adv\\/image\\/56e7b6c31d293a7lQ8rXHGR19919.jpg\",\"width\":\"240\",\"height\":\"70\",\"title\":\"Surface Pro 3 \\u5168\\u65b0\\u5e73\\u677f\\uff0c\\u66ff\\u4ee3\\u60a8\\u7684\\u7b14\\u8bb0\\u672c\",\"link\":\"http:\\/\\/demo.verydows.com\\/item\\/17.html\"}','<a href=\"http://demo.verydows.com/item/17.html\"><img src=\"http://demo.verydows.com/upload/adv/image/56e7b6c31d293a7lQ8rXHGR19919.jpg\" width=\"240\" height=\"70\" alt=\"Surface Pro 3 全新平板，替代您的笔记本\" border=\"0\" /></a>','0','0','1','1'),
('3','3','下载体验 Verydows 宣传广告','image','{\"src\":\"http:\\/\\/demo.verydows.com\\/upload\\/adv\\/image\\/56ebb059678bdlqFgp6z2re2462.jpg\",\"width\":\"480\",\"height\":\"420\",\"title\":\"\\u7acb\\u5373\\u4e0b\\u8f7d\\u4f53\\u9a8cVerydows\",\"link\":\"http:\\/\\/www.verydows.com\\/download\\/index.html\"}','<a href=\"http://www.verydows.com/download/index.html\"><img src=\"http://demo.verydows.com/upload/adv/image/56ebb059678bdlqFgp6z2re2462.jpg\" width=\"480\" height=\"420\" alt=\"立即下载体验Verydows\" border=\"0\" /></a>','0','0','1','1'),
('4','1','双11来啦','image','{\"src\":\"http:\\/\\/demo.verydows.com\\/upload\\/adv\\/image\\/56ebb54d3c934W0xGnTL6Pz42200.jpg\",\"width\":\"630\",\"height\":\"240\",\"title\":\"\\u53cc11\\u8d2d\\u7269\\u72c2\\u6b22\\u8282\\u6765\\u5566\",\"link\":\"http:\\/\\/demo.verydows.com\\/news\\/4.html\"}','<a href=\"http://demo.verydows.com/news/4.html\"><img src=\"http://demo.verydows.com/upload/adv/image/56ebb54d3c934W0xGnTL6Pz42200.jpg\" width=\"630\" height=\"240\" alt=\"双11购物狂欢节来啦\" border=\"0\" /></a>','0','1478880000','2','1'),
('5','1','Verydows 正式版发布','image','{\"src\":\"http:\\/\\/demo.verydows.com\\/upload\\/adv\\/image\\/56ebc00d857f6mPasxHIy0S76596.jpg\",\"width\":\"630\",\"height\":\"240\",\"title\":\"\\u6b63\\u5f0f\\u7248\\u53d1\\u5e03\\u5566!\\u70b9\\u51fb\\u67e5\\u770b\\u8be6\\u60c5\",\"link\":\"http:\\/\\/www.verydows.com\\/\"}','<a href=\"http://www.verydows.com/\"><img src=\"http://demo.verydows.com/upload/adv/image/56ebc00d857f6mPasxHIy0S76596.jpg\" width=\"630\" height=\"240\" alt=\"正式版发布啦!点击查看详情\" border=\"0\" /></a>','0','0','3','1'),
('6','4','首页墨镜横幅广告','image','{\"src\":\"http:\\/\\/demo.verydows.com\\/upload\\/adv\\/image\\/56ebc42abca96qasTDgnQU669537.jpg\",\"width\":\"1110\",\"height\":\"90\",\"title\":\"\\u8fd9\\u773c\\u955c\\u503c\\u54ed\\u4e86\\uff01\\u62cd\\u4e0b\\u7acb\\u9001\\u591c\\u89c6\\u955c\",\"link\":\"#\"}','<a href=\"#\"><img src=\"http://demo.verydows.com/upload/adv/image/56ebc42abca96qasTDgnQU669537.jpg\" width=\"1110\" height=\"90\" alt=\"这眼镜值哭了！拍下立送夜视镜\" border=\"0\" /></a>','0','0','1','1'),
('7','5','首页飞利浦春季特惠横幅广告','image','{\"src\":\"http:\\/\\/demo.verydows.com\\/upload\\/adv\\/image\\/56ebc493d2db7aiqk4OCsJ166640.jpg\",\"width\":\"1110\",\"height\":\"100\",\"title\":\"\\u98de\\u5229\\u6d66\\u6625\\u5b63\\u7279\\uff0c\\u51c0\\u4eab\\u7f8e\\u4e3d\\u597d\\u98df\\u5149\",\"link\":\"#\"}','<a href=\"#\"><img src=\"http://demo.verydows.com/upload/adv/image/56ebc493d2db7aiqk4OCsJ166640.jpg\" width=\"1110\" height=\"100\" alt=\"飞利浦春季特，净享美丽好食光\" border=\"0\" /></a>','0','0','1','1'),
('8','6','首页三月换新家横幅广告','image','{\"src\":\"http:\\/\\/demo.verydows.com\\/upload\\/adv\\/image\\/56ebc543b078atTgnx4sCzV45720.jpg\",\"width\":\"1110\",\"height\":\"80\",\"title\":\"\\u4e09\\u6708\\u6362\\u65b0\\u5bb6\\u54c1\\u724c\\u5bb6\\u7535\\u4e00\\u7ad9\\u8d2d\",\"link\":\"#\"}','<a href=\"#\"><img src=\"http://demo.verydows.com/upload/adv/image/56ebc543b078atTgnx4sCzV45720.jpg\" width=\"1110\" height=\"80\" alt=\"三月换新家品牌家电一站购\" border=\"0\" /></a>','0','0','1','1');


DROP TABLE IF EXISTS `verydows_adv_position`;
CREATE TABLE `verydows_adv_position` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `width` smallint(4) unsigned NOT NULL default '0',
  `height` smallint(4) unsigned NOT NULL default '0',
  `codes` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_adv_position` VALUES
('1','首页轮播图片广告位','630','240','<div class=\"carousel cut\">
  <div class=\"carousel-imgs cut\">
  <{foreach $vars as $v}><{$v nofilter}><{/foreach}>
  </div>
  <ul class=\"carousel-tog\"><{foreach $vars as $k => $v}><li><{$k}></li><{/foreach}></ul>
</div>'),
('2','首页资讯下方广告位','240','70','<{$vars[0] nofilter}>'),
('3','用户登录左侧广告位','480','0','<{$vars[0] nofilter}>'),
('4','首页横幅Banner广告位一','1110','90','<{$vars[0] nofilter}>'),
('5','首页横幅Banner广告位二','1110','100','<{$vars[0] nofilter}>'),
('6','首页横幅Banner广告位三','1110','80','<{$vars[0] nofilter}>');


DROP TABLE IF EXISTS `verydows_aftersales`;
CREATE TABLE `verydows_aftersales` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_aftersales` VALUES
('1','1','553020002103193','9','1','0','日访问风微风微风到得对得起我的','13899996655','1459677781','2');


DROP TABLE IF EXISTS `verydows_aftersales_message`;
CREATE TABLE `verydows_aftersales_message` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `as_id` mediumint(8) unsigned NOT NULL default '0',
  `admin_id` smallint(5) unsigned NOT NULL default '0',
  `content` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `as_id` (`as_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_aftersales_message` VALUES
('1','1','0','人文氛围纷纷为分无分文','1459677875'),
('2','1','0','人文氛围纷纷为分无分文','1459677889'),
('3','1','1','绯闻绯闻绯闻发我','1459736362');


DROP TABLE IF EXISTS `verydows_article`;
CREATE TABLE `verydows_article` (
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_article` VALUES
('1','1','Verydows 1.0版 正式发布','','','','','','','0','1458022719'),
('2','1','软件环境要求','','','','','','','0','1458023419'),
('3','1','QQ讨论交流群','','<p>QQ交流群：372701906</p>','','','','','0','1458023994'),
('4','2','双11购物狂欢节','','<p>双11购物狂欢节 双11来了</p>','','','','','0','1458287839');


DROP TABLE IF EXISTS `verydows_article_cate`;
CREATE TABLE `verydows_article_cate` (
  `cate_id` smallint(5) unsigned NOT NULL auto_increment,
  `cate_name` varchar(60) character set utf8 NOT NULL,
  `meta_keywords` varchar(240) character set utf8 NOT NULL default '',
  `meta_description` varchar(240) character set utf8 NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  PRIMARY KEY  (`cate_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `verydows_article_cate` VALUES
('1','软件','','','1'),
('2','活动','','','2');


DROP TABLE IF EXISTS `verydows_brand`;
CREATE TABLE `verydows_brand` (
  `brand_id` smallint(5) unsigned NOT NULL auto_increment,
  `brand_name` varchar(60) NOT NULL default '',
  `brand_logo` varchar(255) NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  PRIMARY KEY  (`brand_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_brand` VALUES
('1','Apple/苹果','','99'),
('2','Huawei/华为','','99'),
('3','Miui/小米','','99'),
('4','ASUS/华硕','','99'),
('5','Lenovo/联想','','99'),
('6','Dell/戴尔','','99'),
('7','Acer/宏碁','','99'),
('8','HP/惠普','','99'),
('9','Samsung/三星','','99'),
('10','Nokia/诺基亚','','99'),
('11','Philips/飞利浦','','99'),
('12','Sony/索尼','','99'),
('13','周大福','','99'),
('14','施华洛世奇','','99'),
('15','vivo','','99'),
('16','Midea/美的','','99'),
('17','AUX/奥克斯','','99'),
('18','Microsoft/微软','','99');


DROP TABLE IF EXISTS `verydows_email_queue`;
CREATE TABLE `verydows_email_queue` (
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

DROP TABLE IF EXISTS `verydows_email_subscription`;
CREATE TABLE `verydows_email_subscription` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(60) NOT NULL default '',
  `created_date` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `verydows_email_template`;
CREATE TABLE `verydows_email_template` (
  `id` char(30) NOT NULL,
  `name` varchar(50) NOT NULL default '',
  `subject` varchar(240) NOT NULL default '',
  `is_html` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `verydows_email_template` VALUES
('retrieve_user_password','用户密码找回','通过您的邮箱找回密码','1'),
('validate_user_email','用户邮箱地址验证','邮箱地址验证','1');


DROP TABLE IF EXISTS `verydows_feedback`;
CREATE TABLE `verydows_feedback` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_feedback` VALUES
('1','2','1','fewfewfwefewf','fefewfewfewfewfewfewfewfewfewfewfewf','13899996655','1459679785','1');


DROP TABLE IF EXISTS `verydows_feedback_message`;
CREATE TABLE `verydows_feedback_message` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fb_id` mediumint(8) unsigned NOT NULL default '0',
  `admin_id` smallint(5) unsigned NOT NULL default '0',
  `content` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fb_id` USING BTREE (`fb_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_feedback_message` VALUES
('1','1','0','dw1dwdwdwqdwqdw','1459689107');


DROP TABLE IF EXISTS `verydows_friendlink`;
CREATE TABLE `verydows_friendlink` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `logo` varchar(255) NOT NULL default '',
  `url` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `created_date` int(10) unsigned NOT NULL default '0',
  `seq` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `verydows_goods`;
CREATE TABLE `verydows_goods` (
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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_goods` VALUES
('1','1','1','Apple iPhone 6s (A1700) 移动联通电信4G手机','0010011563','4888.00','5128.99','156e2d4fa488d0.jpg','小改变，大不同，3D Touch触屏新时代，6s带你感受前沿科技 ！国行正品，不怕检、不怕测，买的放心，售后贴心，用的舒心！','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e2daaf5dc64IiTXQnmG6Z66363.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e2dc22524ec5iENqzFsIB51238.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e2dc2ed68dbTJKr2HGiBQ34101.jpg\"/></p></div>','0.00','10000','苹果, 苹果手机, 苹果6s, iPhone, iPhone 6s, Apple','','1457712963','1','0','0','1'),
('2','1','2','华为 Mate8 3GB+32GB版 全网通 移动版 月光银 苍穹灰','0010022030','2999.00','3299.99','256e2f30e7256c.jpg','','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e2f2abed2195dHIXDVxa295565.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e2f26836850PvsUtf5CjI32491.jpg\"/></p></div>','0.00','9999','MATE8, 华为 Huawei, 华为手机, Huawei手机, 苍穹灰, 月光银','','1457714965','1','0','0','1'),
('3','1','3','小米4 MI4 2GB内存版 移动 联通 电信 4G手机','0010033521','1299.00','1299.00','356e2fa344ec51.jpg','新鲜1299！不锈钢金属边框、 5英寸屏窄边，工艺和手感超乎想象！','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e2fad802f73JXclAK3DV07838.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e2fae1ea2e2F6Y1lpxjE250520.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e2fae836e9fugT70NKaUe75951.jpg\"/></p></div>','0.00','9999','','','1457716238','0','1','0','1'),
('4','7','1','Apple MacBook Air MJVE2CH/A 13.3英寸宽屏笔记本电脑','0070014005','6666.00','0.00','456e3dfe6d22c1.jpg','','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e3df31c8fb7iRwAu3PbmC99819.jpg\" _src=\"http://demo.verydows.com/upload/goods/editor/56e3df31c8fb7iRwAu3PbmC99819.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e3df4023401NJv1Rc3LfM73993.jpg\" _src=\"http://demo.verydows.com/upload/goods/editor/56e3df4023401NJv1Rc3LfM73993.jpg\"/></p></div>','0.00','9999','MacBook, Apple 笔记本, 苹果笔记本, 苹果电脑, 笔记本电脑, MacBook Air MJVG2CH/A, 13.3英寸笔记本','','1457777278','0','1','0','1'),
('5','7','6','戴尔（DELL）Ins14MR-7508R 14.0英寸笔记本电脑 （i5-6200U 4G 500G Win10）','0070065228','4399.00','4599.99','556e3f1ea40b41.jpg','','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e3f3d10866cKyN8ADTlm775049.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e3f3d857b8fjc8pRygfUY79695.jpg\"/></p></div>','0.00','9999','Ins14MR-7508R, DELL, 戴尔, Dell 笔记本电脑, 戴尔笔记本, Ins14MR, Dell Ins14MR','','1457779699','0','0','0','1'),
('6','7','7','宏碁（acer）V3-372-P47B 13.3英寸轻薄笔记本电脑','0070076922','2999.00','3199.00','656e3f5f12fd03.jpg','奔腾4405U 4G 128G SSD 核芯显卡 蓝牙 win10','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e3f5d64aca8H4Ums25rXt87027.jpg\"/></p></div>','0.00','9999','','','1457780209','0','0','1','1'),
('7','7','9','三星(SAMSUNG)900X5L-K01 15.0英寸超薄笔记本电脑','0070097259','8999.00','0.00','756e3f9459edf9.jpg','i7-6500U 8G 256G固态硬盘 FHD PLS屏 超窄边框 Win10','','0.00','9999','三星 SAMSUNG, 900X5L-K01, 900X5L-K01, SAMSUNG 900X5L-K01, 三星 900X5L-K01, 笔记本电脑, 三星笔记本, SAMSUNG 笔记本','','1457781061','0','0','0','1'),
('8','8','1','Apple iPad Air 2 MH0W2CH/A 9.7英寸平板电脑','0080018206','3288.00','0.00','856e3fba434963.jpg','年货必备，送礼首选，太子妃升职记尽收眼底','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e4008736b14kWtqo0HygS54399.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e4008e6fc68FW01pSODne25515.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e40093ecb183MHgfX2PFV79431.jpg\"/></p></div>','0.00','9999','Apple iPad Air 2, MH0W2CH/A, Apple MH0W2CH/A, 苹果平板, 苹果iPad, 苹果iPad Air','','1457782972','0','1','0','1'),
('9','8','10','诺基亚（Nokia）N1 7.9英寸平板电脑','0000009639','1099.00','0.00','956e4029db165b.jpg','Z3580处理器 安卓5.0系统 2048x1536分辨率 2/32G内存','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e404e0a51a58MblFa9Jn152056.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e404e6555ca5o90xsbOah31334.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e404eb65c8bZyJVI760NU84987.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e404f152c6f6iFm8oWvgq47192.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e404f5ec67ffxakSiFUmN92775.jpg\"/></p></div>','0.00','10003','诺基亚 N1, NOKIA N1, 平板电脑, 诺基亚平板电脑, NOKIA平板电脑, N1 平板电脑','','1457784146','1','0','1','1'),
('10','9','11','飞利浦 PHILIPS 49PFL3445/T3 49英寸 全高清LED液晶电视','00901110039','2399.00','2699.00','1056e5188b5e84e.jpg','HDMI接口X3！阅影闻声，明智之选！保修两年，详情请关注PhilipsTV官方微信！','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e5197255a8dKT69YEHpMO28069.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e5196a0b5e2wgTuQ6I5Bd43172.jpg\"/></p></div>','0.00','9999','','','1457855939','0','1','1','1'),
('11','9','11','飞利浦 PHILIPS 48PFL5445/T3 48英寸 全高清LED智能电视','00901111459','2999.00','0.00','1156e51cf0075f8.jpg','智能电视 超薄窄边+玻璃底座 简洁流线外观设计 好评看得见！','','0.00','9999','','','1457855932','0','0','0','1'),
('12','9','12','索尼 SONY U90 55英寸4K超高清 安卓5.0智能系统液晶电视','00901212289','8999.00','0.00','1256e51f1dd0df2.jpg','4K钜献，U90震撼上市！','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e5203fa7fbeiAu3Jpt1BI19575.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e52045e8048Gui7A89S4Y21703.jpg\"/></p></div>','0.00','9999','索尼电视, 索尼智能电视, 智能电视, 液晶电视','','1457856602','1','0','0','1'),
('13','1','15','vivo Xplay5 全网通4G手机 4GB+128GB 双卡双待 香槟金','00101513635','3698.00','0.00','1356e6c40a0d492.jpg','双曲面屏 震撼上市','<div align=\"center\"><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e6c6405bf3bw2T3JtxeM558784.jpg\"/></p><p><img src=\"http://demo.verydows.com/upload/goods/editor/56e6c64a81c98BuJ9V5C0D426836.jpg\"/></p></div>','0.00','9999','vivo手机, Xplay5','','1457964653','1','0','0','1'),
('14','14','16','美的 Midea KFR-51LW/WYAA2 2匹 除PM2.5超静音变频柜机 物联网版','01401614386','6699.00','6755.99','1456e6ccd6bdbd6.jpg','手机APP操控，智享生活！出风效果好，覆盖广，低音运行！','','0.00','9999','','','1457966294','0','0','1','1'),
('15','14','17','奥克斯 AUX KFR-25GW/FK01+3 1匹 挂壁式家用冷暖空调','01401715715','1499.00','0.00','1556e6cf29b6bb6.jpg','奥克斯正1匹冷暖空调！高性价比，荣耀加冕！纯铜链接管，舒爽快人一步！','','0.00','9999','','','1457966889','0','0','0','1'),
('16','1','1','Apple iPhone 6s Plus','00100116500','5588.00','0.00','1656e6d17164926.jpg','小改变，大不同，3D Touch触屏新时代，6s带你感受前沿科技','','0.00','9999','','','1457968016','0','1','0','1');


DROP TABLE IF EXISTS `verydows_goods_album`;
CREATE TABLE `verydows_goods_album` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `goods_id` mediumint(8) unsigned NOT NULL,
  `image` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_goods_album` VALUES
('1','1','56e2d943368b959ynGbQ7lW80201.jpg'),
('2','1','56e2d94336995XcJQWpDSwE86673.jpg'),
('3','2','56e2f30e72928BSOzT93IkP9639.jpg'),
('4','2','56e2f30e729e9TJ9pqaSYQZ87527.jpg'),
('5','2','56e2f30e72a98gP0RSdCui37720.jpg'),
('6','2','56e2f30e72b41PyGKWtfZ0311691.jpg'),
('7','2','56e2f5332bff4rao5t7snf261816.jpg'),
('8','2','56e2f5332c0d0YT4xyB0kAd82509.jpg'),
('9','3','56e2fa344f02cY1uRU7wkr339310.jpg'),
('10','3','56e2fa344f0eamoiRIGQUST26183.jpg'),
('11','3','56e2fa344f195JtQsiem0Hy2366.jpg'),
('12','3','56e2fa344f23ctCfyjL1WAZ46687.jpg'),
('13','3','56e2fa344f2e0XPhivetwqd36656.jpg'),
('14','3','56e2fa344f384iqwXgUJ4OM64483.jpg'),
('15','4','56e3dfe6d279cuDlyS83YL155115.jpg'),
('16','4','56e3dfe6d288aDoQdTKf6nJ56044.jpg'),
('17','5','56e3f1ea40f58mkRSp4lMJA2728.jpg'),
('18','5','56e3f1ea4101alQtjuWg7G16579.jpg'),
('19','5','56e3f1ea410c87IodqvTUXH31299.jpg'),
('20','5','56e3f1ea41174sdoGeHNpL541450.jpg'),
('21','6','56e3f5f13024fewM0GFa5Vt49870.jpg'),
('22','6','56e3f5f13032bKUPf8uecMz91660.jpg'),
('23','6','56e3f5f1303d9oYudLMSe7y99233.jpg'),
('24','6','56e3f5f130486e4w0WSoUmZ13763.jpg'),
('25','7','56e3f9459f26416pkIfzRxL94522.jpg'),
('26','7','56e3f9459f329V1m5ivX80J61533.jpg'),
('27','7','56e3f9459f3d75RjAoGYxNI60323.jpg'),
('28','8','56e3fba434df3FvpI1ea2z011388.jpg'),
('29','8','56e3fba434ebdgGnoHcM62f69789.jpg'),
('30','8','56e3fba434f6bOk0oljHqRg88606.jpg'),
('31','9','56e4029db1a5aVK73Qd4kRY44539.jpg'),
('32','9','56e4029db1b2dYKsE8d9bl265913.jpg'),
('33','9','56e4029db1bdc2SgAQe8DhX13870.jpg'),
('34','9','56e4029db1c85GtqHLd9bfO22887.jpg'),
('35','9','56e4029db1d36cjQ02S4wRx84896.jpg'),
('36','10','56e5188b5ed92nkGi16usrt831.jpg'),
('37','10','56e5188b5ee5cX8rRQBv4Gh26528.jpg'),
('38','10','56e5188b5ef0asHY8fUuQv198815.jpg'),
('39','11','56e51cf0079a7JE9oKl0Njz63104.jpg'),
('40','11','56e51cf007a6aBOfzmsalQE58255.jpg'),
('41','12','56e51f1dd11f741p2ygvBAq21097.jpg'),
('42','12','56e51f1dd12bf5cX4uhmtb368719.jpg'),
('43','13','56e6c40a0d901YOUSGBw2NA2184.jpg'),
('44','13','56e6c40a0d9c8UVbCSzOqNW97722.jpg'),
('45','13','56e6c40a0da8byXYOEBjsqz53057.jpg'),
('46','14','56e6ccd6bdfeeayuY1i63oD43514.jpg'),
('47','14','56e6ccd6be0b0hc3qve6I0Z69833.jpg'),
('48','14','56e6ccd6be15cY3SmyQldF512516.jpg'),
('49','15','56e6cf29b71afKUwdtDvquI21134.jpg'),
('50','15','56e6cf29b729907TybLOFhI8230.jpg'),
('51','16','56e6d17164e20BpxUwMG6eC69216.jpg'),
('52','16','56e6d17164ee4EAzyl70sHQ5322.jpg'),
('53','16','56e6d17164f91PGQJUuEyrV97248.jpg');


DROP TABLE IF EXISTS `verydows_goods_attr`;
CREATE TABLE `verydows_goods_attr` (
  `goods_id` mediumint(8) unsigned NOT NULL,
  `attr_id` mediumint(8) NOT NULL,
  `value` varchar(160) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `verydows_goods_attr` VALUES
('1','19','143'),
('1','18','小卡(nano-SIM)'),
('1','17','3.5MM'),
('1','16','True Tone 闪光灯'),
('1','15','500万'),
('1','13','不支持'),
('1','14','1200万'),
('1','9','64 位架构的 A9 芯片'),
('1','10','16G / 64G / 128G'),
('1','11','2G'),
('1','12','不支持'),
('1','8','四核'),
('1','7','移动3G(TD-SCDMA),电信3G(CDMA2000),联通3G(WCDMA),联通2G/移动2G(GSM),电信2G(CDMA),电信4G(FDD-LTE),联通4G(FDD-LTE),移动4G(TD-LTE)'),
('1','6','1334*750'),
('1','5','4.5-5.0英寸'),
('1','4','2015年'),
('1','3','单卡单待'),
('1','2','IOS'),
('1','1','iPhone 6s A1700'),
('1','20','138.3*67.1*7.1mm'),
('1','21','1810'),
('1','22','4.7'),
('2','17','3.5MM'),
('2','16','支持'),
('2','15','800万'),
('2','14','1600万'),
('2','13','TF(microSD)卡'),
('2','11','3G'),
('2','12','128G'),
('2','10','32G'),
('2','9','麒麟950'),
('2','8','双四核'),
('2','7','移动3G(TD-SCDMA),联通2G/移动2G(GSM),移动4G(TD-LTE)'),
('2','6','1920*1080'),
('2','5','5.5英寸以上'),
('2','4','2015年'),
('2','3','双卡双待'),
('2','2','Android'),
('2','1','Mate8'),
('2','18','Nano SIM卡＋Nano SIM卡'),
('2','19','185'),
('2','20','157.1×80.6×7.9 mm'),
('2','21','4000'),
('2','22','6.0'),
('3','1','MI4'),
('3','2','Android'),
('3','3','双卡多模'),
('3','4','2014年'),
('3','5','5.0-5.5英寸'),
('3','6','1920x1080'),
('3','7','联通3G 电信3G 电信4G 联通4G 移动4G'),
('3','8','四核'),
('3','9','TM8674AC'),
('3','10','16G'),
('3','11','2G'),
('3','12','不支持'),
('3','13','不支持扩展卡'),
('3','14','1300万'),
('3','15','800万'),
('3','16','LED补光灯'),
('3','17','3.5MM'),
('3','18','小卡(Micro-SIM)'),
('3','19','150'),
('3','20','139.2×68.5×8.9 mm'),
('3','21','3080'),
('3','22','5.0英寸'),
('4','32','Intel HD Graphics 6000'),
('4','31','共享系统内存'),
('4','30','8GB'),
('4','29','DDR3-1600'),
('4','28','Intel Core i5-5250U(1.6GHz/L3 3M)'),
('4','27','4'),
('4','26','集成显卡'),
('4','25','Intel Core i5'),
('4','24','商务办公'),
('4','23','13'),
('4','33','摄像头, 蓝牙功能, 背光键盘'),
('4','34','USB 3.0'),
('4','35','约1.35'),
('4','36','2015'),
('4','37','17'),
('4','38','蓝牙4.0'),
('5','33','摄像头, 蓝牙功能, HDMI接口'),
('5','32','Intel GMA HD 520'),
('5','31','共享内存容量'),
('5','30','其他'),
('5','29','DDR3L (低电压版)'),
('5','28','Intel Core i5-6200U'),
('5','27','4'),
('5','26','独立显卡'),
('5','25','Intel Core i5'),
('5','24','游戏达人'),
('5','23','14'),
('5','34','USB 2.0/3.0, RJ45, 音频输出'),
('5','35','1.93'),
('5','36','2015年'),
('5','37','21.6'),
('5','38','蓝牙4.0'),
('6','23','13'),
('6','24','校园学生'),
('6','25','Intel 其他'),
('6','26','集成显卡'),
('6','27','4'),
('6','28','Intel 奔腾双核 4405U'),
('6','29','DDR3（低电压版）'),
('6','30','4GB'),
('6','31','4GB'),
('6','32','Intel GMA HD 510'),
('6','33','共享内存'),
('6','34','USB 2.0/3.0, RJ45, HDMI, 音频输出'),
('6','35','1.5'),
('6','36','2015年'),
('6','37','19.65'),
('6','38','蓝牙模块'),
('7','23','15'),
('7','24','家庭娱乐'),
('7','25','Intel Core i7'),
('7','26','独立显卡'),
('7','27','8'),
('7','28','Intel 酷睿i7 6500U'),
('7','29','LPDDR3'),
('7','30','8GB'),
('7','31','共享内存容量'),
('7','32','Intel GMA HD 520'),
('7','33','摄像头, 蓝牙功能, 读卡器, 扬声器'),
('7','34','音频输出, USB 3.0'),
('7','35','1.29'),
('7','36','2016年02月'),
('7','37','14.5'),
('7','38','蓝牙4.1'),
('8','48','GPS导航, 重力感应, 距离感应, 光线感应, 三轴陀螺仪, 支持蓝牙'),
('8','47','WiFi'),
('8','45','2048 x 1536'),
('8','46','苹果iOS'),
('8','42','2'),
('8','43','2014年'),
('8','44','IPS'),
('8','41','双核'),
('8','40','16'),
('8','39','8.1-10'),
('8','49','双摄像头, iSight 摄像头：800 万像素自动对焦； FaceTime 摄像头：120 万像素照片;'),
('8','50','437'),
('8','51','240 x 169.5 x 6.1'),
('8','52','金色 / 灰色 / 银色'),
('8','53','27.3 Whr'),
('9','39','7-8'),
('9','40','32'),
('9','41','双核'),
('9','42','2'),
('9','43','2015年01月'),
('9','44','IPS'),
('9','45','2048 x 1536'),
('9','46','Android 5.0'),
('9','47','WiFi'),
('9','48','6轴加速器，三轴陀螺仪，电子罗盘'),
('9','49','双摄像头'),
('9','50','318'),
('9','51','200.7×138.6×6.9'),
('9','52','天然铝灰色 / 火山灰色'),
('9','53','5300 mAh'),
('10','64','3个'),
('10','63','MP3'),
('10','62','无'),
('10','61','LED背光'),
('10','60','1080p(全高清)'),
('10','57','46-50英寸'),
('10','59','宽屏16:9'),
('10','58','1920*1080'),
('10','56','不支持'),
('10','55','非智能电视'),
('10','54','49PFL3445/T3'),
('11','63','USB视频播放,USB图片播放,USB音频播放'),
('11','62','Android'),
('11','61','LED背光'),
('11','60','1080p(全高清)'),
('11','57','46-50英寸'),
('11','59','宽屏16:9'),
('11','58','1920*1080'),
('11','56','不支持'),
('11','55','智能电视'),
('11','54','48PFL5445/T3'),
('10','65','2个'),
('10','66','内置底座'),
('10','67','外置挂架'),
('10','68','不支持'),
('11','64','2个'),
('11','65','2个'),
('11','66','内置底座'),
('11','67','外置挂架'),
('11','68','无线Wifi'),
('12','57','50-60英寸'),
('12','59','宽屏16:9'),
('12','58','3840*2160'),
('12','56','不支持'),
('12','55','4K超高清电视'),
('12','54','X8566D'),
('12','60','2160p'),
('12','62','Android'),
('12','64','4个'),
('13','1','Xplay5'),
('13','2','Android'),
('13','3','双卡多模'),
('13','4','2016年'),
('13','5','5.0-5.5英寸'),
('13','6','2560×1440'),
('13','7','移动4G、联通4G、电信4G'),
('13','8','八核'),
('13','9','Qualcomm 骁龙 八核 1.8GHz'),
('13','10','128GB'),
('13','11','4G'),
('13','12','不支持'),
('13','13','不支持'),
('13','14','1600万像素'),
('13','15','800万像素'),
('13','16','支持'),
('13','17','3.5mm'),
('13','18','Nano SIM卡'),
('13','19','168'),
('13','20','153.5 x 76.2 x 7.59'),
('13','21','3600'),
('13','22','5.43'),
('14','69','2'),
('14','70','立柜式'),
('14','71','变频'),
('14','72','冷暖电辅'),
('14','73','二'),
('15','69','1'),
('15','70','壁挂式'),
('15','71','定频'),
('15','72','冷暖电辅'),
('15','73','三'),
('16','1','Apple iPhone 6s Plus A1699'),
('16','2','IOS'),
('16','3','单卡单待'),
('16','4','2015年'),
('16','5','5.0-5.5英寸'),
('16','6','1920*1080'),
('16','7','移动3G, 电信3G, 联通3G, 电信4G, 联通4G ,移动4G'),
('16','8','四核'),
('16','9','64 位架构 A9 芯片'),
('16','10','16G / 64G /128G'),
('16','11','2G'),
('16','12','不支持'),
('16','13','不支持扩展卡'),
('16','14','1200万'),
('16','15','500万'),
('16','16','True Tone'),
('16','17','3.5mm'),
('16','18','小卡 nano-SIM'),
('16','19','192'),
('16','20','158.2 x 77.9 x 7.3 mm'),
('16','21','2915'),
('16','22','5.5');


DROP TABLE IF EXISTS `verydows_goods_cate`;
CREATE TABLE `verydows_goods_cate` (
  `cate_id` smallint(5) unsigned NOT NULL auto_increment,
  `parent_id` smallint(5) unsigned NOT NULL default '0',
  `cate_name` varchar(60) NOT NULL default '',
  `meta_keywords` varchar(240) NOT NULL default '',
  `meta_description` varchar(240) NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  PRIMARY KEY  (`cate_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_goods_cate` VALUES
('1','0','手机','手机','手机','1'),
('2','0','电脑','电脑','电脑','2'),
('3','0','家电','家电','家电','3'),
('4','0','汽车用品','汽车用品','汽车用品','4'),
('5','0','个护化妆','个护化妆','个护化妆','5'),
('6','0','珠宝饰品','珠宝饰品','珠宝饰品','6'),
('7','2','笔记本','笔记本','笔记本','2'),
('8','2','平板电脑','平板电脑','平板电脑','1'),
('9','3','电视','电视','电视','1'),
('10','0','食品酒类','食品酒类','食品酒类','99'),
('11','6','项链','项链','项链','1'),
('12','6','手镯','手镯','手镯','2'),
('13','6','戒指','戒指','戒指','3'),
('14','3','空调','空调','空调','99');


DROP TABLE IF EXISTS `verydows_goods_cate_attr`;
CREATE TABLE `verydows_goods_cate_attr` (
  `attr_id` mediumint(8) unsigned NOT NULL auto_increment,
  `cate_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(60) NOT NULL default '',
  `opts` text NOT NULL,
  `uom` varchar(20) NOT NULL default '',
  `filtrate` tinyint(1) unsigned NOT NULL default '0',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  PRIMARY KEY  (`attr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_goods_cate_attr` VALUES
('1','1','型号','','','0','99'),
('2','1','操作系统','[\"IOS\",\"Android\",\"Windows Phone\"]','','1','99'),
('3','1','待机模式','','','0','99'),
('4','1','上市时间','','','0','99'),
('5','1','屏幕大小','[\"4.5\\u82f1\\u5bf8\\u4ee5\\u4e0b\",\"4.5-5.0\\u82f1\\u5bf8\",\"5.0-5.5\\u82f1\\u5bf8\",\"5.5\\u82f1\\u5bf8\\u4ee5\\u4e0a\"]','','1','99'),
('6','1','主屏分辨率','','','0','99'),
('7','1','适用网络制式','','','0','99'),
('8','1','CPU核心数','[\"\\u53cc\\u6838\",\"\\u56db\\u6838\",\"\\u53cc\\u56db\\u6838\",\"\\u516b\\u6838\"]','','1','99'),
('9','1','CPU型号描述','','','0','99'),
('10','1','机身存储','','','0','99'),
('11','1','运行内存','[\"1G\",\"2G\",\"3G\",\"4G\"]','','1','99'),
('12','1','最大储存扩展','','','0','99'),
('13','1','扩展卡类型','','','0','99'),
('14','1','主摄像头像素','','','0','99'),
('15','1','副摄像头像素','','','0','99'),
('16','1','闪光灯','','','0','99'),
('17','1','耳机接口','','','0','99'),
('18','1','SIM卡类型','','','0','99'),
('19','1','重量','','g','0','99'),
('20','1','产品尺寸','','','0','99'),
('21','1','电池容量','','mAh','0','99'),
('22','1','屏幕尺寸','','英寸','0','99'),
('23','7','屏幕尺寸','[\"17\",\"15\",\"14\",\"13\",\"12\",\"11\",\"10\"]','英寸','1','99'),
('24','7','适用场景','[\"\\u5bb6\\u5ead\\u5a31\\u4e50\",\"\\u5546\\u52a1\\u529e\\u516c\",\"\\u6e38\\u620f\\u8fbe\\u4eba\",\"\\u6821\\u56ed\\u5b66\\u751f\",\"\\u5973\\u6027\\u65f6\\u5c1a\"]','','1','99'),
('25','7','CPU处理器','[\"Intel Core i3\",\"Intel Core i5\",\"Intel Core i7\",\"Intel \\u5176\\u4ed6\",\"AMD APU\",\"AMD \\u5176\\u4ed6\"]','','1','99'),
('26','7','显卡类型','[\"\\u72ec\\u7acb\\u663e\\u5361\",\"\\u96c6\\u6210\\u663e\\u5361\",\"\\u53cc\\u663e\\u5361\"]','','1','99'),
('27','7','内存容量','[\"1\",\"2\",\"4\",\"8\",\"16\"]','GB','1','99'),
('28','7','处理器型号','','','0','99'),
('29','7','内存类型','','','0','99'),
('30','7','最大支持内存容量','','','0','99'),
('31','7','显存容量','','','0','99'),
('32','7','显示芯片描述','','','0','99'),
('33','7','高级功能','','','0','99'),
('34','7','支持接口类型','','','0','99'),
('35','7','重量','','Kg','0','99'),
('36','7','上市时间','','','0','99'),
('37','7','厚度','','mm','0','99'),
('38','7','蓝牙','','','0','99'),
('39','8','屏幕尺寸','[\"7-8\",\"8.1-10\",\"10.1-11\",\"11.1-12\"]','英寸','1','99'),
('40','8','机身存储','[\"4\",\"8\",\"16\",\"32\",\"64\",\"128\"]','GB','1','99'),
('41','8','CPU核心数','[\"\\u53cc\\u6838\",\"\\u56db\\u6838\",\"\\u516b\\u6838\"]','','1','99'),
('42','8','内存容量','[\"1\",\"2\",\"3\",\"4\"]','GB','1','99'),
('43','8','上市时间','','','0','99'),
('44','8','屏幕材质','','','0','99'),
('45','8','最高分辨率','','','0','99'),
('46','8','操作系统','','','0','99'),
('47','8','联网模式','','','0','99'),
('48','8','附加功能','','','0','99'),
('49','8','摄像头','','','0','99'),
('50','8','重量','','g','0','99'),
('51','8','机身尺寸','','mm','0','99'),
('52','8','颜色','','','0','99'),
('53','8','电池容量','','','0','99'),
('54','9','型号','','','0','1'),
('55','9','类型','[\"\\u975e\\u667a\\u80fd\\u7535\\u89c6\",\"\\u667a\\u80fd\\u7535\\u89c6\",\"4K\\u8d85\\u9ad8\\u6e05\\u7535\\u89c6\"]','','1','2'),
('56','9','3D功能','[\"\\u652f\\u6301\",\"\\u4e0d\\u652f\\u6301\"]','','1','3'),
('57','9','屏幕尺寸','[\"32\\u82f1\\u5bf8\\u4ee5\\u4e0b\",\"32-37\\u82f1\\u5bf8\",\"37-46\\u82f1\\u5bf8\",\"46-50\\u82f1\\u5bf8\",\"50-60\\u82f1\\u5bf8\",\"60\\u82f1\\u5bf8\\u4ee5\\u4e0a\"]','','1','99'),
('58','9','最高分辨率','','','0','3'),
('59','9','屏幕比例','','','0','4'),
('60','9','清晰度','','','0','99'),
('61','9','背光灯类型','','','0','99'),
('62','9','操作系统','','','0','99'),
('63','9','USB支持音频格式','','','0','99'),
('64','9','HDMI接口','','','0','99'),
('65','9','USB接口','','','0','99'),
('66','9','底座配置','','','0','99'),
('67','9','壁挂配置','','','0','99'),
('68','9','联网功能','','','0','99'),
('69','14','功率','[\"1\",\"1.5\",\"2\",\"2.5\",\"3\"]','匹','1','99'),
('70','14','类型','[\"\\u58c1\\u6302\\u5f0f\",\"\\u7acb\\u67dc\\u5f0f\",\"\\u4e2d\\u592e\\u7a7a\\u8c03\"]','','1','99'),
('71','14','技术','[\"\\u53d8\\u9891\",\"\\u5b9a\\u9891\"]','','1','99'),
('72','14','冷暖方式','[\"\\u51b7\\u6696\\u578b\",\"\\u51b7\\u6696\\u7535\\u8f85\",\"\\u5355\\u51b7\\u578b\"]','','1','99'),
('73','14','能效等级','[\"\\u4e00\",\"\\u4e8c\",\"\\u4e09\",\"\\u56db\"]','级','1','99');


DROP TABLE IF EXISTS `verydows_goods_cate_brand`;
CREATE TABLE `verydows_goods_cate_brand` (
  `cate_id` smallint(5) unsigned NOT NULL,
  `brand_id` smallint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `verydows_goods_cate_brand` VALUES
('1','1'),
('1','2'),
('1','3'),
('7','8'),
('7','7'),
('7','6'),
('7','5'),
('7','4'),
('7','1'),
('7','9'),
('8','3'),
('8','9'),
('8','10'),
('8','1'),
('9','11'),
('9','9'),
('9','3'),
('9','12'),
('6','14'),
('6','13'),
('11','14'),
('11','13'),
('2','12'),
('2','11'),
('2','9'),
('2','8'),
('2','7'),
('2','6'),
('2','5'),
('2','4'),
('2','1'),
('8','18');


DROP TABLE IF EXISTS `verydows_goods_optional`;
CREATE TABLE `verydows_goods_optional` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type_id` smallint(5) unsigned NOT NULL default '0',
  `goods_id` mediumint(8) unsigned NOT NULL default '0',
  `opt_text` varchar(80) NOT NULL default '',
  `opt_price` decimal(10,2) unsigned NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_goods_optional` VALUES
('28','1','1','银色','0.00'),
('27','1','1','深空灰','0.00'),
('26','1','1','金色','0.00'),
('25','1','1','玫瑰金','50.00'),
('24','2','1','16G','0.00'),
('23','2','1','128G','1700.00'),
('22','2','1','64G','700.00'),
('48','1','2','苍穹灰','0.00'),
('47','1','2','月光银','0.00'),
('46','3','2','移动版','0.00'),
('45','3','2','全网通','200.00'),
('54','3','3','移动','0.00'),
('53','1','3','黑色','0.00'),
('52','1','3','白色','0.00'),
('55','3','3','电信','0.00'),
('56','3','3','联通','0.00'),
('61','2','4','256G','1333.00'),
('60','2','4','128G','0.00'),
('82','1','8','银色','0.00'),
('81','1','8','灰色','100.00'),
('80','1','8','金色','0.00'),
('79','2','8','128G WiFi版','1500.00'),
('78','2','8','16G WiFi版','0.00'),
('77','2','8','64G WiFi版','700.00'),
('83','1','16','玫瑰金','0.00'),
('84','1','16','金色','0.00'),
('85','1','16','深空灰','0.00'),
('86','1','16','银色','0.00'),
('87','2','16','16G','0.00'),
('88','2','16','64G','800.00'),
('89','2','16','128G','1800.00'),
('90','3','16','移动版','0.00'),
('91','3','16','全网通','200.00');


DROP TABLE IF EXISTS `verydows_goods_optional_type`;
CREATE TABLE `verydows_goods_optional_type` (
  `type_id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_goods_optional_type` VALUES
('1','颜色'),
('2','机身存储'),
('3','网络类型'),
('4','版本'),
('5','dfe飞');


DROP TABLE IF EXISTS `verydows_goods_related`;
CREATE TABLE `verydows_goods_related` (
  `goods_id` mediumint(8) NOT NULL,
  `related_id` mediumint(8) NOT NULL,
  `direction` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`goods_id`,`related_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `verydows_goods_related` VALUES
('10','11','2'),
('11','10','2'),
('16','1','2'),
('1','16','2');


DROP TABLE IF EXISTS `verydows_goods_review`;
CREATE TABLE `verydows_goods_review` (
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_goods_review` VALUES
('2','602890001521835','9','1','5','hehehehehe','1459737671','1','{\"admin\":\"admin\",\"content\":\"\\u5730\\u533a\\u5927\\u6c14\\u7684\\u6743\\u5a01\",\"dateline\":1459737717}');


DROP TABLE IF EXISTS `verydows_help`;
CREATE TABLE `verydows_help` (
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

DROP TABLE IF EXISTS `verydows_help_cate`;
CREATE TABLE `verydows_help_cate` (
  `cate_id` smallint(5) unsigned NOT NULL auto_increment,
  `cate_name` varchar(60) NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `verydows_login_security`;
CREATE TABLE `verydows_login_security` (
  `ip` char(15) NOT NULL,
  `err_count` tinyint(1) unsigned NOT NULL default '1',
  `expires` int(10) unsigned NOT NULL default '0',
  `lock_expires` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `ip_expires` (`ip`,`expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `verydows_navigation`;
CREATE TABLE `verydows_navigation` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `position` tinyint(1) unsigned NOT NULL default '0',
  `target` tinyint(1) unsigned NOT NULL default '0',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  `visible` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_navigation` VALUES
('1','后台管理','http://localhost/verydowsmaster/index.php?m=backend&c=main&a=index','0','1','1','1'),
('2','官方网站','http://www.verydows.com/','0','1','2','1'),
('3','我的订单','http://localhost/vds/user/order.html','1','1','1','1'),
('4','收藏夹','http://localhost/vds/user/index.html','1','1','2','1'),
('5','社区论坛','http://bbs.verydows.com/','2','1','1','1'),
('6','帮助文档','http://www.verydows.com/manual/starting.html','2','1','2','1'),
('7','相关下载','http://www.verydows.com/download/index.html','2','1','3','1'),
('8','Github','https://github.com/Verytops/verydows','2','1','4','1');


DROP TABLE IF EXISTS `verydows_oauth`;
CREATE TABLE `verydows_oauth` (
  `party` char(10) NOT NULL default '',
  `name` varchar(30) NOT NULL,
  `params` text NOT NULL,
  `instruction` varchar(240) NOT NULL default '',
  `enable` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`party`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `verydows_oauth` VALUES
('qq','QQ','{\"app_id\":\"101302962\",\"app_key\":\"dead9cc4c476b8d321fa92e97d6c0880\"}','QQ互联开放平台为第三方网站提供了丰富的API。第三方网站接入QQ互联开放平台后，即可通过调用平台提供的API实现用户使用QQ帐号登录网站功能，且可以获取到腾讯QQ用户的相关信息。','1'),
('weibo','新浪微博','{\"app_key\":\"3149024890\",\"app_secret\":\"fa92704f51fb903417a3704ba1e94509\"}','网站接入是微博针对第三方网站提供的社会化网络接入方案。接入微连接让您的网站支持用微博帐号登录，基于OAuth2.0协议，使用微博 Open API 进行开发， 即可用微博帐号登录你的网站， 让你的网站降低新用户注册成本，快速获取大量用户。','1');


DROP TABLE IF EXISTS `verydows_order`;
CREATE TABLE `verydows_order` (
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

INSERT INTO `verydows_order` VALUES
('552180004398401','1','{\"id\":\"1\",\"user_id\":\"1\",\"name\":\"\\u54c8\\u54c8\",\"province\":\"\\u5317\\u4eac\\u5e02\",\"city\":\"\\u5317\\u4eac\\u5468\\u8fb9\",\"borough\":\"\\u5bc6\\u4e91\\u53bf\",\"address\":\"fewfewfewfewfewf\",\"zip\":\"332000\",\"mobile_no\":\"18179239565\",\"tel_no\":\"\",\"is_default\":\"0\"}','1','2','0','7687.00','10.00','7697.00','','1459511203','0',''),
('553020002103193','1','{\"id\":\"1\",\"user_id\":\"1\",\"name\":\"\\u54c8\\u54c8\",\"province\":\"\\u5317\\u4eac\\u5e02\",\"city\":\"\\u5317\\u4eac\\u5468\\u8fb9\",\"borough\":\"\\u5bc6\\u4e91\\u53bf\",\"address\":\"fewfewfewfewfewf\",\"zip\":\"332000\",\"mobile_no\":\"18179239565\",\"tel_no\":\"\",\"is_default\":\"0\"}','1','2','4','7687.00','10.00','7697.00','发额外绯闻绯闻绯闻服务','1459514241','0',''),
('602490003464054','1','{\"id\":\"2\",\"user_id\":\"1\",\"name\":\"\\u674e\\u660e\",\"province\":\"\\u5409\\u6797\\u7701\",\"city\":\"\\u56db\\u5e73\\u5e02\",\"borough\":\"\\u94c1\\u4e1c\\u533a\",\"address\":\"\\u9752\\u5e74\\u8def555\\u597d\\u5662\",\"zip\":\"333000\",\"mobile_no\":\"13899996666\",\"tel_no\":\"8\",\"is_default\":\"1\"}','1','2','0','1099.00','10.00','1109.00','','1459692334','0',''),
('602550000270376','1','{\"id\":\"2\",\"user_id\":\"1\",\"name\":\"\\u674e\\u660e\",\"province\":\"\\u5409\\u6797\\u7701\",\"city\":\"\\u56db\\u5e73\\u5e02\",\"borough\":\"\\u94c1\\u4e1c\\u533a\",\"address\":\"\\u9752\\u5e74\\u8def555\\u597d\\u5662\",\"zip\":\"333000\",\"mobile_no\":\"13899996666\",\"tel_no\":\"8\",\"is_default\":\"1\"}','1','2','0','1099.00','10.00','1109.00','','1459692542','0',''),
('602820002375047','1','{\"id\":\"3\",\"user_id\":\"1\",\"name\":\"\\u5c0f\\u660e\",\"province\":\"\\u5317\\u4eac\\u5e02\",\"city\":\"\\u5e02\\u8f96\\u533a\",\"borough\":\"\\u897f\\u57ce\\u533a\",\"address\":\"\\u4e2d\\u56fd\\u4eba\\u6c11\\u5927\\u5b66\\u884c\\u77e5\\u697c\",\"zip\":\"100010\",\"mobile_no\":\"13900008888\",\"tel_no\":\"13900008888\",\"is_default\":\"1\"}','1','3','0','1099.00','10.00','1109.00','','1459693523','0',''),
('602890001521835','1','{\"id\":\"3\",\"user_id\":\"1\",\"name\":\"\\u5c0f\\u660e\",\"province\":\"\\u5317\\u4eac\\u5e02\",\"city\":\"\\u5e02\\u8f96\\u533a\",\"borough\":\"\\u897f\\u57ce\\u533a\",\"address\":\"\\u4e2d\\u56fd\\u4eba\\u6c11\\u5927\\u5b66\\u884c\\u77e5\\u697c\",\"zip\":\"100010\",\"mobile_no\":\"13900008888\",\"tel_no\":\"13900008888\",\"is_default\":\"1\"}','1','2','4','1099.00','10.00','1100.00','','1459693755','0',''),
('616240002090689','1','{\"id\":\"2\",\"user_id\":\"1\",\"name\":\"\\u674e\\u660e\",\"province\":\"\\u5409\\u6797\\u7701\",\"city\":\"\\u56db\\u5e73\\u5e02\",\"borough\":\"\\u94c1\\u4e1c\\u533a\",\"address\":\"\\u9752\\u5e74\\u8def555\\u597d\\u5662\",\"zip\":\"333000\",\"mobile_no\":\"13899996666\",\"tel_no\":\"8\",\"is_default\":\"0\"}','1','3','1','3199.00','10.00','3209.00','','1459741820','0','');


DROP TABLE IF EXISTS `verydows_order_goods`;
CREATE TABLE `verydows_order_goods` (
  `order_id` char(15) NOT NULL default '',
  `goods_id` mediumint(8) unsigned NOT NULL default '0',
  `goods_name` varchar(180) NOT NULL default '',
  `goods_image` varchar(30) NOT NULL default '',
  `goods_opts` varchar(255) NOT NULL default '',
  `goods_qty` smallint(5) NOT NULL default '1',
  `goods_price` decimal(10,2) unsigned NOT NULL default '0.00',
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `verydows_order_goods` VALUES
('552180004398401','9','诺基亚（Nokia）N1 7.9英寸平板电脑','956e4029db165b.jpg','','1','1099.00'),
('552180004398401','1','Apple iPhone 6s (A1700) 移动联通电信4G手机','156e2d4fa488d0.jpg','{\"27\":{\"type\":\"\\u989c\\u8272\",\"opt_text\":\"\\u6df1\\u7a7a\\u7070\"},\"23\":{\"type\":\"\\u673a\\u8eab\\u5b58\\u50a8\",\"opt_text\":\"128G\"}}','1','6588.00'),
('553020002103193','9','诺基亚（Nokia）N1 7.9英寸平板电脑','956e4029db165b.jpg','','1','1099.00'),
('553020002103193','1','Apple iPhone 6s (A1700) 移动联通电信4G手机','156e2d4fa488d0.jpg','{\"27\":{\"type\":\"\\u989c\\u8272\",\"opt_text\":\"\\u6df1\\u7a7a\\u7070\"},\"23\":{\"type\":\"\\u673a\\u8eab\\u5b58\\u50a8\",\"opt_text\":\"128G\"}}','1','6588.00'),
('602490003464054','9','诺基亚（Nokia）N1 7.9英寸平板电脑','956e4029db165b.jpg','','1','1099.00'),
('602550000270376','9','诺基亚（Nokia）N1 7.9英寸平板电脑','956e4029db165b.jpg','','1','1099.00'),
('602820002375047','9','诺基亚（Nokia）N1 7.9英寸平板电脑','956e4029db165b.jpg','','1','1099.00'),
('602890001521835','9','诺基亚（Nokia）N1 7.9英寸平板电脑','956e4029db165b.jpg','','1','1099.00'),
('616240002090689','2','华为 Mate8 3GB+32GB版 全网通 移动版 月光银 苍穹灰','256e2f30e7256c.jpg','{\"48\":{\"type\":\"\\u989c\\u8272\",\"opt_text\":\"\\u82cd\\u7a79\\u7070\"},\"45\":{\"type\":\"\\u7f51\\u7edc\\u7c7b\\u578b\",\"opt_text\":\"\\u5168\\u7f51\\u901a\"}}','1','3199.00');


DROP TABLE IF EXISTS `verydows_order_log`;
CREATE TABLE `verydows_order_log` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `order_id` char(15) NOT NULL default '',
  `admin_id` smallint(5) unsigned NOT NULL default '0',
  `operate` char(10) NOT NULL,
  `cause` varchar(240) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_order_log` VALUES
('1','602890001521835','1','amount','飞非我方违法','1459736680');


DROP TABLE IF EXISTS `verydows_order_shipping`;
CREATE TABLE `verydows_order_shipping` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `order_id` char(15) NOT NULL default '',
  `carrier_id` smallint(5) unsigned NOT NULL default '0',
  `tracking_no` varchar(20) NOT NULL default '',
  `memos` varchar(240) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_order_shipping` VALUES
('2','553020002103193','1','3243243243242','非我方违法违法','1459518840'),
('3','602890001521835','2','3134143242','','1459737611');


DROP TABLE IF EXISTS `verydows_payment_method`;
CREATE TABLE `verydows_payment_method` (
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

INSERT INTO `verydows_payment_method` VALUES
('1','余额支付','0','balance','','','1','1'),
('2','货到付款','1','cod','','','2','1'),
('3','支付宝','0','alipay','{\"seller_email\":\"\",\"partner\":\"\",\"key\":\"\"}','','3','1');


DROP TABLE IF EXISTS `verydows_role`;
CREATE TABLE `verydows_role` (
  `role_id` smallint(5) unsigned NOT NULL auto_increment,
  `role_name` varchar(50) NOT NULL default '',
  `role_brief` varchar(240) NOT NULL default '',
  `role_acl` text NOT NULL,
  PRIMARY KEY  (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `verydows_sendmail_limit`;
CREATE TABLE `verydows_sendmail_limit` (
  `ip` char(15) NOT NULL default '',
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `type` char(30) NOT NULL default '',
  `count` tinyint(1) unsigned NOT NULL default '1',
  `dateline` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `verydows_setting`;
CREATE TABLE `verydows_setting` (
  `sk` varchar(30) NOT NULL,
  `sv` text NOT NULL,
  `sc` varchar(20) NOT NULL default '0',
  PRIMARY KEY  (`sk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `verydows_setting` VALUES
('site_name','Verydows 开源电商系统','global'),
('home_title','Verydows 开源电子商务系统 | 轻松开启电商之旅','home'),
('home_keywords','','home'),
('home_description','','home'),
('footer_info','<div class=\"copyright mt10\"><p>联系邮箱：service@verydows.com<span class=\"sep\">|</span>QQ交流群：372701906<span class=\"sep\">|</span>Copyright © 2016 Verydows.com 版权所有</p></div>','global'),
('goods_search_per_num','20','goods'),
('upload_filetype','.jpg|.jpeg|.gif|.png|.bmp|.swf|.flv|.avi|.rmvb','other'),
('upload_filesize','2MB','other'),
('captcha_admin_login','2','captcha'),
('captcha_user_login','2','captcha'),
('captcha_user_register','1','captcha'),
('captcha_feedback','1','captcha'),
('smtp_server','smtp.163.com','smtp'),
('smtp_user','cy150526401@163.com','smtp'),
('smtp_password','xmdzknzsdaltpacf','smtp'),
('smtp_port','25','smtp'),
('smtp_secure','','smtp'),
('admin_mult_ip_login','0','global'),
('upload_goods_filesize','300KB','goods'),
('visitor_stats','0','global'),
('goods_hot_searches','','goods'),
('cate_goods_per_num','20','goods'),
('goods_history_num','5','goods'),
('goods_related_num','5','goods'),
('goods_review_per_num','10','goods'),
('upload_goods_filetype','.jpg|.png|.gif','goods'),
('show_goods_stock','0','goods'),
('order_cancel_expires','5','user'),
('goods_img_thumb','[{\"w\":350,\"h\":350},{\"w\":150,\"h\":150},{\"w\":100,\"h\":100},{\"w\":50,\"h\":50}]','goods'),
('goods_album_thumb','[{\"w\":350,\"h\":350},{\"w\":50,\"h\":50}]','goods'),
('enabled_theme','default','global'),
('user_consignee_limits','15','user'),
('upload_avatar_filesize','200KB','user'),
('order_delivery_expires','7','user'),
('user_register_email_verify','0','user'),
('user_review_approve','0','user'),
('rewrite_enable','1','global'),
('home_newarrival_num','5','home'),
('home_recommend_num','5','home'),
('home_bargain_num','5','home'),
('home_article_num','4','home'),
('data_cache_lifetime','7200','global'),
('goods_fulltext_query','0','goods'),
('debug','1','global'),
('rewrite_rule','{\"404.html\":\"main\\/404\",\"search.html\":\"goods\\/search\",\"item\\/<id>.html\":\"goods\\/index\",\"cate\\/<id>.html\":\"category\\/index\",\"news\\/<id>.html\":\"article\\/index\",\"help\\/<id>.html\":\"help\\/index\",\"<a>\\/img\":\"image\\/<a>\",\"index.html\":\"main\\/index\",\"<c>\\/<a>.html\":\"<c>\\/<a>\"}','global'),
('encrypt_key','bea4a37eda61b6102f7cacd3de54c43f','global');


DROP TABLE IF EXISTS `verydows_shipping_carrier`;
CREATE TABLE `verydows_shipping_carrier` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `tracking_url` varchar(255) NOT NULL default '',
  `service_tel` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_shipping_carrier` VALUES
('1','顺丰速运','http://www.sf-express.com/cn/sc/dynamic_functions/waybill/#search/bill-number/','95338'),
('2','中通快递','http://www.zto.cn/GuestService/Bill?txtbill=','95311');


DROP TABLE IF EXISTS `verydows_shipping_method`;
CREATE TABLE `verydows_shipping_method` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `params` text NOT NULL,
  `instruction` varchar(240) NOT NULL default '',
  `seq` tinyint(2) unsigned NOT NULL default '99',
  `enable` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_shipping_method` VALUES
('1','普通快递','{\"0\":{\"type\":\"fixed\",\"area\":\"0\",\"charges\":\"10\"}}','全国范围统一价10元','1','1');


DROP TABLE IF EXISTS `verydows_user`;
CREATE TABLE `verydows_user` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_user` VALUES
('1','cigery','707b5637abd9b8ab5ebb95a6b65494dc','150526401@qq.com','0','0','0ef32c536771aff8836e7184a54fe7d038cb1f4d');


DROP TABLE IF EXISTS `verydows_user_account`;
CREATE TABLE `verydows_user_account` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `balance` decimal(10,2) unsigned NOT NULL default '0.00',
  `points` int(10) unsigned NOT NULL default '0',
  `exp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `verydows_user_account` VALUES
('1','0.00','0','0');


DROP TABLE IF EXISTS `verydows_user_account_log`;
CREATE TABLE `verydows_user_account_log` (
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

DROP TABLE IF EXISTS `verydows_user_actinfo`;
CREATE TABLE `verydows_user_actinfo` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `created_date` int(10) unsigned NOT NULL default '0',
  `created_ip` char(15) NOT NULL default '0.0.0.0',
  `last_date` int(10) unsigned NOT NULL default '0',
  `last_ip` char(15) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `verydows_user_actinfo` VALUES
('1','1459248783','127.0.0.1','1459736395','127.0.0.1');


DROP TABLE IF EXISTS `verydows_user_consignee`;
CREATE TABLE `verydows_user_consignee` (
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_user_consignee` VALUES
('2','1','李明','7','3','2','青年路555好噢','333000','13899996666','8','0'),
('3','1','小明','1','1','2','中国人民大学行知楼','100010','13900008888','13900008888','1');


DROP TABLE IF EXISTS `verydows_user_favorite`;
CREATE TABLE `verydows_user_favorite` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `goods_id` mediumint(8) unsigned NOT NULL,
  `created_date` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `verydows_user_group`;
CREATE TABLE `verydows_user_group` (
  `group_id` smallint(5) unsigned NOT NULL auto_increment,
  `group_name` varchar(60) character set utf8 collate utf8_unicode_ci NOT NULL,
  `min_exp` int(10) unsigned NOT NULL default '0',
  `discount_rate` tinyint(3) unsigned NOT NULL default '100',
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `verydows_user_group` VALUES
('1','初级会员','0','100'),
('2','中级会员','2000','100'),
('3','高级会员','7000','100');


DROP TABLE IF EXISTS `verydows_user_oauth`;
CREATE TABLE `verydows_user_oauth` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `party` char(10) NOT NULL default '0',
  `oauth_key` char(32) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `verydows_user_profile`;
CREATE TABLE `verydows_user_profile` (
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

INSERT INTO `verydows_user_profile` VALUES
('1','好好奋斗','1570117d4e4e1d.png','1','1932','4','17','13899996655','1505264041','分为纷纷往返');


DROP TABLE IF EXISTS `verydows_visitor_stats`;
CREATE TABLE `verydows_visitor_stats` (
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

INSERT INTO `verydows_visitor_stats` VALUES
('faf75b3e98363175f1822994690c82f6','182.107.36.168','1458662400','1','localhost','2','1','江西省'),
('f31eaef4abf1270be8d2284bcfc808c4','182.107.36.168','1458748800','1','localhost','2','1','江西省'),
('dae39a0294c982c140f2889ea4908e45','182.107.34.101','1458921600','16','localhost','2','1','江西省'),
('09665cf2a46e0b94e0d6f211193ea5aa','182.107.34.101','1459008000','18','localhost','2','1','江西省'),
('09665cf2a46e0b94e0d6f211193ea5aa','182.107.34.101','1459094400','55','localhost','2','1','江西省'),
('09665cf2a46e0b94e0d6f211193ea5aa','182.107.39.107','1459094400','76','localhost','2','1','江西省'),
('3d969b509325640782d16187528624e1','182.107.39.107','1459180800','115','localhost','2','1','江西省'),
('e21f63d8a092a87778df707f0be4bb2e','182.107.37.14','1459353600','61','localhost','2','1','江西省'),
('91bbd00f93a516ebb9812450462851f1','182.107.37.14','1459440000','106','localhost','2','1','江西省'),
('e03cc93a2b1f61768b07358712dcdf26','182.107.33.37','1459526400','9','localhost','2','1','江西省'),
('62b018a4964197ff8c1caf26609e47c8','182.107.33.37','1459612800','9','localhost','2','1','江西省');


