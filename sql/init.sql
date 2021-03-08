-- 五张表
DROP TABLE IF EXISTS `province`;
CREATE TABLE IF NOT EXISTS `province` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '省份名称',
  `province_code` bigint(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '省份代码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='省份表';

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `id` smallint(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '城市名称',
  `city_code` bigint(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '城市代码',
  `province_code` bigint(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '省份代码',
  PRIMARY KEY (`id`),
  KEY `province_code` (`province_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='城市表';

DROP TABLE IF EXISTS `area`;
CREATE TABLE IF NOT EXISTS `area` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '区域名称',
  `area_code` bigint(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区域代码',
  `city_code` bigint(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '城市代码',
  PRIMARY KEY (`id`),
  KEY `city_code` (`city_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='区域表';

DROP TABLE IF EXISTS `street`;
CREATE TABLE IF NOT EXISTS `street` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '街道名称',
  `street_code` bigint(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '街道代码',
  `area_code` bigint(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区域代码',
  PRIMARY KEY (`id`),
  KEY `area_code` (`area_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='街道表';

DROP TABLE IF EXISTS `community`;
CREATE TABLE IF NOT EXISTS `community` (
  `id` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '社区名称',
  `community_code` bigint(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '社区代码',
  `street_code` bigint(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '街道代码',
  PRIMARY KEY (`id`),
  KEY `street_code` (`street_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='社区表';
