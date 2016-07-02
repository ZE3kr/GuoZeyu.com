---
layout: post
status: publish
published: true
title: 奇虎 360 旗下的好搜所做的恶心事
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 176
wordpress_url: http://ze3kr.tlo.xyz/?p=176
date: '2015-08-18 23:10:44 +0000'
date_gmt: '2015-08-18 15:10:44 +0000'
categories:
- 科技
tags:
- 搜索引擎
---
<p>现在的搜索引擎都在尝试转码桌面站点，把网页内容重新排版。百度等搜索引擎都在尝试做这个，目的是让用户有 “一致化” 的体验。但是如果有一家商业公司将重新排版网页用作商业目的，这可就不好了。</p>
<p>我认为这种一致化有好有坏，不能下结论。但是你把网页内容重新排版后还加广告，那不就相当于是<strong>商业使用</strong>了么？360 旗下的好搜就是光明正大的做这个<strong>无视著作权</strong>的事情，在我创作的内容中加入它们的广告，于是它们便可以从中获利。但是它们<!--more-->官方的客服<a href="http://bbs.360safe.com/thread-4784046-1-1.html#post_38104495">却说这样的话</a>，我归纳的大意就是：</p>
<ol>
<li>你可以去我们的平台修改这个设置</li>
<li>你们的网站没有适配移动端，展现比较差</li>
<li>我们这样做是为你好，提高你的转化率</li>
</ol>
<p>但是，它们官方并没有明确的提出如何不让好搜给网站重新排版，说白了就是 “绑架” 站长，我好搜就给你的网站重新排版，就做<strong>无视著作权</strong>的事情，钻大陆法律空子，<strong>我是 360 我不怕</strong>。</p>
<p>360 一家安全公司本身就干过很多恰好违背安全的事情，在此我也不想提了，我对好搜做的这些恶心事也很无能为力，所以我就只能做些我能做的事了。</p>
<h2>截图证据</h2>
<p>[img size="medium" id="848"]重新排版后去掉了网站名[/img]</p>
<p>[img size="medium" id="849"]重新排版后加上了广告[/img]</p>
<h2>屏蔽好搜</h2>
<p>首先能做的事情就是屏蔽它，我特意看了<a href="http://www.haosou.com/help/help_3_2.html">它们的文档</a></p>
<p>然后我发现了啥？好搜竟然不支持使用 <code>robots.txt</code> 文件协议单独屏蔽它，好搜跪着求我：“不要屏蔽我，我还想那你的内容赚钱，你要非要屏蔽就把所有的都屏蔽了吧！”</p>
<p>当然，我是不怕这些的，我直接屏蔽你的 User Agent 不就好了。于是我在 <code>.htaccess</code> 文件下加入了如下内容：</p>
<pre class="lang:apache decode:true">RewriteEngine on

SetEnvIfNoCase User-Agent "360Spider" bad_bot # 屏蔽垃圾搜索引擎
SetEnvIfNoCase User-Agent "HaoSouSpider" bad_bot # 屏蔽垃圾搜索引擎
SetEnvIfNoCase User-Agent "360Spider-Image" bad_bot # 屏蔽垃圾搜索引擎
SetEnvIfNoCase User-Agent "360Spider-Video" bad_bot # 屏蔽垃圾搜索引擎
Deny from env=bad_bot</pre>
<p>然后我竟然发现了就算这样也依然没有效果！看来好搜为了不想让别的网站屏蔽可是下了功夫啊，没关系，我还有方法。</p>
<h3>利用 Referer 屏蔽它加载图片</h3>
<p>嗯，很成功，图片加载不了了，但是并没有完全屏蔽它。</p>
<p>[img size="medium" id="847"]利用 Referer 屏蔽[/img]</p>
<h3>利用好搜缺陷屏蔽它</h3>
<p>于是，我发现好搜并不支持抓取 HTTPS 站点，那好，我强制 HTTPS。</p>
<p>好搜终于怂了。</p>
