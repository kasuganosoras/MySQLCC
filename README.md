# MySQLCC
MySQL 压力测试工具，使用 PHP 编写

__请注意：本项目仅用于学习和测试，请勿用于违法行为，造成的一切后果由使用者自行承担。__

这是一个使用 PHP 编写的 MySQL 性能测试工具，用于检测数据库的性能，可承载的并发数量等。

> 经过测试，一台 I5-4590 CPU，12GB 内存的机器，在被 1024 线程攻击后，CPU 瞬间上到 90%，内存占用率以每秒增加 50-100MB 的速度持续增长，并且在攻击结束后都没有下降，数据库软件为 Mariadb 10.3，所有设置均为默认。

![img](https://i.loli.net/2019/07/02/5d1a61f2f41f880202.png)

<p align="center">攻击开始前目标主机工作正常，可用内存接近 4GB</p>

![img](https://i.loli.net/2019/07/02/5d1a62da8fa7831631.png)

<p align="center">攻击开始一分钟后 CPU 跑满，内存几乎已经用完</p>

三分钟后，目标主机因为内存不足导致死机，只能硬重启。

在我另一台 Atom CPU、2GB 内存的机器上测试更是直接秒杀，1 分钟左右就已经死机，SSH 连接断开，只能强制重启。

## 需要环境
运行 MySQLCC 需要以下环境 / 组件支持

1. PHP 7.2 及以上
2. PHP MySQLi 组件
3. Pthreads 组件

## 安装 Pthreads
首先检查自己的 PHP 是不是 Zend Thread Safety：

> 命令行输入 `php -v`，如果看到 `ZTS` 说明是线程安全的，如果是 `NTS` 则说明不是线程安全。

将 pthreads 代码克隆到本地并编译：

```
git clone https://github.com/krakjoe/pthreads
cd pthreads/
phpize
./configure
make
make install
```

接下来修改 php.ini，在结尾增加一行：

```
extension=pthreads.so
```

如果运行 `php -v` 命令时提示 `No such file` 之类的字样，请尝试将 `make install` 之后提示的路径加到 `pthreads.so` 前面

```
extension=/usr/local/php/include/php/ext/pthreads.so
```

## 运行方式
下载或克隆本项目到您的服务器上，然后运行以下命令进行测试：

```
php mysqlcc.php <目标主机> <端口号> <线程数> [随机数据模式] [用户名] [密码] [数据库名]
```

说明：
- 选项里，`<>` 是必填的，`[]` 是可选的。
- 目标主机可以是 IP 或者域名，域名会自动解析为 IP。
- 端口号默认是 3306 端口。
- 线程数推荐 256-1024 之间，视服务器性能而定，线程并非越大越好。
- 随机数据模式的值是 `true` 启用或者 `false` 关闭。
- 随机数据模式关闭时，需要自己手动指定用户名、密码和数据库名。

## 如何防止 MySQL 被攻击
最简单就是修改 MySQL 端口，不过这是治标不治本，如果没有必要，把 3306 端口用防火墙屏蔽即可。

如果一定要外部连接，建议屏蔽外部访问 3306，然后用 Frp 等工具进行反向代理，或者设置防火墙 IP 白名单。

## 开源协议
本项目使用通用公共许可协议 v3（GPL v3）开放源代码
