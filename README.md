## 项目概述

- 产品名称：mcshop 小商店
- 官方地址：https://github.com/yefangyong/mcshop

## 说明

基于 `Github` 开源项目 `litemall` 使用 `Laravel框架` 重构 `litemall` 项目中的 `Api` 接口，主要涉及的模块有团购模块，订单模块，优惠券模块，商品模块，用户登录注册模块等等，努力打造一个企业级项目。

![](https://gitee.com/yefangyong/blog-image/raw/master/png/20210104104203.png)

## 功能如下

### 用户模块

- 账号注册/登录/登出
- 短信验证码
- 密码重置
- 用户信息展示
- 用户信息修改
- 地址列表
- 地址详情
- 新建收获地址
- 删除收获地址

### 订单模块

- 购物车列表/添加/删除
- 选择/取消购物车产品
- 下单
- 确认商品
- 提交订单
- 支付/超时取消
- 订单列表/详情

### 商品模块

- 类目列表
- 类目信息
- 品牌列表
- 品牌详情
- 商品数量统计
- 商品列表/详情

### 营销模块

- 优惠券列表
- 优惠券领域与使用
- 团购列表
- 团购支付
- 团购分享

### 支付模块

- 微信支付
- 支付宝支付

## 运行环境要求

- Nginx 1.8+
- PHP 7.2+
- Mysql 5.7+
- Redis 3.0+

> 本项目可以基于 laradock 一键启动服务

## 开发环境部署/安装

本项目使用 `PHP` 框架 `Laravel7.x` 进行开发，运行环境基于docker集成环境 `laradock`，下文在假定读者已经安装好 `docker` 环境下进行说明。

### 基础安装

1、克隆源代码

克隆 `mcshop` 代码到本地

```
git clone https://github.com/yefangyong/mcshop.git
```

2、安装扩展包依赖

```
composer install
```

3、生成 `Laravel`  框架的配置文件

```
cp .env.example .env
```

根据情况修改成自己的配置，比如邮件发送配置，数据库配置，微信支付配置，支付宝支付配置等等

```
APP_URL=http://larabbs.test
...
DB_HOST=localhost
DB_DATABASE=larabbs
DB_USERNAME=homestead
DB_PASSWORD=secret

DOMAIN=.larabbs.test
```

4、 配置本地的 `laradock` 环境

进入laradock目录，生成配置文件

```
cp .env.example .env
```

如果某些端口被占用，需要修改配置，改成其他的端口

5、修改配置文件 .env

```
APP_CODE_PATH_HOST = ../../ 
```

6、启动框架运行环境

```
docker-compose up -d workspace redis mysql nginx
```

至此 `php` 运行环境搭建完成

7、导入数据到数据库中

`sql` 文件在 `sql` 目录中，使用数据库工具导入数据即可，比如 `navicat` 等

### 前端框架安装

> 前端代码在 `H5` 文件夹中

1). 安装 node.js

直接去官网 [https://nodejs.org/en/](https://nodejs.org/en/) 下载安装最新版本。

2). 安装 Yarn

请安装最新版本的 Yarn —— http://yarnpkg.cn/zh-Hans/docs/install

3). 安装依赖包

```shell
yarn install
```

4). 编译前端内容

```shell
// 运行所有 Mix 任务...
npm run dev

// 运行所有 Mix 任务并缩小输出..
npm run production
```

5). 监控修改并自动编译

```shell
npm run watch

// 在某些环境中，当文件更改时，Webpack 不会更新。如果系统出现这种情况，请考虑使用 watch-poll 命令：

npm run watch-poll
```

## 扩展包使用情况

| 扩展包 | 一句话描述 | 本项目应用场景 |
| --- | --- | --- | --- | --- | --- | --- | --- |
| [tymondesigns/jwt-auth](github.com/tymondesigns/jwt-auth) | Jwt组件 | 用于登录注册 |
| [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper) | 代码提示组件 | 用户模型和门面代码提示  |
| [Bacon/BaconQrCode](https://github.com/Bacon/BaconQrCode) | 图片生成和裁剪组件 | 用于团购二维码的生成 |
| [yansongda/pay](https://github.com/yansongda/pay) | 微信支付和支付宝组件 | 用于微信支付和支付宝支付 |


## 自定义 Artisan 命令

| 命令行名字 | 说明 | Cron | 代码调用 |
| --- | --- | --- | --- |
| `php artisan schedule:run` |  超时未确认自动收货 | 每天夜里3点运行 | 无 |

## 队列清单

| 名称 | 说明 | 调用时机 |
| --- | --- | --- |
| php artisan queue:work | 超时未支付取消订单 | 用户提交订单超过30分钟未支付时 |
