---
layout: post
status: publish
published: true
title: Piwik 与 WordPress 配合使用，建立强大统计系统
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 70
wordpress_url: https://xn--oor13x.tlo.xyz/?p=70
date: '2016-01-23 08:00:00 -0500'
date_gmt: '2016-01-23 00:00:00 -0500'
categories:
- 开发
tags:
- WordPress
---
<p>在配合使用之前，首先需要安装 Piwik。[a href="https://piwik.org/download/"]前往 Piwik 官网下载软件包[/a]，然后解压到服务器上。当然，如果你的服务器上支持一键安装 Piwik 那更好。需要 PHP 环境和 MySQL 数据库。安装只需要根据步骤一步步来就好了。最好和 WordPress 安装在一个主机下，这样更方便配合使用。</p>
<h2>地理位置功能</h2>
<p>前往设置中的地理位置页面，在页面左下角选择下载一个 GeoIP 数据库。下载了之后，你就可以使用 GeoIP (Php) 了，然而这个比较慢。我推荐你使用 GeoIP (PECL)，如果你使用的是 cPanel，那么你可以直接在 Select PHP Version 页面中开启 GeoIP 模<!--more-->块，然后再去 <code>php.ini</code> 中加入或编辑这一行：</p>
<pre class="lang:ini decode:true">geoip.custom_directory = /piwik/misc</pre>
<p>然后就 OK 了，选择 GeoIP (PECL) 即可！</p>
<p>[img id="1024" size="medium"][/img]</p>
<h2>安装中文字体</h2>
<p>如果你的语言设置的是中文，那么你的图像报表中会出现乱码。请[a href="https://piwik.org/wp-content/uploads/unifont.ttf.zip"]下载字体文件[/a]，然后解压放到 <code>plugins/ImageGraph/fonts/</code> 下即可。</p>
<h2>与 WordPress 配合使用</h2>
<p>首先，需要在 WordPress 下安装 [a href="https://wordpress.org/plugins/wp-piwik/"]WP-Piwik[/img]（完美支持多站点），之后前往设置。如果你的 WordPress 和 Piwik 都安装在一个站点下了，那么就选择 PHP API，否则选择 HTTP API。填写 Piwik 路径和 Auth Token（可在 Piwik 的后台中找到）即可，然后打开 Enable Tracking，选择默认跟踪即可。在 Show Statistics 里可以选择在哪些地方显示哪些统计，非常方便。</p>
<p>[img id="1027" size="medium"][/img]</p>
<p>[img id="1026" size="medium"][/img]</p>
<p>[img id="1025" size="medium"][/img]</p>
<p>当你选择了 Show per post stats 后，你可以在每一个文章的编辑页面都能看到这个文章的访客信息，非常赞。</p>
<p>[img id="1028" size="medium"][/img]</p>
<h2>与 CloudFlare 配合使用</h2>
<p>首先，为了能够正确的识别用户 IP，你需要在 <code>config/config.ini.php</code> 中添加下面这行代码。</p>
<pre class="lang:ini decode:true ">proxy_client_headers[] = "HTTP_CF_CONNECTING_IP"</pre>
<p>如果你的 CloudFlare 启用了 IP Geolocation 功能，那么你其实不需要在主机上开启 GeoIP。</p>
<p>[img id="1087" size="medium"][/img]</p>
<p>根目录下创建 <code>.htaccess</code> 文件，添加下面这几行代码：</p>
<pre class="lang:apache decode:true ">RewriteEngine On
RewriteBase /

RewriteRule ^ - [E=GEOIP_COUNTRY_CODE:%{HTTP:CF-IPCountry}]</pre>
<p>然后前往 Piwik 统计的地理位置选项，现在，你的地理位置选项就可以选第三个了 GeoIP (Apache)，这个是速度最快的。</p>
<p>[img id="1088" size="medium"][/img]</p>
