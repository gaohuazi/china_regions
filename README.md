2021最新省市区sql/json 文件

**最新更新（2021年6月19日）**：`master分支`数据源已更新为[腾讯位置服务（行政区划）API](https://lbs.qq.com/service/webService/webServiceGuide/webServiceDistrict)，增加了香港、澳门、台湾相关数据~



有问题请提issue，持续更新中.........



> 分支说明：
> - `master分支`：3级联动-包括省、市、区，数据来源[腾讯位置服务（行政区划）API](https://lbs.qq.com/service/webService/webServiceGuide/webServiceDistrict)
> - `level5分支`：5级联动-包括省、市、区、街道、社区，数据来源于[国家统计局](http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/)，数据比较old



### 目录结构描述
<pre>
├── json                        // json文件目录
│   ├── province.json           // 省
│   ├── city.json               // 市
│   ├── area.json               // 区
│   └── region.json             // 包含所有省市区数据
├── sql                         // mysql文件目录
│   ├── province.sql            // 省
│   ├── city.sql                // 市
│   ├── area.sql                // 区
│   ├── init.sql                // mysql表结构文件, 需要5张表  
│   └── region.sql              // 包含所有省市区数据,只需1张表
├── LICENSE                     // MIT
└── Readme.md                   // help
</pre>

```mysql
# 导入sql时报错时可以尝试以下方式导入
mysql -uroot -p --default-character-set=utf8 dbname < /path/community.sql
```

---
生活不易，打个广告：博主业余兼职开发小程序、网站、商城、APP，有需要的请加我微信，请备注gitee

<img src="https://vkceyugu.cdn.bspapp.com/VKCEYUGU-16bf85f1-2181-4870-ac73-b170c68d178c/9a095a7c-a4e0-419f-a384-ddca625c9a35.png" alt="扫码加我微信" title="扫码加我微信" style="zoom:80%;" />
<img src="https://vkceyugu.cdn.bspapp.com/VKCEYUGU-16bf85f1-2181-4870-ac73-b170c68d178c/0721e41d-ea66-4ab8-9ab2-52a48e9231a2.png" alt="阿里云" title="阿里云" style="zoom:80%;" />
<img src="https://vkceyugu.cdn.bspapp.com/VKCEYUGU-16bf85f1-2181-4870-ac73-b170c68d178c/4077f381-c7e1-4219-87c9-e8902e789fbd.png" alt="腾讯云" title="腾讯云" style="zoom:80%;" />

