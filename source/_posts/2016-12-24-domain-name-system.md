---
title: DNS 域名解析系统详解——基础篇
tags:
  - DNS
  - 网络
id: '2313'
categories:
  - - 开发
date: 2016-12-24 20:18:24
languages:
  en-US: https://ze3kr.com/2016/12/domain-name-system/
---

DNS（域名解析系统）的工作使命，就是服务于与域名相关的内容的底层。是域名（如：`example.com`）的核心组成部分。绝大多数与域名相关的东西，都离不开它。比如：

*   **访问一个网站**，通常是输入一个域名（如 `https://www.example.com`）
*   **发送邮件**，`@` 后面是主机名，而主机名通常是个域名（如 `webmaster@example.com`）

整个 DNS 具有复杂的层次，这对刚开始购买域名的人有很大的疑惑。本文将详尽的介绍 DNS 的工作原理，有助于更深刻的理解。本文将介绍：

1.  在客户端上是如何解析一个域名的
2.  在 DNS 缓存服务器上是如何逐级解析一个域名的

同时还包含：

1.  域名的分类
2.  什么是 Glue 记录
3.  为什么 CNAME 不能设置在主域名上
<!-- more -->

先从本地的 DNS 开始讲起。

## 本地 DNS

本地的 DNS 相对于全球 DNS 要简单的多。所以先从本地 DNS 开始讲起。 `127.0.0.1` 常被用作环回 IP，也就可以作为本机用来访问自身的 IP 地址。通常，这个 IP 地址都对应着一个域名，叫 `localhost`。这是一个一级域名（也是顶级域名），所以和 `com` 同级。系统是如何实现将 `localhost` 对应到 `127.0.0.1` 的呢？其实就是通过 DNS。在操作系统中，通常存在一个 `hosts` 文件，这个文件定义了一组域名到 IP 的映射。常见的 hosts 文件内容如下：

```
127.0.0.1       localhost
::1             localhost
```


它就定义了 `localhost` 域名对应 `127.0.0.1` 这个 IP（第二行是 IPv6 地址）。这样，当你从浏览器里访问这个域名，或者是在终端中执行 Ping 的时候，会自动的查询这个 `hosts` 文件，就从文件中得到了这个 IP 地址。此外，`hosts` 文件还可以控制其他域名所对应的 IP 地址，并可以 override 其在全球 DNS 或本地网络 DNS 中的值。但是，`hosts` 文件只能控制本地的域名解析。`hosts` 文件出现的时候，还没有 DNS，但它可以说是 DNS 的前身。 如果需要在一个网络中，公用同一个 DNS，那么就需要使用 IP 数据包向某个服务器去获取 DNS 记录。 在一个网络里（此处主要指本地的网络，比如家庭里一个路由器下连接的所有设备和这个路由器组成的网络），会有很多主机。在与这些主机通信的时候，使用域名会更加的方便。通常，连接到同一个路由器的设备会被设置到路由器自己的一个 DNS 服务器上。这样，解析域名就不仅可以从 hosts 去获取，还可以从这个服务器上去获取。从另一个 IP 上去获取 DNS 记录通过 DNS 查询，DNS 查询通常基于 UDP 或者 TCP 这种 IP 数据包，来实现远程的查询。我的个人电脑的网络配置如下，这是在我的电脑连接了路由器之后自动就设置上的：

![网络配置截图](/cdn-cgi/imagedelivery/6T-behmofKYLsxlrK0l_MQ/228ba536-02e2-40af-c0c4-760d74c0ce00/large)

重点是在路由器和搜索域上。 我的电脑的主机名（也是电脑名）设置的是 `ze3kr`，这个内容在连接路由器时也被路由器知道了，于是路由器就给我的主机分配了一个域名 `ze3kr.local`，`local` 这个一级域名专门供本地使用。在这个网络内所有主机通过访问 `ze3kr.local` 这个域名时，路由器（`10.0.1.1`）就会告知这个域名对应的 IP 地址，于是这些主机够获得到我的电脑的 IP 地址。至于搜索域的作用，其实是可以省去输入完整的域名，比如：

```
$ ping ze3kr
PING ze3kr.local (10.0.1.201): 56 data bytes
64 bytes from 10.0.1.201: icmp_seq=0 ttl=64 time=0.053 ms
--- ze3kr.local ping statistics ---
1 packets transmitted, 1 packets received, 0.0% packet loss
round-trip min/avg/max/stddev = 0.053/0.053/0.053/0.000 ms
```


