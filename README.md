## 2023 全网最完整、最新、最全、中国省市区街道 sql/json 文件

---

### 效果演示

> [联动效果演示](https://gaohuazi.github.io/china_regions/)  
> [国内备用演示](http://static.i920.wang/china_regions/index.html)

![image](http://static.i920.wang/china_regions/city.gif)

### 更新记录

-   **2022-03-10**：增加 github action,每日自动抓取最新数据，[快速下载最新 json/sql](https://github.com/gaohuazi/china_regions/actions/workflows/go.yml)![快速下载步骤，需科学上网](docs/GIF%202022-05-31%20%E6%98%9F%E6%9C%9F%E4%BA%8C%207-58-34.gif)
-   **2022-02-13**：增加 golang 版爬虫源码
-   **2022-02-08**：增加 PHP 版爬虫源码
-   **2022-02-08**：`master`增加街道数据（4 级联动），增加了香港、澳门、台湾、钓鱼岛相关数据~

### 分支说明

> -   `master分支`：4 级联动-包括省、市、区、街道，数据来源于[京东网页版](https://misc.360buyimg.com/jdf/1.0.0/ui/area/1.0.0/area.js)，
> -   `level5分支`：5 级联动-包括省、市、区、街道、社区，数据来源于[国家统计局](http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/)，数据比较 old

### 目录结构描述

<pre>
├── json                        // json文件目录
│   ├── province.json           // 省
│   ├── city.json               // 市
│   ├── area.json               // 区
│   └── street.json             // 街道
├── sql                         // mysql文件目录
│   ├── province.sql            // 省
│   ├── city.sql                // 市
│   ├── area.sql                // 区
│   ├── street.sql              // 街道
│   └── init.sql                // mysql表结构文件, 需要4张表
├── spider                      // 爬虫源文件目录
│   ├── go                      // golang版爬虫
│   │   ├── main.go             // demo入口文件
│   │   └── core                // 核心类库文件
│   └── php                     // php版爬虫
│       ├── index.php           // demo入口文件
│       └── Region.class.php    // 核心类库文件
├── docs                        // github联动效果演示文件，忽略
├── LICENSE                     // MIT
└── Readme.md                   // help
</pre>

### 如何抓取最新数据

##### 1.使用 PHP 版爬虫

clone 项目后 直接执行 `php spider/php/index.php` 耗时约 180s

##### 2.使用 golang 版爬虫

clone 项目后 直接执行 `GO111MODULE=off go run spider/go/main.go` 耗时约 30s

---

执行后会在当前目录生成最新 json/sql 文件

### 其他说明

```mysql
# 导入sql时报错时可以尝试以下方式导入
mysql -uroot -p --default-character-set=utf8 dbname < /path/community.sql
```

有问题请提 issue，持续更新中.........
