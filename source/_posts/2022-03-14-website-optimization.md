---
title: 本站改版后的架构与优化——Hexo，分区解析，CDN，图像压缩等
date: 2022-03-14 11:59:59
tags: 
  - 响应式设计
  - 网站
  - CDN
categories:
  - 开发
---

去年，我把我的网站从 WordPress 迁移到了 Hexo。随后的一段时间里，我对 Hexo 进行了大量定制，包括修改主题，实现图片自适应，适配视频等。

## 基础架构

本站使用 [Hexo](https://hexo.io/zh-cn/) 博客框架生成静态网页，使用 Nginx 服务器分发静态网页、图片等。本站的静态文件和图片同时部署在全球四台服务器上，使用 Route 53 实现了分区解析和宕机后自动切换，使用 CloudFront 和 UPYUN CDN 分发视频。

<!-- more -->

## 主题

本站主题是在 [Claudia](https://github.com/Haojen/hexo-theme-Claudia) 的基础上进行[定制与魔改](https://github.com/ZE3kr/hexo-theme-Claudia)。原主题就有如下功能：

+ 移动端适配
+ 评论插件集成
+ 页面边栏 (个人信息，最近文章等)
+ 自适应设备的深色模式
+ 目录自动生成
+ 现代化的设计语言

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/1f3d2ac8-a17d-4d1a-7d16-41a1995ac401/extra" alt="Claudia 主题封面" width="3168" height="2455"/>

我对其增加的功能主要如下：

### 图片

+ 自动为文章中图片增加 srcset 和 sizes 属性
+ 支持全屏展示
+ 支持全屏后缩放，并按需加载缩放后的分辨率
+ 移动端 `100vw` 宽度无边距
+ 自动将图片转化为 `<figure>` 并展示图片描述

### 视频

+ 自动解析 `<iframe>` 中 `src=` 的内容并将其放在 `srcdoc=` 中减少网络请求
+ 可以实现在首页显示视频预览并自动静音播放（集成了 `HLS.js`）

### 搜索

原本主题的搜索功能在电脑端只会在最右侧的边栏展示搜索结果，我对其进行了改进，实现了用户在搜索时搜索栏和搜索结果自动延长。各位可以直接前往首页体验，或者查看下方的效果视频：

<figure class="my-video">
  <div style="position: relative; padding-top: 62.24299065420561%;"><iframe src="https://cdn.yangxi.tech/iframe/beb50392b3f14f49b01fb75b20d4cef7?controls=false&muted=true&preload=metadata&loop=true&autoplay=true&poster=https%3A%2F%2Fcdn.yangxi.tech%2Fbeb50392b3f14f49b01fb75b20d4cef7%2Fthumbnails%2Fthumbnail.jpg%3Fheight%3D600" style="border: none; position: absolute; top: 0; left: 0; height: 100%; width: 100%;"  allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen="true"></iframe></div>
  <figcaption>网站搜索演示</figcaption>
</figure>


## 分区解析与 CDN

我的网站目前同时部署在国内外多个主机上，使用着相同的配置。域名使用 Route 53 的延迟记录进行分区解析，并开启了 “运行状况检查” 实现宕机后自动切换服务器目前本站使用了 5 个服务器，分别部署在北京、东京（日本）和拉斯维加斯 (美国)、蒙特利尔 (加拿大) 和法兰克福 (德国)。

选择使用 Route 53 作为解析服务器的原因是：

+ 它使用了 Anycast 技术，在全球访问速度都很快
+ 国内连接 Route 53 的 DNS 服务器可以连接到延迟小于 50ms 的日本节点，
+ 支持 DNSSEC 和 IPv6
+ 支持按地区的分区解析、延迟解析等。在美国可以细分到州，其他地方也可以细分到国家
+ 支持 “运行状况检查”
+ 按量计费。起步价每个域名 $0.50 /月，“运行状况检查” 每个服务器 $0.50 /月。上述的所有功能也均是按量付费，没有固定月费。

### 视频 CDN

本站视频虽然是使用 Cloudflare Stream 存储和转码，但实际分发使用的是 CloudFront 和阿里云 CDN。主要是 Cloudflare 的视频是按量计费，其单价比 CloudFront 要贵。其次是 Cloudflare Stream 不提供国内节点，因此为国内提使用阿里云 CDN。

#### 双 CDN 配置——Nginx 替换 CDN URL

实现双 CDN 有两种方法，第一种是使用 Nginx 根据访客国家替换 CDN URL。这样做相比分区解析的好处是即便用户配置的 DNS 服务器非用户所在地服务器，替换 CDN URL 方式依然会让用户使用正确的 CDN。可以采用了 Nginx 根据客户端 IP 的国家进行 CDN 域名的替换，具体配置——在 `http` 中：

```
geoip2 /usr/share/GeoIP/GeoLite2-Country.mmdb {
    auto_reload 1d;
    $geoip2_data_country_code default=CN country iso_code;
}

map $geoip2_data_country_code $tlo_domain {
    default    "tlo.xyz";
    "CN"       "tloxygen.com";
}
```

在 `server` 中：

```
sub_filter '//videodelivery.net/' "//video.${tlo_domain}/";
```

这样一来，用户访问网站时，服务器会根据用户访问网站时的 IP 地址（而非 DNS 提供的 IP 地址）来选择合适的 CDN。

#### 双 CDN 配置——分区解析

这就不用多说了，将两个 CDN 绑定在一个域名上，使用 GeoDNS 对不同地区的访客返回不同的结果。

### 图像 CDN

本站的图片均使用 Cloudflare Images 进行压缩与存储，并通过 Nginx 进行代理与缓存。Nginx 配置如下，实现了根据设备兼容性优先提供 AVIF、WebP 和 JPEG。这是因为 Cloudflare Images 在国内的速度不佳。此外 Cloudflare Images 可以在访问时自动将图片调整分辨率和格式。当用户访问相对路径 `/cdn-cgi/imagedelivery/` 时，网站就会加载相应的图片，还省去了与新的域名建立 HTTP 连接的时间。

#### Nginx 配置

在 `http` 中：

```
map $http_accept $suffix {
    default        "jpeg";
    "~image/avif"  "avif";
    "~image/webp"  "webp";
    "~image/apng"  "apng";
}

map $http_accept $png_suffix {
    default        "png";
    "~image/avif"  "avif";
    "~image/webp"  "webp";
    "~image/apng"  "apng";
}

map $http_accept $gif_suffix {
    default        "gif";
    "~image/avif"  "avif";
    "~image/webp"  "webp";
    "~image/apng"  "apng";
}

map $http_accept $default_suffix {
    default        "default";
    "~image/avif"  "avif";
    "~image/webp"  "webp";
    "~image/apng"  "apng";
}

map $sent_http_Content_Type $file_name {
    default        "default";
    "image/avif"   "avif";
    "image/webp"   "webp";
    "image/apng"   "apng";
    "image/jpeg"   "jpeg";
    "image/png"    "png";
    "image/gif"    "gif";
}
```

在 `server` 中

```
proxy_store /var/www/images$uri/image.$file_name;

location /cdn-cgi/imagedelivery/6T-behmofKYLsxlrK0l_MQ/ {
    root /var/www/images/;

    try_files $uri/image.$suffix $uri/image.$png_suffix $uri/image.$gif_suffix $uri/image.$default_suffix @proxy;
}

location @proxy {
    proxy_ssl_name imagedelivery.net;
    proxy_ssl_server_name on;
    proxy_ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3;
    proxy_set_header Host imagedelivery.net;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection upgrade;
    proxy_hide_header Upgrade;
    proxy_hide_header Alt-Svc;
    proxy_hide_header Expect-CT;
    proxy_http_version 1.1;
    proxy_set_header Connection "";
    if ($uri ~* '^https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/(.*)$') { 
        add_header X-Cache-Status "MISS";
        proxy_pass https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/$1;
        break;
    }
}
```

通过 `proxy_store`，图片将会在第一次请求后永久的存储在本地，并在下次访问时从本地提供。在存储时，图片的变体名和格式会作为文件名的一部分进行存储。在访问时，服务器会根据客户端发送的 `http_accept` 请求头去查找对应文件。

AVIF 格式比 WebP 的压缩效率更好，而 WebP 格式比 JPEG 的压缩效率更好。然而对于兼容性而言，JPEG > WebP > AVIF。尽量不要在网站上使用 GIF，如果需要展示动画，可以使用静音自动循环播放的 `<video>` 代替。

#### WebP/AVIF 自适应之 CDN 配置

如果原站支持 WebP/AVIF 自适应，那么在配置 CDN 的时候，需要选择根据客户端的 Accept 头进行缓存。CloudFront 和阿里云 CDN 的配置分别如下：

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/7f9e5347-4fe6-4af1-fde4-77a6cd962801/extra" alt="CloudFront Accept 配置" width="1602" height="1022"/>

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/d64345ba-b229-4f19-8426-9005f7fe6001/extra" alt="阿里云 CDN Accept 配置" width="1588" height="1456"/>

## 自动部署

本站[使用 GitHub Action 在服务器上运行 SSH 脚本](https://github.com/ZE3kr/GuoZeyu.com/blob/17ec424703392867527f85aa9ce198a45859b4e9/.github/workflows/ci.yaml)实现自动部署。在服务器上配置了 `post-merge` 的 git hooks，在有改动后自动运行脚本。具体来讲，是在一个部署专用的服务器上生成静态页面，然后将这些静态页面分发到对应的服务器。

## 统计

本站使用[自建的 Matomo](/2016/01/piwik-wordpress/)网站统计。Matomo 是一个基于 PHP 和 MySQL 的非常强大的开源统计软件。

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/4285add5-3bcb-4cb8-168d-240bc55c0501/extra" alt="Matomo 后台管理界面截屏" width="2722" height="1716"/>

除此之外，本站还通过 JavaScript 实现了对视频播放，图像查看和搜索的统计。本站还使用了 Nginx 的 post_action 功能实现了异步发送统计，具体在 `server` 中的配置如下：

```
location /api/a {
    post_action @tracker;
    return 204;
}

location @tracker {
    internal;
    proxy_pass https://origin/matomo.php$is_args$args;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection upgrade;
    proxy_set_header Host matomo.tloxygen.com;
    proxy_set_header TLO-Connecting-IP $remote_addr;
}
```

## HTTP/2 Server Push

本站还启用了 HTTP/2 Server Push，用户在访问网站时，服务器会一次性直接推送首屏渲染必要的 CSS 和 JS 文件，由于用户不需要在获取到 HTML 页面后再去获取 CSS 和 JS 文件，首屏渲染的时间大大缩短。实测在模拟 Fast 3G 环境下，启用 HTTP/2 Server Push 后首屏时间减少了约 0.3 秒：

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/fe478b12-883e-4a9f-f3df-76a634c5a901/extra" alt="仅启用 HTTP/2，首屏耗时 1.48 秒" width="1376" height="1012"/>

<img src="https://cdn.yangxi.tech/6T-behmofKYLsxlrK0l_MQ/a2192c1b-c769-4ff4-c759-084291df6101/extra" alt="启用 HTTP/2 Server Push，首屏耗时 1.20 秒" width="1376" height="1012"/>

具体 Nginx 中 `server` 的配置如下

```
listen 443 ssl http2;
listen [::]:443 ssl http2;

location ~* \.(?:html)$ {
    http2_push /style/common/bulma.css;
    http2_push /style/base.css;
    http2_push /style/common/helper.css;
    http2_push /style/post.css;
    http2_push /style/widget-header.css;
    http2_push /style/widget-post-list.css;
    http2_push /style/themes/highlight-theme-light.css;
    http2_push /js/common.js;
    http2_push /js/sdk.latest.js;
    http2_push /js/post.js;
}
```
