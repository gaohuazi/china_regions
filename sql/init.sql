-- 三张表
DROP TABLE IF EXISTS `province`;
CREATE TABLE IF NOT EXISTS `province` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '省份名称',
  `province_code` mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '省份代码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='省份表';

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '城市名称',
  `city_code` mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '城市代码',
  `province_code` mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '省份代码',
  PRIMARY KEY (`id`),
  KEY `province_code` (`province_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='城市表';

DROP TABLE IF EXISTS `area`;
CREATE TABLE IF NOT EXISTS `area` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '区域名称',
  `area_code` mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区域代码',
  `city_code` mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '城市代码',
  PRIMARY KEY (`id`),
  KEY `city_code` (`city_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='区域表';

