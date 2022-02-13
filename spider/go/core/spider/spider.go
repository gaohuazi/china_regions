package spider

import (
	"bytes"
	"fmt"
	"path/filepath"

	"../city"
	"../province"
	"../util"
)

type Item = province.Item
type Spider struct {
	Province province.Province
	City     city.City
	Area     city.City
	Street   city.City
	OutPath  string
}

func NewSpider() *Spider {
	_province := province.Province{}
	_city := city.City{Data: make(map[int][]Item)}
	_area := city.City{Data: make(map[int][]Item)}
	_street := city.City{Data: make(map[int][]Item)}

	outPath, _ := filepath.Abs("./")
	// path, _ := os.Getwd()
	// fmt.Println(path)
	// outPath := filepath.Join(path, "../../")

	return &Spider{Province: _province, City: _city, Area: _area, Street: _street, OutPath: outPath}
}

func (this *Spider) Run() {
	this.initData()
	this.toJson()
	this.toSql()
}

func (this *Spider) initData() {
	this.Province.Run()
	this.City.Run(this.Province.Data)

	for _, itemSlice := range this.City.Data {
		this.Area.Run(itemSlice)
	}

	for _, itemSlice := range this.Area.Data {
		this.Street.Run(itemSlice)
	}
}

func (this *Spider) toJson() {
	util.WriteFile(filepath.Join(this.OutPath, "./json/province.json"), this.Province.Json)
	util.WriteFile(filepath.Join(this.OutPath, "./json/city.json"), this.City.Json)
	util.WriteFile(filepath.Join(this.OutPath, "./json/area.json"), this.Area.Json)
	util.WriteFile(filepath.Join(this.OutPath, "./json/street.json"), this.Street.Json)
}

func (this *Spider) toSql() {
	util.WriteFile(filepath.Join(this.OutPath, "./sql/init.sql"), this.getSqlDesc())
	util.WriteFile(filepath.Join(this.OutPath, "./sql/province.sql"), this.getProvinceSql())
	util.WriteFile(filepath.Join(this.OutPath, "./sql/city.sql"), this.getRegionSql("city"))
	util.WriteFile(filepath.Join(this.OutPath, "./sql/area.sql"), this.getRegionSql("area"))
	util.WriteFile(filepath.Join(this.OutPath, "./sql/street.sql"), this.getRegionSql("street"))
}

func (this *Spider) getSqlDesc() string {
	sql := `
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
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='街道表';`
	return sql
}

func (this *Spider) getProvinceSql() string {
	var buf bytes.Buffer
	buf.WriteString("INSERT INTO `province` (`id`, `name`) VALUES ")
	l := len(this.Province.Data)
	for i, v := range this.Province.Data {

		if i != l-1 {
			buf.WriteString(fmt.Sprintf("\n(%d,'%s'),", v.Id, v.Name))
		} else {
			buf.WriteString(fmt.Sprintf("\n(%d,'%s');", v.Id, v.Name))
		}
	}

	return buf.String()
}

func (this *Spider) getRegionSql(dbname string) string {
	var buf bytes.Buffer
	var dataList map[int][]Item
	switch dbname {
	case "city":
		dataList = this.City.Data
	case "area":
		dataList = this.Area.Data
	case "street":
		dataList = this.Street.Data
	default:
		return ""
	}

	buf.WriteString(fmt.Sprintf("INSERT INTO `%s` (`id`, `name`,`pid`) VALUES ", dbname))
	for pid, childSlice := range dataList {
		for _, v := range childSlice {
			buf.WriteString(fmt.Sprintf("\n(%d,'%s',%d),", v.Id, v.Name, pid))
		}
	}
	byteSlice := bytes.TrimRight(buf.Bytes(), ",")
	return string(append(byteSlice, []byte(";")...))
}
