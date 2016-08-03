---
layout: post
status: publish
published: true
title: 自建 PowerDNS 智能解析服务器
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1832
wordpress_url: https://ze3kr.com/?p=1832
date: '2016-08-03 09:24:21 -0400'
date_gmt: '2016-08-03 01:24:21 -0400'
categories:
- 开发
tags: []
---
<p>最近我越来越喜欢自建一些东西，比如 GitLab。今天我又把 DNS 服务器改成自建的了，分享一下经验：</p>
<p>首先，我先说用自建 DNS 服务器的<strong>致命坏处</strong>：</p>
<ol>
<li>如果那天自己的服务器挂了，整个域名相关服务都会挂，即使你邮件收信服务器是用的是第三方的，你也不能收信了</li>
<li>基本上必须是开放端口，并有<strong>固定 IP</strong>（而且最好还需要至少两个 IP） 的 VPS</li>
<li>个人一半关于 DNS 运维经验不足，容易导致配置错误</li>
<li>第三方 DNS 提供商基本都有 DDOS 防御，而你的服务器可不一定有，攻击者可以直接通过 L7 DNS Flood 攻击掉你的服务器，然后又回到第一个问题上了</li>
</ol>
<p>使用自建 DNS 服务器优点：</p>
<p><!--more--></p>
<ol>
<li>可 DIY 程度极大，各种 DNS 方面功能几乎都能配置，但却又都十分复杂</li>
<li>可以建在已有的服务器上，不用额外花钱（但也不一定，请看下文）</li>
</ol>
<p>最终我还是选择了使用 PowerDNS 软件（这其实也是很多提供 DNS 服务的服务商所使用的），我安装它的最近才出的 4.0 版本，这个版本支持的一些特性：</p>
<ul>
<li>EDNS Client Subnet</li>
<li>DNSSEC</li>
<li>GEODNS</li>
<li>IPv6</li>
</ul>
<p>等等，以上只是我想到的。同时 PowerDNS 支持超多的解析记录种类（至少是我目前见过最多的）：A、AAAA、AFSDB、ALIAS（也是 ANAME）、CAA、CERT、CDNSKEY、CDS、CNAME、DNSKEY、DNAME、DS、HINFO、KEY、LOC、MX、NAPTR、NS、NSEC、NSEC3、NSEC3PARAM、OPENPGPKEY、PTR、RP、RRSIG、SOA、SPF、SSHFP、SRV、TKEY、TSIG、TLSA、TXT、URI 等，还有不常用的没有列出来，见<a href="https://doc.powerdns.com/md/types/" target="_blank">所有支持的记录</a>。说实话有一些冷门的记录很多解析商都不支持，但我又需要用，比如 LOC、SSHFP 和 TLSA。不知道这一堆记录是干什么的？请见<a href="https://en.wikipedia.org/wiki/List_of_DNS_record_types" target="_blank">维基百科</a>。</p>
<p>安装方法<a href="https://doc.powerdns.com/md/authoritative/installation/" target="_blank">见此</a>，需要先安装 <code>pdns-server</code> ，然后再安装 <code>pdns-backend-$backend</code> 。Backend 是你可以自己选的，常用的有 <code>BIND</code> 和 <code>Generic MySQL</code> ，需要 GEODNS 可以用 <code>GEOIP</code> ，所有列表<a href="https://doc.powerdns.com/md/authoritative/" target="_blank">见此</a>。如果想做网页版控制后台，使用 MySQL 的可能比较方便。如果只是通过文件形式控制，那么 BIND 和 GEOIP 都可以。</p>
<p>我使用 GEOIP 版本的，GEOIP 版本可拓展性强，使用 YAML 文件，更灵活、优雅，本文就讲讲 GEOIP 版本：</p>
<p>在 Ubuntu 上安装（系统软件源里就有）：</p>
<pre>$ sudo apt install pens-server
$ sudo apt install pdns-backend-geoip
</pre>
<p>然后修改配置文件：</p>
<pre>$ rm /etc/powerdns/pdns.d/* # 删除 Example
</pre>
<h3>安装地理位置数据库</h3>
<p>注意，你应该已经有 MaxMind GeoIP Lite 数据库，如果没有，通过如下方式安装：</p>
<p>创建文件 <code>/etc/GeoIP.conf</code> 内容是：</p>
<pre># The following UserId and LicenseKey are required placeholders:
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
ProductIds 506 GeoLite-Legacy-IPv6-Country # 安装 IPv4 和 IPv6 国家模块

DatabaseDirectory /usr/share/GeoIP
</pre>
<p>然后安装 geoipupdate，执行 <code>sudo apt install geoipupdate && mkdir -p /etc/powerdns/key && geoipupdate -v</code> ，你的数据库就已经下载完毕了。</p>
<h3>配置 PowerDNS</h3>
<p>创建文件 <code>/etc/powerdns/pdns.d/geoip.conf</code> 内容是：</p>
<pre>launch=geoip
geoip-database-files=/usr/share/GeoIP/GeoLiteCountry.dat /usr/share/GeoIP/GeoIPv6.dat # 选择 IPv4 和 IPv6 国家模块
geoip-database-cache=memory
geoip-zones-file=/share/zone.yaml # 你的 YAML 配置文件的位置，随便哪个地方都行
geoip-dnssec-keydir=/etc/powerdns/key
</pre>
<p>创建那个 YAML 文件，然后开始写 Zone，这是一个例子（IPv6 不是必须的，所有 IP 应该都填写外部 IP，本文以精确到国家举例，并列内容的顺序无所谓）：</p>
<pre># @see: https://doc.powerdns.com/md/authoritative/backend-geoip/
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
    ns1.example.com: # 你的服务器的第二个 IPv4 地址（如果没有就和上面一样）
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
    beijing-server.example.com: &amp;beijing
      - a: 10.0.1.1
      - aaaa: ::1:1
    newyork-server.example.com: &amp;newyork
      - a: 10.0.2.1
      - aaaa: ::2:1
    japan-server.example.com: &amp;japan
      - a: 10.0.3.1
      - aaaa: ::3:1
    london-server.example.com: &amp;uk
      - a: 10.0.4.1
      - aaaa: ::4:1
    france-server.example.com: &amp;france
      - a: 10.0.5.1
      - aaaa: ::5:1


##### GEODNS 分区解析
    # @see: https://php.net/manual/en/function.geoip-continent-code-by-name.php
    # @see: https://en.wikipedia.org/wiki/ISO_3166-1_alpha-3
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

</pre>
<p>这个配置，就相当于把 www.example.com 给分区解析，由于目前这个解析存在一些问题，导致不能同时在根域名和子域名下设置 GEODNS，这个 Bug 我<a href="https://github.com/PowerDNS/pdns/issues/4276" target="_blank">已经提交反馈</a>了。</p>
<p>如果你想只把解析精度设在洲级别，那么就直接 %cn.geo.example.com 这样少写一级就行了。如果你需要精确到城市，那么多写一级就行，但是需要在配置文件中添加 GeoIP 城市的数据库。然而免费的城市数据库的城市版本并不精准，你还需要去购买商业数据库，这又是一个额外开销。</p>
<h3>配置域名</h3>
<p>前往你的域名注册商，进入后台修改设置，给域名添加上子域名服务器记录，如图：</p>
<p>[img id="1837" size="large"][/img]</p>
<p>由于要设置的 NS 是在自己服务器下的，所以务必要在域名注册商上向上级域名（如 .com）注册你的 NS 服务器 IP 地址，这样上级域名就能解析道 NS 的 IP，自建 DNS 才能使用，比如 icann.org 下就有一个属于自己的 NS：</p>
<pre>$ dig icann.org ns +short
a.iana-servers.net.
b.iana-servers.net.
c.iana-servers.net.
ns.icann.org.
</pre>
<p>然后再看它的上级域名 org：</p>
<pre>$ dig org ns +short
a2.org.afilias-nst.info.
b0.org.afilias-nst.org.
d0.org.afilias-nst.org.
c0.org.afilias-nst.info.
a0.org.afilias-nst.info.
b2.org.afilias-nst.org.
</pre>
<p>随便找一个服务器，查询权威记录（我就不用 +short 了）：</p>
<pre>$ dig @a0.org.afilias-nst.info icann.org ns

; &lt;&lt;&gt;&gt; DiG 9.8.3-P1 &lt;&lt;&gt;&gt; @a0.org.afilias-nst.info icann.org ns
; (2 servers found)
;; global options: +cmd
;; Got answer:
;; -&gt;&gt;HEADER&lt;</pre>
<p>可以看到，在这个 org 的 NS 服务器就已经把 ns.icann.org. 的记录返回来了，这也就是你需要在域名注册商填写 IP 地址的原因。然而你最好在你域名下的 DNS 服务器上也返回相同的 NS 和相同的 IP。</p>
<p>最后，不要忘了改域名的 NS 记录：</p>
<p>[img id="1838" size="large"][/img]</p>
<h3>YAML 的一些高级写法</h3>
<p>我刚才的 YAML 中其实就已经用到了 YAML 的高级写法，就是 <code>&amp;variable</code> 设置变量，<code>*variable</code> 使用变量，这很像 CloudXNS 下的 LINK 记录，比如在 CloudXNS 下你可以这么写：</p>
<pre>www.example.com    600   IN    A       10.0.0.1
www.example.com    600   IN    A       10.0.0.2
www.example.com    600   IN    AAAA    ::1
www.example.com    600   IN    AAAA    ::2
sub.example.com    600   IN    LINK    www.example.com
</pre>
<p>然后在你的 YAML 记录里就可以这么写：</p>
<pre>www.example.com: &amp;www
  - a: 10.0.0.1
  - a: 10.0.0.2
  - aaaa: ::1
  - aaaa: ::2
sub.example.com: *www
</pre>
<p>这就是 YAML 的一种高级写法，不需要其他额外支持。</p>
<h3>添加 DNSSEC 支持</h3>
<p>详情可以<a href="https://doc.powerdns.com/md/authoritative/dnssec/" target="_blank">参考文档</a>，运行以下指令：</p>
<pre>$ mkdir /etc/powerdns/key
$ pdnsutil secure-zone example.com
$ pdnsutil show-zone example.com
</pre>
<p>最后一个指令所返回的结果就是你需要在域名注册商设置的记录，不推荐都设置，只设置 ECDSAP256SHA256 - SHA256 digest 就行了。</p>
<p>最后<a href="http://dnssec-debugger.verisignlabs.com/" target="_blank">在线检查设置</a>即可，可能有几天缓存时间。</p>
<p>[img id="1842" size="large"][/img]</p>
<h3>其他一些有趣的东西</h3>
<p>你可以在 YAML 里写上这个，为了方便你调试：</p>
<pre>"*.ip.example.com":
  - txt:
      content: "IP%af: %ip, Continent: %cn, Country: %co, ASn: %as, Region: %re, Organisation: %na, City: %ci"
      ttl: 0
</pre>
<p>这些变量都能作为你 GEODNS 的标准，也可以检查你的 GEOIP 数据库情况。</p>
