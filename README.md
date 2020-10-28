
speedAdmin-基于hyperf的后台快速构建系统

## 介绍
speedAdmin是一款快速构建后台的轻量级系统，有别于传统的前后端分离的框架，对于后端开发人员更加友好，大大降低学习成本。在使用上和传统的Thinkphp、Laravel框架无异。但是有别于传统FPM环境，性能上是成倍的提升。

## 环境要求
- PHP >= 7.2
- Swoole PHP 扩展 >= 4.4，并关闭了 Short Name
- OpenSSL PHP 扩展
- JSON PHP 扩展
- PDO PHP 扩展 （如需要使用到 MySQL 客户端）
- Redis PHP 扩展 （如需要使用到 Redis 客户端）
- mysql>5.6
- redis

## 特色
- 脚手架一键生成CRUD
- 自动化的权限节点管理
- 基于Swoole常驻内存运行模式，运行速度快
- 支持灵活的业务对接权限

## 具备的模块
- 用户模块：登录，忘记密码，编辑资料，修改密码
- 基于auth的权限模块，预留了业务对接模块
- 基于权限的目录渲染和按钮显示
- 操作日志模块

## 安装
- 推荐Docker下运行
```
# 下载并运行 hyperf/hyperf 镜像
docker pull hyperf/hyperf

# 将镜像内的项目目录绑定到宿主机的目录
docker run -v 主机目录:容器目录 -p 9501:9501 -it --entrypoint /bin/sh hyperf/hyperf:latest

# 镜像容器运行后，在容器内安装 Composer
wget https://github.com/composer/composer/releases/download/1.8.6/composer.phar
chmod u+x composer.phar
mv composer.phar /usr/local/bin/composer

# 将 Composer 镜像设置为阿里云镜像，加速国内下载速度
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer

# 通过git克隆项目到主机目录中
git clone https://gitee.com/yangyigit/speedAdmin.git

# 进入安装好的 Hyperf 项目目录
cd 容器目录

# 启动 Hyperf
php bin/hyperf.php start
```
- 导入database目录下的数据库文件


## Nginx配置代理
```
# 配置静态资源存放路径
location /static {
     root \www\wwwroot\code\public;
}

location / {
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

    # 执行代理访问真实服务器
     proxy_pass http://127.0.0.1:9501;
}
```

## 使用说明

1.  [后台代码生成器](./books/STAGINGCODE.md)
2.  [按钮权限设置](./books/BTNAUTHSHOW.md)
3.  [后台搜索设置](./books/SEARCH.md)
4.  [自动化权限节点配置](./books/AUTOAUTH.md)