当设置了搜索域时，当你去解析 `ze3kr` 这个一级域名时，便会先尝试去解析 `ze3kr.local`，当发现 `ze3kr.local` 存在对应的解析时，便停止进一步解析，直接使用 `ze3kr.local` 的地址。 现在你已经了解了本地 DNS 的工作方式。DNS 的基本工作方式就是：获取域名对应 IP，然后与这个 IP 进行通信。 当在本地去获取一个完整域名（FQDN）时（执行 `getaddrbyhost`），通常也是通过路由器自己提供的 DNS 进行解析的。当路由器收到一个完整域名请求且没有缓存时，会继续向下一级缓存 DNS 服务器（例如运营商提供的，或者是组织提供的，如 8.8.8.8）查询，下一级缓存 DNS 服务器也没有缓存时，就会通过全球 DNS 进行查询。具体的查询方式，在 “全球 DNS” 中有所介绍。

### 总结

在本地，先读取本地缓存查找记录，再读取 Hosts 文件，然后在搜索域中查找域名，最后再向路由器请求 DNS 记录。

## 全球 DNS

在全球 DNS 中，一个完整域名通常包含多级，比如 `example.com.` 就是个二级域名， `www.example.com.` 就是个三级域名。通常我们常见到的域名都是完整的域名。

![全球 DNS 拓扑图](/cdn-cgi/imagedelivery/6T-behmofKYLsxlrK0l_MQ/0068a9ee-7456-4512-25fe-e16929f99700/large)
 
 一级域名被分为以下三个部分：

1.  **普通域（gTLD）**：通常是三个字节或三个字节以上的域名后缀，或者是 Unicode 字符的后缀。这些域名分配给机构管理。
2.  **国家域（ccTLD）**：所有两个字节的域名都是国家代码，这些域名分配给国家管理。不少国家域都开放了注册，不过有的国家域仅允许当前国家的人去注册。
3.  **`arpa` 域**：用于将 IP 地址转换为对应的域名的根域。

**我们通常所见到的域名都是普通域和国家域**，而 `arpa` 域用作 IP 到域名的反向解析。 在本地 DNS 中，只存在域名对应 IP 这种映射关系。然而，在全球 DNS 中，有着更多的资源记录种类（RR），不只是域名对应 IP 的关系，下面将分别介绍一些最基本的资源记录种类：

