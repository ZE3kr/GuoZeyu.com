---
layout: post
status: publish
published: true
title: 几个 WordPress 的加速建议
author:
  display_name: ZE3kr
  login: ZE3kr
  email: ze3kr@tlo.xyz
  url: https://ze3kr.com
author_login: ZE3kr
author_email: ze3kr@tlo.xyz
author_url: https://ze3kr.com
wordpress_id: 1709
wordpress_url: https://ze3kr.com/?p=1709
date: '2016-06-09 17:36:00 +0000'
date_gmt: '2016-06-09 09:36:00 +0000'
categories:
- 开发
tags:
- 网站
- 网络
- HTML5
- WordPress
- 响应式设计
---
<p>WordPress 是目前最流行的内容管理系统，本网站正是使用着它。但对于一个全新安装的 WordPress 来说，它的性能并不是很高，当网站的访问量突然增加时，优化性能就显得十分重要了。通过实施以下几个方案，可以大大提升 WordPress 访问速度：</p>
<p><!--more--></p>
<h2>1. 配置缓存</h2>
<p>WordPress 是一个动态的系统，如果不配置缓存，每次请求都需要服务器去读取数据库，生成页面内容，对于不同性能的主机，这可能就需要 20ms~1000ms 甚至更慢。如果能够正确配置缓存，就可以明显的加速，并且减少主机的运算资源。</p>
<p>使用插件来配置缓存是最简单的方法。在此推荐 <a href="https://wordpress.org/plugins/wp-super-cache/" target="_blank">WP Super Cache</a>，这是 WordPress.com 出品的缓存插件，就页面缓存来说，功能非常全面，它支持多种缓存模式，包括 mod_rewrite，如果你使用 Nginx，那么<a href="https://git.tlo.xyz/ZE3kr/ZE3kr.com/snippets/6" target="_blank">可以使用我这个配置文件</a>。</p>
<p>同时，为浏览器返回正确的 <code>Cache-Control</code> 也是十分有必要的，尤其是 CSS 和 JS 文件。</p>
<h3>1.1 建立分布式缓存系统</h3>
<p>比如我的网站使用美国和日本的 VPS，主服务器在美国，配置了 Nginx，PHP 和 MySQL；另一个日本的服务器只配置了 Nginx。在日本和美国服务器上都配置好缓存，并用 lsyncd 同步缓存内容。每次访问时 Nginx 检查缓存，仅当没有缓存时代理，这样可以大大减少首页面的延迟。</p>
<h2>2. 使用 CDN</h2>
<h3>2.1 使用全站 CDN</h3>
<p>使用全站 CDN，可以免去在自己的服务器上配置缓存的问题，还可以为服务器增加 HTTPS、HTTP/2 等功能，同时还能过滤非法流量，防御 DDOS（前提是你的 IP 没有被暴露，或者你设置好了白名单）。</p>
<p>除此之外，使用 CDN 后还能更加明显的提高网站加载速度，让访客从中受益。</p>
<h4>2.1.1 使用 CloudFlare</h4>
<p>CloudFlare 是可以免费使用的，使用 CloudFlare 前需要更改 DNS 服务商，然后无需额外配置就能使用了。但是它只会缓存 CSS、JS 和多媒体文件，不会缓存 HTML 页面，也就是说用户每次访问时还是会返回到原始服务器，页面本身的速度不会有明显提高，在原始服务器上配置缓存也是必要的。</p>
<h4>2.1.2 使用 KeyCDN 并配合插件</h4>
<p><a href="https://app.keycdn.com/signup?a=7126" target="_blank">KeyCDN</a> 是一个按流量使用付费的 CDN 提供商，使用我制作的插件 <a href="https://wordpress.org/plugins/full-site-cache-kc/" target="_blank">Full Site Cache for KeyCDN</a> 可以简单的对其配置，这个插件会自动刷新缓存。</p>
<p>KeyCDN 相比 CloudFlare，可以缓存 HTML 页面，大大减少源服务器的压力，同时在刷新缓存时可以通过 Tag 方式刷新，避免不必要的刷新。</p>
<p>在网站访问量较大时，使用 KeyCDN 就能明显的提高速度，缓存命中率也会有很大的提高。</p>
<h3>2.2 仅资源部份使用 CDN</h3>
<p>你可以配置另一个域名，在那个域名上使用 CDN，然后通过插件重写页面地址实现部分 CDN。上文提到的 WP Super Cache 就能配置 CDN，或者使用 <a href="https://wordpress.org/plugins/cdn-enabler/" target="_blank">CDN Enabler</a> 也能实现部分 CDN 功能。至于 CDN 的选择无所谓，只要支持 <a href="http://knowledgelayer.softlayer.com/questions/365/How+does+Origin+Pull+work%3F" target="_blank">Origin Pull</a> （也就是请求回源）就行。</p>
<h2>3. 服务器性能</h2>
<p>提高服务器本身的性能是最简单的方法，使用更大的内存，更多核心的 CPU 能明显提速。除此之外，提高服务器上应用的性能也很重要：</p>
<h3>3.1 脚本性能</h3>
<p>PHP 脚本的处理速度是 WordPress 的一大瓶颈，使用最新版本的 PHP，可以获得更高的性能，例如 <a href="https://www.zend.com/en/resources/php7_infographic" target="_blank">7.0 就比 5.6 快了 3 倍</a>。</p>
<p>其次，少用插件能减少 PHP 需要执行的脚本，因为在加载每一个页面时，WordPress 都会加载一遍所有的插件。少量的插件（10个以下）对 WordPress 速度的影响不大，当然也取决于插件本身。</p>
<h3>3.2 数据库性能</h3>
<p>数据库是 WordPress 性能的瓶颈之一，在数据库上优化能提高一定的速度。</p>
<p>一般情况下，如果正确的使用 WordPress，并不需要清理数据库。但可能会有某些插件可能在数据库中创建了太多没用的表，这时服务器的响应速度就会大大降低（约 1～3 倍），推荐使用 <a href="https://wordpress.org/plugins/wp-optimize/" target="_blank">WP-Optimize</a> 进行清理。</p>
<p>使用最新版本的 MySQL，可以获得更高的性能，比如 <a href="https://www.mysql.com/why-mysql/benchmarks/" target="_blank">5.7 就比 5.6 快了 3 倍</a>。其次，正确配置数据库参数，也能有效的提高性能。</p>
<p>不是太多的文章数量，是不太会的影响加载速度（1 万篇文章以下速度其实都还能接受，不过你写那么多文章干嘛，质量比数量更重要嘛）。</p>
<h2>4. 图片优化</h2>
<p>图片占据着网页中很大一部分的大小，同时也关系着用户体验。</p>
<h3>4.1 图片压缩</h3>
<p>对图片压缩不仅可以提高访问时图片加载速度，还能减少服务器带宽</p>
<p>推荐使用免费的 <a href="https://wordpress.org/plugins/ewww-image-optimizer/" target="_blank">EWWW Image Optimizer</a>，可以在服务器上对图片进行处理。如果你的服务器性能有限，可以使用 <a href="https://optimus.io/en/" target="_blank">Optimus</a> 在线处理，免费版功能十分有限。</p>
<h3>4.2 响应式图片</h3>
<p>对于不同的设备加载不同的图片，比如在手机上加载的图片，就可以比视网膜屏幕的电脑上要加载的图片小的多。使用 <a href="https://ze3kr.com/2015/08/using-srcset/" target="_blank">本站曾经提到过的 srcset 技术</a>可以最简单的实现这个功能，只要你的主题支持就可以了（官方的最新默认主题已经支持），如果主题本身不支持，也可以通过插件实现。</p>
<h2>5. 禁用不需要的服务</h2>
<p>WordPress 中有一些自带的服务你可能并不需要，禁用它们可以减少页面所需要加载的资源以及服务器需要做的事情。</p>
<h3>5.1 Emoji 支持</h3>
<p>WordPress 支持 Emoji 表情符号，但会因此在页面中引入额外的 CSS，<a href="https://gist.github.com/MaruscaGabriel/fc7c069860406c77304a" target="_blank">使用这个脚本</a>可以禁用它（如果你不需要的话）。</p>
<h3>5.2 Google Fonts</h3>
<p>Google Font 在国内加载非常慢，而且加载完成之前页面会一直白屏。你可以专门安装 Disable Google Fonts 的插件，或者在下文要提到的 Autoptimize 插件中的设置里禁用它。</p>
<h3>5.3 Pingback</h3>
<p>进入 设置  =&gt; 讨论 中可以禁用 “尝试通知文章中链接的博客” 和 “<label for="default_ping_status">允许其他博客发送链接通知（pingback和trackback）到新文章</label>” 功能（如果你不需要的话）。 此功能并不影响页面加载时间。</p>
<h2>6. 减少请求数和页面大小</h2>
<p>减少请求数在也一些情况下也能提高加载速度，减少页面大小能缩短下载页面所需要的时间。推荐使用 <a href="https://wordpress.org/plugins/autoptimize/" target="_blank">Autoptimize</a>，它可以 minify CSS、JS 和 HTML，还能 combine CSS 和 JS 以减少请求数。</p>
<p>然而，如果你的博客启用了 HTTP/2 协议，那么减少请求数就没什么必要了，不过为了启用 HTTP/2，必须要使用 HTTPS，所以最终下来是快是慢也不好说。不过还是强烈推荐使用 HTTPS，为了安全，牺牲点速度算什么。</p>
<h2>7. 总结</h2>
<p>做到上面几点，就能有效提速了，我的网站做到以上几点，在国内无缓存的 Wi-Fi 情况下本网站的时间线如下：</p>
<p>[img id="1711" size="large"]在 1 秒钟内完成包括图片在内的加载[/img]</p>
