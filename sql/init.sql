
    -- 省、市、区、街道，四张表
    DROP TABLE IF EXISTS province;
    CREATE TABLE IF NOT EXISTS province (
      id mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      name varchar(30) NOT NULL DEFAULT '' COMMENT '省份名称',
      PRIMARY KEY (id)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='省份表';

    DROP TABLE IF EXISTS city;
    CREATE TABLE IF NOT EXISTS city (
      id mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      name varchar(60) NOT NULL DEFAULT '' COMMENT '城市名称',
      pid mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '省份id',
      PRIMARY KEY (id),
      KEY pid (pid)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='城市表';

    DROP TABLE IF EXISTS area;
    CREATE TABLE IF NOT EXISTS area (
      id mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      name varchar(60) NOT NULL DEFAULT '' COMMENT '区域名称',
      pid mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '城市id',
      PRIMARY KEY (id),
      KEY pid (pid)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='区域表';

    DROP TABLE IF EXISTS street;
    CREATE TABLE IF NOT EXISTS street (
      id mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      name varchar(60) NOT NULL DEFAULT '' COMMENT '街道名称',
      pid mediumint(6) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区域id',
      PRIMARY KEY (id),
      KEY pid (pid)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='街道表';