*   **A 记录**：定义了一个 IP 地址。（AAAA 记录则是定义一个 IPv6 地址）（[RFC 1035](https://tools.ietf.org/html/rfc1035)）
*   **NS 记录**：域名服务器记录，说明一个域名下的的授权域名服务器记录。**内容必须是一个域名**。（[RFC 1035](https://tools.ietf.org/html/rfc1035)）

### 根域名

先从根域名开始，未命名根也可以作为 `.` 。你在接下来的部分所看到的很多域名都以 `.`结尾，以 `.`结尾的域名是特指的是根域名下的完整域名，然而不以 `.` 结尾的域名大都也是完整域名，实际使用时域名末尾的 `.`也常常省略。在本文中，我使用 dig 这一个常用的 DNS 软件来进行查询，我的电脑也已经连接到了互联网。 假设目前这个计算机能与互联网上的 IP 通信，但是完全没有 DNS 服务器。此时你需要知道根 DNS 服务器，以便自己获取某个域名的 IP 地址。根 DNS 服务器列表可以在[这里](https://www.internic.net/domain/named.root)下载到。文件内容如下：

```
;       This file holds the information on root name servers needed to
;       initialize cache of Internet domain name servers
;       (e.g. reference this file in the "cache  .  <file>"
;       configuration file of BIND domain name servers).
;
;       This file is made available by InterNIC
;       under anonymous FTP as
;           file                /domain/named.cache
;           on server           FTP.INTERNIC.NET
;       -OR-                    RS.INTERNIC.NET
;
;       last update:    October 20, 2016
;       related version of root zone:   2016102001
;
; formerly NS.INTERNIC.NET
;
.                        3600000      NS    A.ROOT-SERVERS.NET.
A.ROOT-SERVERS.NET.      3600000      A     198.41.0.4
A.ROOT-SERVERS.NET.      3600000      AAAA  2001:503:ba3e::2:30
;
; FORMERLY NS1.ISI.EDU
;
.                        3600000      NS    B.ROOT-SERVERS.NET.
B.ROOT-SERVERS.NET.      3600000      A     192.228.79.201
B.ROOT-SERVERS.NET.      3600000      AAAA  2001:500:84::b
;
; FORMERLY C.PSI.NET
;
.                        3600000      NS    C.ROOT-SERVERS.NET.
C.ROOT-SERVERS.NET.      3600000      A     192.33.4.12
C.ROOT-SERVERS.NET.      3600000      AAAA  2001:500:2::c
;
; FORMERLY TERP.UMD.EDU
;
.                        3600000      NS    D.ROOT-SERVERS.NET.
D.ROOT-SERVERS.NET.      3600000      A     199.7.91.13
D.ROOT-SERVERS.NET.      3600000      AAAA  2001:500:2d::d
;
; FORMERLY NS.NASA.GOV
;
.                        3600000      NS    E.ROOT-SERVERS.NET.
E.ROOT-SERVERS.NET.      3600000      A     192.203.230.10
E.ROOT-SERVERS.NET.      3600000      AAAA  2001:500:a8::e
;
; FORMERLY NS.ISC.ORG
;
.                        3600000      NS    F.ROOT-SERVERS.NET.
F.ROOT-SERVERS.NET.      3600000      A     192.5.5.241
F.ROOT-SERVERS.NET.      3600000      AAAA  2001:500:2f::f
;
; FORMERLY NS.NIC.DDN.MIL
;
.                        3600000      NS    G.ROOT-SERVERS.NET.
G.ROOT-SERVERS.NET.      3600000      A     192.112.36.4
G.ROOT-SERVERS.NET.      3600000      AAAA  2001:500:12::d0d
;
; FORMERLY AOS.ARL.ARMY.MIL
;
.                        3600000      NS    H.ROOT-SERVERS.NET.
H.ROOT-SERVERS.NET.      3600000      A     198.97.190.53
H.ROOT-SERVERS.NET.      3600000      AAAA  2001:500:1::53
;
; FORMERLY NIC.NORDU.NET
;
.                        3600000      NS    I.ROOT-SERVERS.NET.
I.ROOT-SERVERS.NET.      3600000      A     192.36.148.17
I.ROOT-SERVERS.NET.      3600000      AAAA  2001:7fe::53
;
; OPERATED BY VERISIGN, INC.
;
.                        3600000      NS    J.ROOT-SERVERS.NET.
J.ROOT-SERVERS.NET.      3600000      A     192.58.128.30
J.ROOT-SERVERS.NET.      3600000      AAAA  2001:503:c27::2:30
;
; OPERATED BY RIPE NCC
;
.                        3600000      NS    K.ROOT-SERVERS.NET.
K.ROOT-SERVERS.NET.      3600000      A     193.0.14.129
K.ROOT-SERVERS.NET.      3600000      AAAA  2001:7fd::1
;
; OPERATED BY ICANN
;
.                        3600000      NS    L.ROOT-SERVERS.NET.
L.ROOT-SERVERS.NET.      3600000      A     199.7.83.42
L.ROOT-SERVERS.NET.      3600000      AAAA  2001:500:9f::42
;
; OPERATED BY WIDE
;
.                        3600000      NS    M.ROOT-SERVERS.NET.
M.ROOT-SERVERS.NET.      3600000      A     202.12.27.33
M.ROOT-SERVERS.NET.      3600000      AAAA  2001:dc3::35
; End of file
```

这个文件中每一行分为 4 列，分别是完整域名、资源类型、生存时间（TTL，也就是可以缓存的时间）以及资源数据。通过这个文件就可以知道根域名所对应的 13 个根域名服务器的完整域名，还能知道这 13 个完整域名所对应的 IP 地址。这是因为 NS 记录只能设置为完整域名，所以为了告知 NS 所对应的 IP，还需要额外的数据去说明这 13 个完整域名对应的 IP（这些额外的数据叫做 **Glue 记录**）。这 13 个根域名服务器都是独立的且冗余的（但是所返回的内容应该是相同的），这样当其中的某个或某些服务器发生故障时，不会影响到解析。一旦无法解析根域名，那么所有的域名都将无法访问。

**Glue 记录**：如果 NS 记录对应的某个完整域名包含在那个域名之中，那么就需要添加一个 Glue 记录，来指定那个完整域名所对应的 IP。实际上任何完整域名都属于根域名之中，所以根域名就必须对这些 NS 记录设置对应的 IP 地址。Glue 记录实质上就是 A（或 AAAA）记录。（[RFC 1033](https://tools.ietf.org/html/rfc1033)）

我们假设你已经通过某种方法成功获取到了这个文件，那么下一步，就是使用这里的服务器对根域名以及一级域名进行解析。对根域名的解析实际上是不必要的，但是我们还是对其进行解析以便进一步分析，获得在互联网上最新、最全的数据。 **在根域名上的记录，以从根域名服务器中所解析其根域名的数据为准**，而不是刚才的那个文件中的内容。刚才的文件内容只是告知根域名服务器的列表，也就是只有 NS 记录和 NS 记录对应的完整域名的 IP 记录，而不是根域名下的所有记录。 我们先向 `198.41.0.4` 这个 IP 发送查询根域名的所有记录，any 代表显示任何类型的记录，为了看起来方便，一些无关的响应已经删除（如果只是解析根域名，这一步不能跳过。如果需要直接解析一级域名，那么这一步就可以跳过）。

```
$ dig @198.41.0.4 . any
;; QUESTION SECTION:
;.				IN	ANY
;; ANSWER SECTION:
.			518400	IN	NS	a.root-servers.net.
.			518400	IN	NS	b.root-servers.net.
.			518400	IN	NS	c.root-servers.net.
.			518400	IN	NS	d.root-servers.net.
.			518400	IN	NS	e.root-servers.net.
.			518400	IN	NS	f.root-servers.net.
.			518400	IN	NS	g.root-servers.net.
.			518400	IN	NS	h.root-servers.net.
.			518400	IN	NS	i.root-servers.net.
.			518400	IN	NS	j.root-servers.net.
.			518400	IN	NS	k.root-servers.net.
.			518400	IN	NS	l.root-servers.net.
.			518400	IN	NS	m.root-servers.net.
.			86400	IN	SOA	a.root-servers.net. nstld.verisign-grs.com. 2016122400 1800 900 604800 86400
;; ADDITIONAL SECTION:
a.root-servers.net.	518400	IN	A	198.41.0.4
b.root-servers.net.	518400	IN	A	192.228.79.201
c.root-servers.net.	518400	IN	A	192.33.4.12
d.root-servers.net.	518400	IN	A	199.7.91.13
e.root-servers.net.	518400	IN	A	192.203.230.10
f.root-servers.net.	518400	IN	A	192.5.5.241
g.root-servers.net.	518400	IN	A	192.112.36.4
h.root-servers.net.	518400	IN	A	198.97.190.53
i.root-servers.net.	518400	IN	A	192.36.148.17
j.root-servers.net.	518400	IN	A	192.58.128.30
k.root-servers.net.	518400	IN	A	193.0.14.129
l.root-servers.net.	518400	IN	A	199.7.83.42
m.root-servers.net.	518400	IN	A	202.12.27.33
a.root-servers.net.	518400	IN	AAAA	2001:503:ba3e::2:30
b.root-servers.net.	518400	IN	AAAA	2001:500:84::b
c.root-servers.net.	518400	IN	AAAA	2001:500:2::c
d.root-servers.net.	518400	IN	AAAA	2001:500:2d::d
e.root-servers.net.	518400	IN	AAAA	2001:500:a8::e
f.root-servers.net.	518400	IN	AAAA	2001:500:2f::f
g.root-servers.net.	518400	IN	AAAA	2001:500:12::d0d
h.root-servers.net.	518400	IN	AAAA	2001:500:1::53
i.root-servers.net.	518400	IN	AAAA	2001:7fe::53
j.root-servers.net.	518400	IN	AAAA	2001:503:c27::2:30
k.root-servers.net.	518400	IN	AAAA	2001:7fd::1
l.root-servers.net.	518400	IN	AAAA	2001:500:9f::42
m.root-servers.net.	518400	IN	AAAA	2001:dc3::35
```

其中，可以看到 TTL 与刚才文件中的内容不一样，此外还多了一个 SOA 记录，**所以实际上是以这里的结果为准**。这里还有一个 SOA 记录，SOA 记录是普遍存在的，具体请参考文档，在这里不做过多说明。

**SOA 记录**：指定有关 _**DNS 区域**_的权威性信息，包含主要名称服务器、域名管理员的电邮地址、域名的流水式编号、和几个有关刷新区域的定时器。（[RFC 1035](https://tools.ietf.org/html/rfc1035)）

_**DNS 区域**_：对于根域名来说，DNS 区域就是空的，也就是说它负责这互联网下所有的域名。而对于我的网站，DNS 区域就是 `guozeyu.com.`，管理着 `guozeyu.com.` 本身及其子域名的记录。

此处的 ADDITIONAL SECTION 其实就包含了 Glue 记录。

### 一级域名

根域名自身的 DNS 服务器服务器除了被用于解析根自身之外，还用于解析所有在互联网上的**一级域名**。**你会发现，几乎所有的 DNS 服务器，无论是否是根 DNS 服务器，都会解析其自身**以及其下级域名。 从之前的解析结果中可以看出，**根域名没有指定到任何 IP 地址**，但是却给出了 NS 记录，于是我们就需要用这些 NS 记录来解析其下级的一级域名。下面，用所得到的根 NS 记录中的服务器其中之一来解析一个一级域名 `com.`。

```
$ dig @198.41.0.4 com any
;; QUESTION SECTION:
;com.				IN	ANY
;; AUTHORITY SECTION:
com.			172800	IN	NS	a.gtld-servers.net.
com.			172800	IN	NS	b.gtld-servers.net.
com.			172800	IN	NS	c.gtld-servers.net.
com.			172800	IN	NS	d.gtld-servers.net.
com.			172800	IN	NS	e.gtld-servers.net.
com.			172800	IN	NS	f.gtld-servers.net.
com.			172800	IN	NS	g.gtld-servers.net.
com.			172800	IN	NS	h.gtld-servers.net.
com.			172800	IN	NS	i.gtld-servers.net.
com.			172800	IN	NS	j.gtld-servers.net.
com.			172800	IN	NS	k.gtld-servers.net.
com.			172800	IN	NS	l.gtld-servers.net.
com.			172800	IN	NS	m.gtld-servers.net.
;; ADDITIONAL SECTION:
a.gtld-servers.net.	172800	IN	A	192.5.6.30
b.gtld-servers.net.	172800	IN	A	192.33.14.30
c.gtld-servers.net.	172800	IN	A	192.26.92.30
d.gtld-servers.net.	172800	IN	A	192.31.80.30
e.gtld-servers.net.	172800	IN	A	192.12.94.30
f.gtld-servers.net.	172800	IN	A	192.35.51.30
g.gtld-servers.net.	172800	IN	A	192.42.93.30
h.gtld-servers.net.	172800	IN	A	192.54.112.30
i.gtld-servers.net.	172800	IN	A	192.43.172.30
j.gtld-servers.net.	172800	IN	A	192.48.79.30
k.gtld-servers.net.	172800	IN	A	192.52.178.30
l.gtld-servers.net.	172800	IN	A	192.41.162.30
m.gtld-servers.net.	172800	IN	A	192.55.83.30
a.gtld-servers.net.	172800	IN	AAAA	2001:503:a83e::2:30
b.gtld-servers.net.	172800	IN	AAAA	2001:503:231d::2:30
```

可以看到解析的结果和解析根域名的类似，`com.` 下也设置了 13 个域名服务器，但是这 13 个域名服务器与根域服务器**完全不同**。

此处也存在 ADDITIONAL SECTION 包含的 Glue 记录，然而 `gtld-servers.net.` 却并不包含在 `com.` 下。然而实际上，`com.` 和 `net.` 域名都是属于同一个所有者（Verisign），所以这样设置是可以的。

和根域名类似，此时解析到的内容只是 `com.` 的域名服务器，而并不是 `com.` 本身的记录，**在 `com.` 上的记录，以从 `com.` 的域名服务器中所解析其 `com.` 域名的数据为准。** 所以，下面再使用 `com.` 的域名服务器来解析 `com.` 自身，看情况如何（如果只是解析一级域名，这一步不能跳过。如果需要直接解析二级域名，那么这一步就可以跳过）：

```
$ dig @192.5.6.30 com any
;; QUESTION SECTION:
;com.				IN	ANY
;; ANSWER SECTION:
com.			900	IN	SOA	a.gtld-servers.net. nstld.verisign-grs.com. 1482571852 1800 900 604800 86400
com.			172800	IN	NS	e.gtld-servers.net.
com.			172800	IN	NS	m.gtld-servers.net.
com.			172800	IN	NS	i.gtld-servers.net.
com.			172800	IN	NS	k.gtld-servers.net.
com.			172800	IN	NS	b.gtld-servers.net.
com.			172800	IN	NS	j.gtld-servers.net.
com.			172800	IN	NS	a.gtld-servers.net.
com.			172800	IN	NS	d.gtld-servers.net.
com.			172800	IN	NS	g.gtld-servers.net.
com.			172800	IN	NS	c.gtld-servers.net.
com.			172800	IN	NS	h.gtld-servers.net.
com.			172800	IN	NS	f.gtld-servers.net.
com.			172800	IN	NS	l.gtld-servers.net.
;; ADDITIONAL SECTION:
e.gtld-servers.net.	172800	IN	A	192.12.94.30
m.gtld-servers.net.	172800	IN	A	192.55.83.30
i.gtld-servers.net.	172800	IN	A	192.43.172.30
k.gtld-servers.net.	172800	IN	A	192.52.178.30
b.gtld-servers.net.	172800	IN	A	192.33.14.30
b.gtld-servers.net.	172800	IN	AAAA	2001:503:231d::2:30
j.gtld-servers.net.	172800	IN	A	192.48.79.30
a.gtld-servers.net.	172800	IN	A	192.5.6.30
a.gtld-servers.net.	172800	IN	AAAA	2001:503:a83e::2:30
d.gtld-servers.net.	172800	IN	A	192.31.80.30
g.gtld-servers.net.	172800	IN	A	192.42.93.30
c.gtld-servers.net.	172800	IN	A	192.26.92.30
h.gtld-servers.net.	172800	IN	A	192.54.112.30
f.gtld-servers.net.	172800	IN	A	192.35.51.30
l.gtld-servers.net.	172800	IN	A	192.41.162.30
```

也和根域名解析的情况类似，此时多了一个 SOA 类型的记录。

### 二级域名

和解析一级域名 `com.` 时类似，继续使用 `com.` 的域名服务器解析 `guozeyu.com.`。

```
$ dig @192.5.6.30 guozeyu.com any
;; QUESTION SECTION:
;guozeyu.com.			IN	ANY
;; AUTHORITY SECTION:
guozeyu.com.		172800	IN	NS	a.ns.guozeyu.com.
guozeyu.com.		172800	IN	NS	b.ns.guozeyu.com.
guozeyu.com.		172800	IN	NS	c.ns.guozeyu.com.
;; ADDITIONAL SECTION:
a.ns.guozeyu.com.		172800	IN	AAAA	2001:4860:4802:38::6c
a.ns.guozeyu.com.		172800	IN	A	216.239.38.108
b.ns.guozeyu.com.		172800	IN	AAAA	2001:4860:4802:36::6c
b.ns.guozeyu.com.		172800	IN	A	216.239.36.108
c.ns.guozeyu.com.		172800	IN	AAAA	2001:4860:4802:34::6c
c.ns.guozeyu.com.		172800	IN	A	216.239.34.108
```

此处也存在 ADDITIONAL SECTION 包含的 Glue 记录，是因为 `ns.guozeyu.com.` 在 `guozeyu.com.` 之下。 同样的，**在 `guozeyu.com.` 上的记录，以从 `guozeyu.com.` 的域名服务器中所解析其 `guozeyu.com.` 域名的数据为准**。此时这种解析就尤为必要了，因为 `guozeyu.com.` 上不只有 SOA 记录，同时也有 A 记录和其他重要的记录。 现在使用 `guozeyu.com.` 的域名服务器来解析 `guozeyu.com.`（如果只是解析二级域名，这一步不能跳过。如果需要解析三级域名，那么这一步可以跳过）：

```
$ dig @216.239.38.108 guozeyu.com any
;; QUESTION SECTION:
;guozeyu.com.			IN	ANY
;; ANSWER SECTION:
guozeyu.com.		21600	IN	A	104.199.138.99
guozeyu.com.		172800	IN	NS	a.ns.guozeyu.com.
guozeyu.com.		172800	IN	NS	b.ns.guozeyu.com.
guozeyu.com.		172800	IN	NS	c.ns.guozeyu.com.
guozeyu.com.		21600	IN	SOA	a.ns.guozeyu.com. support.tlo.xyz. 1 21600 3600 259200 300
guozeyu.com.		172800	IN	MX	100 us2.mx1.mailhostbox.com.
guozeyu.com.		172800	IN	MX	100 us2.mx2.mailhostbox.com.
guozeyu.com.		172800	IN	MX	100 us2.mx3.mailhostbox.com.
;; ADDITIONAL SECTION:
a.ns.guozeyu.com.		604800	IN	A	216.239.38.108
a.ns.guozeyu.com.		604800	IN	AAAA	2001:4860:4802:38::6c
b.ns.guozeyu.com.		604800	IN	A	216.239.36.108
b.ns.guozeyu.com.		604800	IN	AAAA	2001:4860:4802:36::6c
c.ns.guozeyu.com.		604800	IN	A	216.239.34.108
c.ns.guozeyu.com.		604800	IN	AAAA	2001:4860:4802:34::6c
```

可以发现增加了 A、SOA 和 MX 记录。

*   **MX 记录**：邮件交换记录，让发送到一个域名的邮件由其他的主机去接受，用于与 A 记录共存。（[RFC 1035](https://tools.ietf.org/html/rfc1035)）

正是因为 MX 记录的存在，所以发往 `username@guozeyu.com` 的邮件不是指向 `guozeyu.com.` 对应的 IP 地址，而是使用 `mailhostbox.com.` 下的服务器。 当然，由于我是 `guozeyu.com.` 的所有者，所以我也可以控制 `guozeyu.com.` 下的三级或更高级的域名。比如 `www.guozeyu.com.`：

```
$ dig @216.239.38.108 www.guozeyu.com any
;; QUESTION SECTION:
;guozeyu.com.			IN	ANY
;; ANSWER SECTION:
www.guozeyu.com.		172800	IN	CNAME	guozeyu.com.
guozeyu.com.		21600	IN	A	104.199.138.99
guozeyu.com.		172800	IN	NS	a.ns.guozeyu.com.
guozeyu.com.		172800	IN	NS	b.ns.guozeyu.com.
guozeyu.com.		172800	IN	NS	c.ns.guozeyu.com.
guozeyu.com.		21600	IN	SOA	a.ns.guozeyu.com. support.tlo.xyz. 1 21600 3600 259200 300
guozeyu.com.		172800	IN	MX	100 us2.mx1.mailhostbox.com.
guozeyu.com.		172800	IN	MX	100 us2.mx2.mailhostbox.com.
guozeyu.com.		172800	IN	MX	100 us2.mx3.mailhostbox.com.
;; ADDITIONAL SECTION:
a.ns.guozeyu.com.		604800	IN	A	216.239.38.108
a.ns.guozeyu.com.		604800	IN	AAAA	2001:4860:4802:38::6c
b.ns.guozeyu.com.		604800	IN	A	216.239.36.108
b.ns.guozeyu.com.		604800	IN	AAAA	2001:4860:4802:36::6c
c.ns.guozeyu.com.		604800	IN	A	216.239.34.108
c.ns.guozeyu.com.		604800	IN	AAAA	2001:4860:4802:34::6c
```

*   **CNAME 记录**：规范名称记录，一个主机名字的别名。**内容必须是一个域名**。（[RFC 1035](https://tools.ietf.org/html/rfc1035)）

我的三级域名 `www.guozeyu.com.` 使用 CNAME 记录指向了 `guozeyu.com.`，这代表着 `guozeyu.com.` 下**所有资源类型**均与 `guozeyu.com.` 相同。这也是为什么 CNAME 不能和其他任何记录连用的原因，CNAME 的存在会取代任何其他的记录。由于主域名下常常也存在 SOA、NS 以及 MX 记录，所以**主域名下不能使用 CNAME 解析**。此外，我也可以设置在 `guozeyu.com.` 下的三级域名指向一个 NS 记录，这样我就可以把我的三级域名再给别人使用。

### 总结

1.  如果需要解析一个根域名，使用根域名服务器解析根域名即可。
2.  如果需要解析一个一级域名，需要先使用根域名服务器解析一级域名，获取到一级域名的域名服务器，然后用一级域名服务器解析一级域名自身。
3.  如果需要解析一个二级域名，需要先使用根域名服务器解析一级域名，获取到一级域名的域名服务器，然后用一级域名服务器获取二级域名的域名服务器，然后用二级域名服务器解析二级域名自身。
4.  如果需要解析一个三级域名，需要先使用根域名服务器解析一级域名，获取到一级域名的域名服务器，然后用一级域名服务器获取二级域名的域名服务器，然后用二级域名服务器解析三级域名，若三级域名下没有 NS、CNAME 记录，则解析结束，如果有 CNAME 记录则再通过正常的解析方式解析这个 CNAME 所指向的域名的记录，如果有 NS 记录，则用三级域名服务器解析三级域名自身。

### DNS 缓存

通常，凡是解析过的记录，都会被解析服务器、路由器、客户端、软件缓存。这样可以大大减少请求次数。凡是被缓存的记录，其在 TTL 规定的时间内都不会再次去解析，而是直接从高速缓存中读取。正常情况下，服务器、路由器等都不应该扩大 TTL 值。被缓存的内容，TTL 值要在每秒减 1 。

```
$ dig guozeyu.com
;; QUESTION SECTION:
;guozeyu.com.			IN	A
;; ANSWER SECTION:
guozeyu.com.		21599	IN	A	104.199.138.99
;; Query time: 514 msec
;; SERVER: 10.0.1.1#53(10.0.1.1)
;; WHEN: Sat Dec 24 20:00:29 2016
;; MSG SIZE  rcvd: 43
$ dig guozeyu.com
;; QUESTION SECTION:
;guozeyu.com.			IN	A
;; ANSWER SECTION:
guozeyu.com.		21598	IN	A	104.199.138.99
;; Query time: 45 msec
;; SERVER: 10.0.1.1#53(10.0.1.1)
;; WHEN: Sat Dec 24 20:00:30 2016
```

这是我连续两次在我的电脑上直接通过路由器解析 `guozeyu.com.` 的结果。显然第一次解析没有命中高速缓存，路由器向运营商的 DNS 服务器查询，运营商的 DNS 再从根域名逐级解析 `guozeyu.com.`，总耗时 514 毫秒。然后 `guozeyu.com.` 就会同时在运营商的服务器上和路由器的 DNS 服务器上进行缓存。在第二次请求 `guozeyu.com.` 时，就命中了在路由器上的缓存，于是由路由器直接返回解析记录，总耗时 45 毫秒。两次查询时间相隔一秒，所以 TTL 值也被减去 1 。

### `arpa` 反向解析

反向解析用于：给定一个 IP 地址，返回与该地址对应的域名。函数 `gethostbyaddr` 正是使用了这种方法获取 IP 对应的域名。 一级域名 arpa. 用于设置反向解析，对于我的网站的 IPv4 地址 `104.199.138.99`，其所对应的 `arpa` 完整域名是：`99.138.199.104.in-addr.arpa.`，通过获取这个域名的 PTR 记录，就可以得到这个域名所对应的完整域名域名。

```
$ host 104.199.138.99
99.138.199.104.in-addr.arpa domain name pointer 99.138.199.104.bc.googleusercontent.com.
$ dig 99.138.199.104.in-addr.arpa. ptr +short
99.138.199.104.bc.googleusercontent.com.
```

*   PTR 记录：指针解析记录，内容是一个完整域名，定义一个 IP 所对应的域名。

可以看到，使用 `host` 命令获得 IP 地址的域名与使用 `dig` 获取的相同，均为 `99.138.199.104.bc.googleusercontent.com.` 。可以看出，这个 IP 是属于 Google 的。此外，需要注意的是 in-addr.arpa. 下的字域名正好与原 IPv4 地址排列相反。 这个记录通常可以由 IP 的所有者进行设置。然而，IP 的所有者可以将这项记录设置为任何域名，包括别人的域名。所以通过这种方法所判断其属于 Google 并不准确，所以，我们还需要对其验证：

```
$ dig 99.138.199.104.bc.googleusercontent.com a +short
104.199.138.99
```

可以看到，这个域名又被解析到了原本的 IP，此时验证完毕，确认了这个 IP 属于 Google。所以，通过 `gethostbyaddr` 之后，还要继续 `getaddrbyhost` 验证。 然而，进行这种解析时解析服务器是由谁提供的呢？我们来看看 `99.138.199.104.in-addr.arpa.` 的上一级域名的解析记录：

```
$ dig 138.199.104.in-addr.arpa. any
;; QUESTION SECTION:
;138.199.104.in-addr.arpa.	IN	ANY
;; ANSWER SECTION:
138.199.104.in-addr.arpa. 21583	IN	NS	ns-gce-public1.googledomains.com.
138.199.104.in-addr.arpa. 21583	IN	NS	ns-gce-public2.googledomains.com.
138.199.104.in-addr.arpa. 21583	IN	NS	ns-gce-public3.googledomains.com.
138.199.104.in-addr.arpa. 21583	IN	NS	ns-gce-public4.googledomains.com.
138.199.104.in-addr.arpa. 21583	IN	SOA	ns-gce-public1.googledomains.com. cloud-dns-hostmaster.google.com. 1 21600 3600 259200 300
```

可以看到，上一级 `138.199.104.in-addr.arpa.` 下配置了 Google 的域名服务器，所以 `104.199.138.0/24`（`104.196.123.0 - 104.196.123.255`）都是属于 Google 的。然而，既然这个域名下的域名服务器可以设置为 Google 自己的，所以这个域名下就可以设置任何记录，不只是 PTR，所以也可以添加 A 记录把 arpa 域名当作网站！ 我虽然没有那么大块的 IPv4 地址，但是我有这个 IPv6 地址：`2001:470:f913::/48` 并且能够设置在 `arpa.` 下的 NS 记录。这个 `/48` 的 IPv6 地址对应着 `arpa.` 反向解析的完整域名是 `3.1.9.f.0.7.4.0.1.0.0.2.ip6.arpa.` ，可以看到 IPv6 的反向解析在另一个二级域名 `ip6.arpa.` 下，此外其地址是将 IPv6 的每一个十六进制为拆开为一个字域并反向排列的。 我已把这个地址设置为了我自己可以控制的 NS，然后配置了 A 记录，瞬间这个反向解析域名就可以当作正常的解析使用了！

*   `3.1.9.f.0.7.4.0.1.0.0.2.ip6.arpa`
*   `ze3kr.3.1.9.f.0.7.4.0.1.0.0.2.ip6.arpa`

如果你试图用浏览器访问这两个域名，会收到证书错误的报告，因为我还没给这个域名签发证书。
