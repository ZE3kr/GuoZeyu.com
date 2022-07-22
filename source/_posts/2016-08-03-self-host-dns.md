---
title: 自建 PowerDNS 智能解析服务器
tags:
  - DNS
  - VPS
id: '1832'
categories:
  - - 开发
date: 2016-08-03 09:24:21
languages:
  en-US: https://ze3kr.com/2016/08/self-host-dns/
---

最近我越来越喜欢自建一些东西，比如 GitLab。今天我又把 DNS 服务器改成自建的了，分享一下经验（PS：现在为了实现[根域名 CDN](https://guozeyu.com/2017/01/wordpress-full-site-cdn/)，我用换成了 Route 53）：

本文的自建 DNS 是指的是权威 DNS，即给自己的域名配置的 DNS，而非在客户端配置的缓存 DNS。

## 优缺点

首先，我先说用自建 DNS 服务器的**致命坏处**：

1.  如果那天自己的服务器挂了，整个域名相关服务都会挂，即使你邮件收信服务器是用的是第三方的，你也不能收信了
2.  基本上必须是开放端口，并有**固定 IP**（而且最好还需要至少两个 IP） 的 VPS（当然也可以是两个主机，只需要保证配置文件完全相同即可），对于服务提供商要求高
3.  个人一般关于 DNS 运维经验不足，容易导致配置错误
4.  第三方 DNS 提供商基本都有 DDOS 防御，而你的服务器可不一定有，攻击者可以直接通过 L7 DNS Flood 攻击掉你的服务器，然后又回到第一个问题上了

使用自建 DNS 服务器优点：
<!-- more -->

1.  可 DIY 程度极大，各种 DNS 方面功能几乎都能配置，但却又都十分复杂
2.  可以建在已有的服务器上，不用额外花钱

最终我还是选择了使用 PowerDNS 软件（这其实也是很多提供 DNS 服务的服务商所使用的），我安装它的最近才出的 4.0 版本，这个版本支持的一些特性：

*   EDNS Client Subnet
*   DNSSEC
*   GEODNS
*   IPv6

等等，以上只是我想到的。同时 PowerDNS 支持超多的解析记录种类（至少是我目前见过最多的）：A、AAAA、AFSDB、ALIAS（也是 ANAME）、CAA、CERT、CDNSKEY、CDS、CNAME、DNSKEY、DNAME、DS、HINFO、KEY、LOC、MX、NAPTR、NS、NSEC、NSEC3、NSEC3PARAM、OPENPGPKEY、PTR、RP、RRSIG、SOA、SPF、SSHFP、SRV、TKEY、TSIG、TLSA、TXT、URI 等，还有不常用的没有列出来，见[所有支持的记录](https://doc.powerdns.com/md/types/)。说实话有一些冷门的记录很多解析商都不支持，但我又需要用，比如 LOC、SSHFP 和 TLSA。不知道这一堆记录是干什么的？请见[维基百科](https://en.wikipedia.org/wiki/List_of_DNS_record_types)。

## 简述安装过程

详情安装方法[见官方文档](https://doc.powerdns.com/md/authoritative/installation/)，需要先安装 `pdns-server` ，然后再安装 `pdns-backend-$backend` 。Backend 是你可以自己选的，常用的有 `BIND` 和 `Generic MySQL` ，需要 GEODNS 可以用 `GEOIP` ，所有列表[见此](https://doc.powerdns.com/md/authoritative/)。如果想做网页版控制后台，使用 MySQL 的可能比较方便。如果只是通过文件形式控制，那么 BIND 和 GEOIP 都可以。 我使用 GEOIP 版本的，GEOIP 版本可拓展性强，使用 YAML 文件，更灵活、优雅，本文就讲讲 GEOIP 版本： 在 Ubuntu 上安装（系统软件源里就有）：

$ sudo apt install pdns-server
$ sudo apt install pdns-backend-geoip

然后修改配置文件：

```
$ rm /etc/powerdns/pdns.d/* # 删除 Example
```

### 安装更新版本的 PowerDNS

很多特性，如 CAA 记录等，需要新版 PowerDNS。请[前往官网配置软件源](https://repo.powerdns.com)。

### 安装地理位置数据库

注意，你应该已经有 MaxMind GeoIP Lite 数据库，如果没有，通过如下方式安装：

> 重要更新⚠️：2018 年 4 月 1 日起已经无法通过软件自动下载到 DAT 格式的 GeoIP 数据库，请[前往官网手动下载对应数据库](https://dev.maxmind.com/geoip/legacy/geolite/)。需要的是 Binary 格式的。

创建文件 `/etc/GeoIP.conf` 内容是：

```
# The following UserId and LicenseKey are required placeholders:
UserId 999999
LicenseKey 000000000000
# Include one or more of the following ProductIds:
# * GeoLite2-City - GeoLite 2 City
# * GeoLite2-Country - GeoLite2 Country
# * GeoLite-Legacy-IPv6-City - GeoLite Legacy IPv6 City
# * GeoLite-Legacy-IPv6-Country - GeoLite Legacy IPv6 Country
# * 506 - GeoLite Legacy Country
# * 517 - GeoLite Legacy ASN
# * 533 - GeoLite Legacy City
ProductIds 506 GeoLite-Legacy-IPv6-Country
DatabaseDirectory /usr/share/GeoIP
```

然后安装 geoipupdate，执行 `sudo apt install geoipupdate && mkdir -p /usr/share/GeoIP && geoipupdate -v` ，你的数据库就已经下载完毕了。

### 配置 PowerDNS

创建文件 `/etc/powerdns/pdns.d/geoip.conf` 内容是：

```
launch=geoip
geoip-database-files=/usr/share/GeoIP/GeoLiteCountry.dat /usr/share/GeoIP/GeoIPv6.dat # 选择 IPv4 和 IPv6 国家模块
geoip-database-cache=memory
geoip-zones-file=/share/zone.yaml # 你的 YAML 配置文件的位置，随便哪个地方都行
geoip-dnssec-keydir=/etc/powerdns/key
```

创建那个 YAML 文件，然后开始写 Zone，这是一个例子（IPv6 不是必须的，所有 IP 应该都填写外部 IP，本文以精确到国家举例，并列内容的顺序无所谓）：

```
# @see: https://doc.powerdns.com/md/authoritative/backend-geoip/
domains:
- domain: example.com
  ttl: 300 # 默认 TTL 时长
  records:
##### Default NS
    ns1.example.com:
      - a: # 你的服务器的第一个 IPv4 地址
          content: 10.0.0.1
          ttl: 86400
      - aaaa: # 你的服务器的第一个 IPv6 地址
          content: ::1
          ttl: 86400
    ns2.example.com: # 你的服务器的第二个 IPv4 地址（如果没有就和上面一样）
      - a:
          content: 10.0.0.2
          ttl: 86400
      - aaaa: # 你的服务器的第二个 IPv6 地址（如果没有就和上面一样）
          content: ::2
          ttl: 86400
##### Root domain
    example.com: # 根域名下的记录
      - soa:
          content: ns1.example.com. admin.example.com. 1 86400 3600 604800 10800
          ttl: 7200
      - ns:
          content: ns1.example.com.
          ttl: 86400
      - ns:
          content: ns2.example.com.
          ttl: 86400
      - mx:
          content: 100 mx1.example.com. # 权重 [空格] 主机名
          ttl: 7200
      - mx:
          content: 100 mx2.example.com.
          ttl: 7200
      - mx:
          content: 100 mx3.example.com.
          ttl: 7200
      - a: 103.41.133.70 # 如果想使用默认 TTL，那就不用区分 content 和 ttl 字段
      - aaaa: 2001:470:fa6b::1
##### Servers list 你的服务器列表
    beijing-server.example.com: &beijing
      - a: 10.0.1.1
      - aaaa: ::1:1
    newyork-server.example.com: &newyork
      - a: 10.0.2.1
      - aaaa: ::2:1
    japan-server.example.com: &japan
      - a: 10.0.3.1
      - aaaa: ::3:1
    london-server.example.com: &uk
      - a: 10.0.4.1
      - aaaa: ::4:1
    france-server.example.com: &france
      - a: 10.0.5.1
      - aaaa: ::5:1
##### GEODNS 分区解析
    # @see: https://php.net/manual/en/function.geoip-continent-code-by-name.php
    # @see: https://en.wikipedia.org/wiki/ISO\_3166-1\_alpha-3
    # unknown also is default
    # %co.%cn.geo.example.com
    # 默认
    unknown.unknown.geo.example.com: *newyork # 默认解析到美国
    # 洲
    unknown.as.geo.example.com: *japan # 亚洲解析到日本
    unknown.oc.geo.example.com: *japan # 大洋洲解析到日本
    unknown.eu.geo.example.com: *france # 欧洲解析到法国
    unknown.af.geo.example.com: *france # 非洲解析到法国
    # 国家
    chn.as.geo.example.com: *beijing # 中国解析北京
    gbr.eu.geo.example.com: *uk # 英国解析到英国
  services:
    # GEODNS
    www.example.com: [ '%co.%cn.geo.example.com', 'unknown.%cn.geo.example.com', 'unknown.unknown.geo.example.com']
```

这个配置，就相当于把 www.example.com 给分区解析，由于目前这个解析存在一些问题，导致不能同时在根域名和子域名下设置 GEODNS，这个 Bug 我[已经提交反馈](https://github.com/PowerDNS/pdns/issues/4276)了。 如果你想只把解析精度设在洲级别，那么就直接 %cn.geo.example.com 这样少写一级就行了。如果你需要精确到城市，那么多写一级就行，但是需要在配置文件中添加 GeoIP 城市的数据库。然而免费的城市数据库的城市版本并不精准，你还需要去购买商业数据库，这又是一个额外开销。

### 配置域名

前往你的域名注册商，进入后台修改设置，给域名添加上子域名服务器记录，如图：

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/34ffd569-9fc0-4ae1-29f3-bfd3e2ed4001/extra" alt="添加子域名服务器记录" width="1406" height="892"/>

由于要设置的 NS 是在自己服务器下的，所以务必要在域名注册商上向上级域名（如 .com）注册你的 NS 服务器 IP 地址，这样上级域名就能解析道 NS 的 IP，自建 DNS 才能使用，比如 icann.org 下就有一个属于自己的 NS：

```
$ dig icann.org ns +short
a.iana-servers.net.
b.iana-servers.net.
c.iana-servers.net.
ns.icann.org.
```

然后再看它的上级域名 org：

```
$ dig org ns +short
a2.org.afilias-nst.info.
b0.org.afilias-nst.org.
d0.org.afilias-nst.org.
c0.org.afilias-nst.info.
a0.org.afilias-nst.info.
b2.org.afilias-nst.org.
```

随便找一个服务器，查询权威记录（我就不用 +short 了）：

```
$ dig @a0.org.afilias-nst.info icann.org ns
;; QUESTION SECTION:
;icann.org.INNS
;; AUTHORITY SECTION:
icann.org.86400INNSc.iana-servers.net.
icann.org.86400INNSa.iana-servers.net.
icann.org.86400INNSns.icann.org.
icann.org.86400INNSb.iana-servers.net.
;; ADDITIONAL SECTION:
ns.icann.org.86400INA199.4.138.53
ns.icann.org.86400INAAAA2001:500:89::53
```

可以看到，在这个 org 的 NS 服务器就已经把 ns.icann.org. 的记录返回来了，这也就是你需要在域名注册商填写 IP 地址的原因。然而你最好在你域名下的 DNS 服务器上也返回相同的 NS 和相同的 IP。 最后，不要忘了改域名的 NS 记录。

### YAML 的一些高级写法

我刚才的 YAML 中其实就已经用到了 YAML 的高级写法，就是 `&variable` 设置变量，`*variable` 使用变量，这很像 CloudXNS 下的 LINK 记录，比如在 CloudXNS 下你可以这么写：

```
www.example.com    600   IN    A       10.0.0.1
www.example.com    600   IN    A       10.0.0.2
www.example.com    600   IN    AAAA    ::1
www.example.com    600   IN    AAAA    ::2
sub.example.com    600   IN    LINK    www.example.com
```

然后在你的 YAML 记录里就可以这么写：

```
www.example.com: &www
  - a: 10.0.0.1
  - a: 10.0.0.2
  - aaaa: ::1
  - aaaa: ::2
sub.example.com: *www
```

这就是 YAML 的一种高级写法，不需要其他额外支持。

### 添加 DNSSEC 支持

详情可以[参考文档](https://doc.powerdns.com/md/authoritative/dnssec/)，运行以下指令：

```
$ mkdir /etc/powerdns/key
$ pdnsutil secure-zone example.com
$ pdnsutil show-zone example.com
```

最后一个指令所返回的结果就是你需要在域名注册商设置的记录，不推荐都设置，只设置 ECDSAP256SHA256 - SHA256 digest 就行了。 最后在线检查设置即可 [测试地址1](http://dnssec-debugger.verisignlabs.com/) [测试地址2](http://dnsviz.net)，可能有几天缓存时间。 我的检查[结果](http://dnsviz.net/d/guozeyu.com/dnssec/)

### 其他一些有趣的东西

你可以在 YAML 里写上这个，为了方便你调试：

```
"*.ip.example.com":
  - txt:
      content: "IP%af: %ip, Continent: %cn, Country: %co, ASn: %as, Region: %re, Organisation: %na, City: %ci"
      ttl: 0
```

这些变量都能作为你 GEODNS 的标准，也可以检查你的 GEOIP 数据库情况。 然后，正确检查的姿势：

```
$ random=`head -200 /dev/urandom  md5` ; dig ${random}.ip.example txt +short
"IPv4: 42.83.200.23, Continent: as, Country: chn, ASn: unknown, Region: unknown, Organisation: unknown, City: unknown"
```

IP 地址就是 DNS 缓存服务器地址（如果你开启了 EDNS Client Subnet，且缓存服务器支持，那么就是自己的 IP，但是如果使用 8.8.8.8，那么会看到自己的 IP 最后一位是 0），如果你在本地指定了从你自己的服务器查，那就直接返回你自己的 IP 地址。由于我只安装了国家数据库，所以除了洲和国家之外其余都是 Unknown。

## 进阶使用

### 建立分布式 DNS

一般情况下，是一个 Master 和一个  Slave 的 DNS 解析服务器，但是这样的话对 DNSSEC 可能有问题，于是我就建立了两个 Master 服务器，自动同步记录，并设置了**相同的 DNSSEC Private Key**，好像并没有什么错误发生（毕竟包括 SOA 在内的所有记录也都是完全一样的），我的服务器目前的配置

```
$ dig @a.gtld-servers.net guozeyu.com
;; QUESTION SECTION:
;guozeyu.com.INA
;; AUTHORITY SECTION:
guozeyu.com.172800INNSa.geo.ns.tloxygen.net.
guozeyu.com.172800INNSc.geo.ns.tloxygen.net.
;; ADDITIONAL SECTION:
a.geo.ns.tloxygen.net.172800INA198.251.90.65
a.geo.ns.tloxygen.net.172800INAAAA2605:6400:10:6a9::2
c.geo.ns.tloxygen.net.172800INA104.196.241.116
c.geo.ns.tloxygen.net.172800INAAAA2605:6400:20:b5e::2
```

其中是两个 IPv4 两个 IPv6，其中 a.geo.ns.tloxygen.net. 是使用了 Anycast 技术的 IP 地址，其背后由三台服务器提供。c.geo.ns.tloxygen.net. 属于另一家服务商的主机，这样一个挂了之后还有备份，更加稳定。

#### Anycast or Unicast?

像我这种分布式的 DNS，其实是 Unicast 和 Anycast 的组合，这样存在的一个问题就是在一个地方连接其中一个会比较快，但是另一个会比较慢。只有在用支持异步查询，或者是带 GeoIP 的 DNS 缓存服务器，才有可能连接到最快的 DNS 权威服务器，其他情况下则是随机连接，而且如果一个服务器挂掉了，那么服务器对应的 IP 就废了。 Anycast 是一个 IP 对应多个主机，然而我却没有条件用，这个对于个人来说也许成本会比较高，要么你自己有 AS 号然后让主机商给你接入，要么你的主机商提供跨区域的 Load Balancing IP。我的 VPS 在两个不同的主机商，也没有 AS，就不能用 Anycast 了。我觉得 DNS 服务如果可能还是要用 Anycast，因为 DNS 服务器对应的 IP 不能 GEODNS（因为这是根域名给你解析的），使用 Anycast 后就基本能保障最快的连接速率，并且一个服务器挂了 IP 还能用。此外，DNS 必须要同时转发 TCP 和 UDP 的 53 端口。

### 宕机自动切换

如何实现宕机自动切换？实现这个的流程是： 监控服务发现宕机 -> 向服务器发送已经宕机的请求 -> 服务器对宕机处理，解析到备用 IP/暂停解析 监控服务发现服务正常 -> 向服务器发送服务正常的请求 -> 服务器对服务正常处理，恢复解析 可以建立两个 YAML file，一个是默认使用，一个是服务器宕机时使用的，当监控服务发现服务器宕机后，重新加载另一个 YAML file，然后这就是宕机模式了。
