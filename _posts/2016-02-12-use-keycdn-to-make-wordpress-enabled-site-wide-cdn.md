---
ID: 1163
post_title: >
  利用 KeyCDN 让 WordPress 启用全站
  CDN1
author: ZE3kr
post_date: 2016-02-12 10:21:30
post_excerpt: ""
layout: post
permalink: >
  https://www.ze3kr.com/2016/02/use-keycdn-to-make-wordpress-enabled-site-wide-cdn/
published: true
dsq_thread_id:
  - "4572159826"
dsq_needs_sync:
  - "1"
---
如果要启用全站 CDN，就意味着所有页面都会被 CDN 缓存。所以就需要一个新的没有启用全站 CDN 的域名，这样才能访问 WordPress 的后台。同时还需要确保启用全站 CDN 的域名不会显示任何只有 Admin 才能显示的内容。

在此之前，你需要有一个 KeyCDN 账号，添加一个 Pull Zone，Origin URL 填写 <code>http://[你的IP]</code>（或者是 https 也可以，不要求证书有效）。SSL 设为 Letsencrypt，Cache Cookies 和 Strip Cookies 都启用。X-Pull Key 保持是 KeyCDN 不动。Canonical Header 要是 Disabled。

你可以大胆的调高 Max Expire (in minutes) 的值吧，我设置的就是 10080（一周），因为之后的配置，会自动刷新缓存。

在此之前，你需要确保域名不能是一级域名，如果是，务必去后台修改为 www. 开头的二级域名。如果 Blog 是多站点的主站，则无法修改，建议新建一个 www 并导入，如图：

<a href="https://media.landcement.com/sites/2/20160212101726/Screenshot-2016-02-12-10.17.11.png" rel="attachment wp-att-1164"><img src="https://media.landcement.com/sites/2/20160212101726/Screenshot-2016-02-12-10.17.11-450x259.png" alt="Screenshot 2016-02-12 10.17.11" width="450" height="259" class="aligncenter size-medium wp-image-1164" /></a>

然后就需要修改后台的 PHP 代码了，为了确保兼容性，我没有改写 Apache 或 Nginx Rewrite，一切都通过 PHP 解决。警告：你需要有 PHP 基础，再修改代码！

首先，你需要在 WordPress 目录下创建一个名为 <code>keycdn</code> 的目录，这个目录下放置两个文件。

第一个文件名为<!--more--> <code>before.php</code>，内容是：

<pre class="lang:php decode:true " title="before.php" >&lt;?php
if($useHTTPS) {
	if(!isset(explode('.',$_SERVER['HTTP_HOST'])[2]) &amp;&amp; $_SERVER['SCRIPT_NAME'] == '/index.php'){
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: https://www.'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		exit();
	}
	if($_SERVER['HTTP_X_PULL'] == 'KeyCDN') {
		if($_SERVER['SCRIPT_NAME'] != '/index.php'){
			/* Set redirect if is not a page */
			if(substr($_SERVER['HTTP_HOST'],0,4) == 'www.') {
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: https://wp-admin.'.substr($_SERVER['HTTP_HOST'],4).$_SERVER['REQUEST_URI']);
				exit();
			} else {
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: https://wp-admin-'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				exit();
			}
		}
		$_COOKIE = [];// Clear Cookie if use KeyCDN, so when use KeyCDN, never show adminbar.
		$_SERVER['HTTPS'] = 'on';// Set HTTPS on if use KeyCDN, even if the origin server is not HTTPS.
	} else {
		if($_SERVER['SCRIPT_NAME'] == '/index.php' ){
			if(substr($_SERVER['REQUEST_URI'],0,11) != '/robots.txt') {
				if(substr($_SERVER['HTTP_HOST'],0,9) == 'wp-admin-') {
					header('HTTP/1.1 301 Moved Permanently');
					header('Location: https://'.substr($_SERVER['HTTP_HOST'],9).$_SERVER['REQUEST_URI']);
					exit();
				} elseif(substr($_SERVER['HTTP_HOST'],0,9) == 'wp-admin.') {
					header('HTTP/1.1 301 Moved Permanently');
					header('Location: https://www.'.substr($_SERVER['HTTP_HOST'],9).$_SERVER['REQUEST_URI']);
					exit();
				}
			}
		}
		if($useCF){
			$visitor = json_decode($_SERVER['HTTP_CF_VISITOR'],true);
			if($visitor['scheme'] == 'https'){
				$_SERVER['HTTPS'] = 'on';
			}
		}
	}
	if($_SERVER['HTTPS'] == 'on' &amp;&amp;  $useHSTS){
		header('Strict-Transport-Security: max-age=15552000; includeSubDomains; preload');
	}
} else {
	if(!isset(explode('.',$_SERVER['HTTP_HOST'])[2]) &amp;&amp; $_SERVER['SCRIPT_NAME'] == '/index.php'){
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: http://www.'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		exit();
	}
	if($_SERVER['HTTP_X_PULL'] == 'KeyCDN') {
		if($_SERVER['SCRIPT_NAME'] != '/index.php'){
			/* Set redirect if is not a page */
			if(substr($_SERVER['HTTP_HOST'],0,4) == 'www.') {
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: http://wp-admin.'.substr($_SERVER['HTTP_HOST'],4).$_SERVER['REQUEST_URI']);
				exit();
			} else {
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: http://wp-admin-'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				exit();
			}
		}
		$_COOKIE = [];// Clear Cookie if use KeyCDN, so when use KeyCDN, never show adminbar.
	} else {
		if($_SERVER['SCRIPT_NAME'] == '/index.php' ){
			if(substr($_SERVER['REQUEST_URI'],0,11) != '/robots.txt') {
				if(substr($_SERVER['HTTP_HOST'],0,9) == 'wp-admin-') {
					header('HTTP/1.1 301 Moved Permanently');
					header('Location: http://'.substr($_SERVER['HTTP_HOST'],9).$_SERVER['REQUEST_URI']);
					exit();
				} elseif(substr($_SERVER['HTTP_HOST'],0,9) == 'wp-admin.') {
					header('HTTP/1.1 301 Moved Permanently');
					header('Location: http://www.'.substr($_SERVER['HTTP_HOST'],9).$_SERVER['REQUEST_URI']);
					exit();
				}
			}
		}
	}
}
$_SERVER = str_replace('wp-admin-','',$_SERVER);
$_SERVER = str_replace('wp-admin.','www.',$_SERVER);</pre> 

