---
title: WordPress 上的几个推荐安装的插件
tags:
  - WordPress
id: '96'
categories:
  - - 开发
date: 2016-01-29 10:19:00
languages:
  en-US: https://ze3kr.com/2016/01/wordpress-plugins/
---

## EWWW Image Optimizer

无损及有损压缩 JPEG 和 PNG 图像，支持压缩已有的图像，可以加快访问者加载图片的速度。同时支持 JPEG 的渐进式加载。正常情况下，网速低时，图片是一点点从上往下加载，**而使用渐进式加载，则是先加载这个图片的低分辨率版本，然后逐渐变得清晰。**我已经成为这个插件的付费用户，能够进一步有损压缩 PNG 和 JPEG 格式图片、降低服务器 CPU 占用（否则每上传一张图片，CPU 都消耗很多）。不过最近使用了 Cloudflare 的 Polish 功能，就没有再在服务器上安装同类软件了。

## Autoptimize

这个插件能够自动的合并 CSS 和 JS，并对其压缩，非常<!-- more -->的方便。而且一些主题会有大量的 inline CSS，当开启了合并 CSS 后，这些 inline CSS 会自动添加到文件中。 我在这个插件中的**一些配置**，分享出来：

### Exclude scripts from Autoptimize

functions.js,player,mediaelement,sparkline,toolbar,admin,akismet,themes

*   functions.js,themes：排除所有主体主题相关的脚本，全面解决合并后会导致菜单的交互出现问题
*   player,mediaelement：排除 WordPress 播放器的合并，因为这部分文件有的页面有，有的页面没有
*   sparkline,toolbar,admin：这些文件只有用户登录后才会加载
*   akismet：安装了 akismet 插件后推荐添加

### Exclude CSS from Autoptimize

admin,mediaelement,wordfence,piwik,toolbar,dashicons,crayon-syntax-highlighter/themes,crayon-syntax-highlighter/fonts

*   admin,mediaelement,toolbar,dashicons：同上，只有管理员才会加载，或者是只有部分页面才会加载的东西
*   crayon-syntax-highlighter/themes,crayon-syntax-highlighter/fonts：排除 Crayon Syntax Highlighter 插件仅在部分页面才会加载的 CSS
*   wordfence,piwik：排除一些插件

### 建议开启

*   **Optimize HTML Code**：优化 HTML 代码，能减少页面大小
*   **Optimize JavaScript Code**：优化 JS 代码，能减少 JS 大小并合并 JS
*   **Force JavaScript in `<head>`**：能提前加载 JS
*   **Optimize CSS Code**：优化 CSS 代码，能减少 CSS 大小并合并 CSS
*   **Generate data: URIs for images**：将文件直接嵌入 CSS 中，减少请求数
*   **Remove Google Fonts**：移除 Google 字体，这种字体文件都是只对西文有效的，对于中文没有意义。
*   **Also aggregate inline CSS**：将 Inline 的 CSS 放到 CSS 文件里，能减少页面大小
*   **Save aggregated script/css as static files**：能提高处理性能

### 建议关闭

*   **Keep HTML comments**：有些时候 HTML comments 是有作用的
*   **Also aggregate inline JS**：每个页面的 JS 都不同，不要开启
*   **Add try-catch wrapping**：没有什么意义的东西
*   **Inline and Defer CSS**：将 CSS 全面嵌入到页面里，会极大增加页面大小，但能减少请求数，但是真的大多数情况下只会让网站更慢。
*   **Inline all CSS**：同上

## AMP

插件作者是：Automattic ，为 Google 优化，搜索结果中展示超快的 AMP 页面。详情：[使用 AMP 构建超快的移动页面](https://guozeyu.com/2016/10/amp-html/)

## Google Authenticator

二部认证器（需要配合同名手机客户端，或者 1Password 专业版）。你可曾听说过 Heart Bleed 漏洞？有了二部认证，就算再有 Heart Bleed 漏洞，都无需害怕。 原理：手机生成器上扫描二维码保存密钥，然后生成器根据时间变量生成6位数字。这6位数字有有效期，并且只能用一次。安全性甚至大于短信验证码

## IP Dependent Cookies

更换 IP 后需要重新登录，就算使用 HTTP 也可以一定程度上避免 Cookie 泄漏的风险

## Wordfence

WordPress #1 的**安全防御**插件，可限制密码尝试次数防止暴力破解，可为你的 WordPress 增加 WAF 功能，查看实时访问。通过查看它的日志我才发现，原来我的 WordPress 一直在被恶意的暴力破解，有时一天几千次，**几乎天天都有**。不过还好只要尝试 3 次错误密码，IP 会直接被屏蔽掉，这也是 Failed Logins 数量不多的原因。 像这种是基于应用层面的防御，并不是 Cloudflare 等云加速，抗 DDOS 服务所能防御的。

## Crayon Syntax Highlighter

这个插件能够让 WordPress 上展示自动高亮的源代码，有多种主题可以选择，支持多种编程语言。

## Slimpack

![Slimpack 截图](https://cdn.ze3kr.com/6T-behmofKYLsxlrK0l_MQ/60c81119-cf51-482d-8603-17a82c163800/large)

这是 Jetpack 的简化版，没有 Jetpack 那一堆没用的特性，不需要登录到 wordpress.com，功能齐全，使用起来也非常简便。

## XML Sitemap & Google News feeds

这个插件能够自动生成网站的 `sitemap.xml` 和 `robots.txt`，你可以直接将 `sitemap.xml` 提交到百度和 Google，这样搜索引擎就不会漏掉你的网站上的每一篇文章了。 这个插件支持 WordPress 的多站点，不需要任何配置，推荐在整个网络上启用。

## WP-Piwik

![WP-Piwik 截图](https://cdn.ze3kr.com/6T-behmofKYLsxlrK0l_MQ/86f98b39-867f-4be9-4c16-6ffc480f2200/large)

这个插件能够让你的整个网站拥有统计功能，支持 WordPress 的多站点，推荐在整个网络上启用。关于 Piwik 配合 WordPress，请参见这篇文章：[Piwik 与 WordPress 配合使用，建立强大统计系统](https://guozeyu.com/2016/01/piwik-wordpress/)。

## Blubrry PowerPress

这个插件能够让你的 WordPress 生成一个 Podcast Feed，让你有一个播客平台，你也可以把这个 Feed 直接提交到 iTunes 等地方。

![iOS 上播客软件的截图](https://cdn.ze3kr.com/6T-behmofKYLsxlrK0l_MQ/4688a970-5350-4c93-f6ff-e1579be7ea00/large)

## Exif Caption

这个插件可以让你将图片的 Exif 信息插入到图片的说明中，缺点是需要上传图片后手动添加，但是它可以批量添加，还算方便。将 Exif 信息插入到图片的说明后，再将这个图片插入到文章中，Exif 信息就能够显示在文章中。
