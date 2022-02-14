#### 2022全网最完整、最新、最全、中国省市区街道sql/json 文件
---

### 效果演示
> [联动效果演示](https://gaohuazi.github.io/china_regions/)  
> [国内备用演示](https://static-16bf85f1-2181-4870-ac73-b170c68d178c.bspapp.com/)

### 更新记录
- **2022-02-14**：增加github action,每日自动抓取最新数据，[快速下载最新json/sql](https://github.com/gaohuazi/china_regions/actions/workflows/go.yml)
- **2022-02-13**：增加golang版爬虫源码
- **2022-02-08**：增加街道数据（4级联动），增加了香港、澳门、台湾、钓鱼岛相关数据~


### 分支说明

> - `master分支`：4级联动-包括省、市、区、街道
> - `level5分支`：5级联动-包括省、市、区、街道、社区，数据来源于[国家统计局](http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/)，数据比较old


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

##### 1.使用PHP版爬虫
clone项目后 直接执行 `php spider/php/index.php`  耗时约180s

##### 2.使用golang版爬虫
clone项目后 直接执行 `GO111MODULE=off go run spider/go/main.go` 耗时约30s

---
执行后会在当前目录生成最新json/sql文件

### 其他说明

```mysql
# 导入sql时报错时可以尝试以下方式导入
mysql -uroot -p --default-character-set=utf8 dbname < /path/community.sql
```

有问题请提issue，持续更新中.........


### 其他项目

> [美女图相册   （微信小程序）](https://vkceyugu.cdn.bspapp.com/VKCEYUGU-16bf85f1-2181-4870-ac73-b170c68d178c/874413a2-70d6-4283-bcc8-a0b2ade61fc6.jpg) ：海量高清美女图，宅男福利:-)  
> [淘宝领券助手（浏览器插件）](https://static-f7d1f66d-b388-4ba9-82f5-1d8ffc10e3ab.bspapp.com/) ：PC购物党必备，自动解析出淘宝京东优惠券级历史价格，购物不吃亏~  
> [花子领券助手（微信小程序）](https://vkceyugu.cdn.bspapp.com/VKCEYUGU-16bf85f1-2181-4870-ac73-b170c68d178c/9c55474e-4041-4810-9ba6-4f44b9cb92e3.jpg) ：一个领全网优惠券的小程序，包括外卖、打车、电影、淘宝、京东等优惠券  



### 打个广告

生活不易，打个广告：作者业余兼职开发小程序、网站、商城、APP，有需要的请加我微信 **gaohuazi520** ，请备注来意

- [腾讯云服务器：2核4G=74元/年](https://curl.qcloud.com/szErDb63)