第二个文件名为 <code>main.php</code>，内容是：

<pre class="lang:php decode:true " title="main.php" >&lt;?php
if(is_home()||is_search()||is_archive()||is_feed()||is_front_page()){
	header('Cache-Tag: archive');
} elseif(strstr($_SERVER['REQUEST_URI'],'/sitemap') &amp;&amp; strstr($_SERVER['REQUEST_URI'],'.xml')) {
	header('Cache-Tag: archive');
}

function keycdn_purge( $post_ID ) {
	$blog_ID = get_current_blog_id();
	global $keycdn_url,$keycdn_apikey;
	if(isset($keycdn_url[$blog_ID])) {
		$url = 'https://'.$keycdn_apikey.':@api.keycdn.com/zones/';
		$url_end = '/'.$keycdn_url[$blog_ID]['id'].'.json';
		$url_endpoint = parse_url( get_permalink($post_ID) )['path'];

		wp_remote_request($url.'purgeurl'.$url_end,[
			'method' =&gt; 'DELETE',
			'body' =&gt; ['urls' =&gt; [
				$keycdn_url[$blog_ID]['name'].'-1bd6.kxcdn.com'.$url_endpoint,
			],],
			'httpversion' =&gt; '1.1',
			'blocking' =&gt; false,
		]);

		wp_remote_request($url.'purgetag'.$url_end,[
			'method' =&gt; 'DELETE',
			'body' =&gt; ['tags' =&gt; [
				'archive',
			],],
			'httpversion' =&gt; '1.1',
			'blocking' =&gt; false,
		]);
	}
}
add_action( 'publish_post', 'keycdn_purge', 10, 1 );
add_action( 'publish_page', 'keycdn_purge', 10, 1 );
add_action( 'trashed_post', 'keycdn_purge', 10, 1 );

function keycdn_remove_admin ( $url ) {
	$url = str_replace ( 'wp-admin.' , 'www.' , $url ) ;
	$url = str_replace ( 'wp-admin-' , '' , $url ) ;
	return $url ;
}

add_filter ( 'style_loader_src' , 'keycdn_remove_admin' , 99 , 1 ) ;
add_filter ( 'script_loader_src' , 'keycdn_remove_admin' , 99 , 1 ) ;
</pre> 

然后修改 <code>wp-config.php</code>，在文件的最顶部，<code>&lt;?php</code>的下一行添加上如下代码：
 
<pre class="lang:php decode:true " >// sets up KeyCDN
$useHTTPS = false; // Support HTTPS?
$useHSTS = false; // Want enable HSTS?
$useCF = false; // Use CloudFlare? Even if you set “Fixable SSL”, WordPress still can identify if visitor is using HTTPS.
require_once(ABSPATH . 'keycdn/before.php');</pre> 

根据环境，配置好这三个变量。

然后在 <code>wp-config.php</code> 的底部添加上如下代码：
 
<pre class="lang:php decode:true " >// sets up KeyCDN
$keycdn_apikey = 'u_BYvefTjzl7yi2r';
$keycdn_url = [
	2 =&gt; [
		'id' =&gt; 10001,
		'name' =&gt; 'ze3kr',
	],
	3 =&gt; [
		'id' =&gt; 10002,
		'name' =&gt; 'botball',
	],
	4 =&gt; [
		'id' =&gt; 10003,
		'name' =&gt; 'tlo',
	],
];
require_once(ABSPATH . 'keycdn/main.php');</pre> 

这是一个多站点的例子，id 就是 KeyCDN 的 Zone ID（多站点就必须要给每一个站点创建一个 Zone），Name 就是 Zone Name，前面的数字就是 Blog ID，如果没有开启多站点，那么只需要有一个，数字为 1 就好了。

然后，去修改 DNS 设置，改为 CNAME 到 KeyCDN 的 Zone URL，然后去 KeyCDN 的 Zonealiases 页面，选择 New Zonealiases，输入你的域名并选择对应这个域名的 Zone，添加好后等一段时间，就能访问了（需要等待 Letsencrypt 证书自动颁发）

一切就设置好了，网站的速度瞬间提升数倍！