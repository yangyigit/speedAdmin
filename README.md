
speedAdmin-基于hyperf的后台快速构建系统

## 介绍
speedAdmin是一款快速构建后台的轻量级工具

## 环境要求
- PHP >= 7.2
- Swoole PHP 扩展 >= 4.5
- mysql>5.6
- redis

## 具备的模块
- 用户模块：登录，忘记密码，编辑资料，修改密码
- 基于auth的权限模块，预留了业务对接模块
- 基于权限的目录渲染和按钮显示
- 操作日志模块
- 后台登录页令牌授权模块（非法用户找不到地址）


## 好用在哪
### 1. 一行命令完成一个表管理

php think Staging 
--class Order 
--table order 
--explain_class 订单管理 
--author liuyexing

自动完成增、删、改、查的业务，自动完成视图层和控制器层代码的编写


### 2. 自动化的权限节点管理

规则管理中，点击按钮自动同步权限节点

### 3. 前端强大

前后端半分离，页面无刷新，屏幕自动响应，自动适配PC、平板、手机

### 4. 无缝切换运行模式
传统php-fpm和swoole无缝切换

php-fpm：传统方式，请求完成内存释放，线上随时启动，没有内存泄漏

swoole：守护进程类似 java，自动启动数据库连接池，性能10倍提升

### 5. 支持灵活的业务对接权限

规则表中增加判断字段和判断规则，会自动去用户表中去查看对应的字段值，完成权限判断

例如：规则表中规则字段，{score}>5  and {score}<100  
表示用户表中的 score 字段在5-100之间时这条规则才会通过。



## 安装教程

代码文件在server文件夹

数据库文件在sql文件夹

## 使用说明

1.  [后台代码生成器](./books/AUTOCODE.md)
2.  [按钮权限设置](./books/BTNAUTHSHOW.md)
3.  [后台搜索设置](./books/SEARCH.md)
php bin/hyperf.php custom:staging -C Admin -T admin -S id -E 11 -A yang
