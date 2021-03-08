2021最新省市区sql/json 文件

数据来源于[国家统计局](http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/)

更新到国家2020-11-06日 统计的2020年省市区数据

有问题请提issue，持续更新中.........



> 分支说明：

> - `master分支`：3级联动-包括省、市、区
> - `level5分支`：5级联动-包括省、市、区、街道、社区


### 目录结构描述
<pre>
├── json                        // json文件目录
│   ├── province.json           // 省
│   ├── city.json               // 市
│   ├── area.json               // 区
│   ├── street.json             // 街道
│   ├── community.json          // 社区
│   └── region.json             // 包含所有省市区数据
├── sql                         // mysql文件目录
│   ├── province.sql            // 省
│   ├── city.sql                // 市
│   ├── area.sql                // 区
│   ├── street.json             // 街道
│   ├── community.json          // 社区
│   ├── init.sql                // mysql表结构文件, 需要5张表  
│   └── region.sql              // 包含所有省市区数据,只需1张表
├── LICENSE                     // MIT
└── Readme.md                   // help
</pre>

```mysql
#导入sql时报错时可以尝试以下方式导入
mysql -uroot -p --default-character-set=utf8 dbname < /path/community.sql
```