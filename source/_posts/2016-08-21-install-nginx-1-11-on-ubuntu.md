---
title: 使用免费 Let's Encrypt 实现 ECDSA/RSA 双证书
tags:
  - 安全
  - 网络
id: '1904'
categories:
  - - 开发
date: 2016-08-21 10:52:13
languages:
  en-US: https://ze3kr.com/2016/08/install-nginx-1-11-on-ubuntu/
cover: https://cdn.ze3kr.com/6T-behmofKYLsxlrK0l_MQ/e8b62e3d-921d-453e-1b56-20cd930fa500/large
---

Ubuntu 16.04.01 自带的软件源中的是 Nginx 1.10.0，但是这个版本的 Nginx 的 HTTP/2 模块中存在 Bug，[具体见此](https://imququ.com/post/nginx-http2-post-bug.html)。现在 Nginx 1.12 Stable 已经推出，直接安装 Stable 版本即可。 2018-06 更新：如果你使用 Ubuntu 18.04 或者是以后的版本，那么系统默认的软件源的 Nginx 版本（1.14）就足够了。
<!-- more -->

关于双证书，**仅建议使用独立 IP 的人去使用**，如果没有独立 IP，那么就需要启用 SNI 功能——然而几乎所有支持 SNI 功能的浏览器也都支持了 ECC 证书，所以可以跳过升级步骤，直接换 Let's Encrypt 的 ECC 证书，不使用 RSA 证书。 我有不止一个服务器，如果都使用自己编译的 Nginx，那么太麻烦了，于是我决定使用添加软件源的方法，通过 `apt` 升级，方法如下： 首先需要先添加 Nginx mainline 的软件源：

```
$ sudo add-apt-repository ppa:nginx/stable # sudo apt install software-properties-common
$ sudo apt update
```

然后移除现有 Nginx 并安装新版本：

```
$ sudo apt remove nginx nginx-common nginx-core
$ sudo apt install nginx
```

安装时可能会询问是否替换原来默认的配置文件，选择 N 即可。 此时安装的 Nginx 已经包含了几乎所有的必要和常用模块，比如包括但不限于 GeoIP Module、HTTP Substitutions Filter Module、HTTP Echo Module。我安装的 Nginx 的 OpenSSL 版本是 1.0.2g-fips，所以并不支持 CHACHA20，想要支持 CHACHA20 只能使用 [CloudFlare 的 Patch](https://github.com/cloudflare/sslconfig) 然后自己编译。安装完成后就可以验证 Nginx 版本了：

```
$ nginx -V
nginx version: nginx/1.12.0
built with OpenSSL 1.0.2g  1 Mar 2016
TLS SNI support enabled
configure arguments: --with-cc-opt='-g -O2 -fPIE -fstack-protector-strong -Wformat -Werror=format-security -fPIC -Wdate-time -D_FORTIFY_SOURCE=2' --with-ld-opt='-Wl,-Bsymbolic-functions -fPIE -pie -Wl,-z,relro -Wl,-z,now -fPIC' --prefix=/usr/share/nginx --conf-path=/etc/nginx/nginx.conf --http-log-path=/var/log/nginx/access.log --error-log-path=/var/log/nginx/error.log --lock-path=/var/lock/nginx.lock --pid-path=/run/nginx.pid --modules-path=/usr/lib/nginx/modules --http-client-body-temp-path=/var/lib/nginx/body --http-fastcgi-temp-path=/var/lib/nginx/fastcgi --http-proxy-temp-path=/var/lib/nginx/proxy --http-scgi-temp-path=/var/lib/nginx/scgi --http-uwsgi-temp-path=/var/lib/nginx/uwsgi --with-debug --with-pcre-jit --with-http_ssl_module --with-http_stub_status_module --with-http_realip_module --with-http_auth_request_module --with-http_v2_module --with-http_dav_module --with-http_slice_module --with-threads --with-http_addition_module --with-http_geoip_module=dynamic --with-http_gunzip_module --with-http_gzip_static_module --with-http_image_filter_module=dynamic --with-http_sub_module --with-http_xslt_module=dynamic --with-stream=dynamic --with-stream_ssl_module --with-stream_ssl_preread_module --with-mail=dynamic --with-mail_ssl_module --add-dynamic-module=/build/nginx-DYnRGx/nginx-1.12.0/debian/modules/nginx-auth-pam --add-dynamic-module=/build/nginx-DYnRGx/nginx-1.12.0/debian/modules/nginx-dav-ext-module --add-dynamic-module=/build/nginx-DYnRGx/nginx-1.12.0/debian/modules/nginx-echo --add-dynamic-module=/build/nginx-DYnRGx/nginx-1.12.0/debian/modules/nginx-upstream-fair --add-dynamic-module=/build/nginx-DYnRGx/nginx-1.12.0/debian/modules/ngx_http_substitutions_filter_module
```

此时，你的服务器就没有 Nginx 的 HTTP/2 bug 了，既然使用了最新版的 Nginx，那么就能够配置 ECDSA/RSA 双证书了。

### Nginx 升级的小坑

在我升级的时候，遇到了 GeoIP 模块无法使用的问题，经研究发现是新版本将 GeoIP 改成动态调用模块的方式实现了，在 Nginx 的 `http {}` 配置中添加下方代码得以解决：

```
load_module "modules/ngx_http_geoip_module.so";
```

## 使用 Let's Encrypt 签发免费多域名证书

Let's Encrypt 提供完全面为免费，并且是自动化签发的证书，一张证书最多能签 100 个域名，暂不支持通配。 为了配置双证书，你首先应该签发下来两张证书，以下以 [acme.sh](https://github.com/Neilpang/acme.sh) 为例，首先先建立目录（以下所有案例均使用 `example.com` 作为例子，实际使用需自行替换）：

```
$ mkdir -p /etc/letsencrypt
$ mkdir -p /etc/letsencrypt/rsa
$ mkdir -p /etc/letsencrypt/ecdsa
```

然后修改 Nginx 配置文件，确保所有在监听 80 端口的都有 `location ^~ /.well-known/acme-challenge/` 区块，本配置文件是强制跳转 HTTPS 的案例，这是源站的配置：

```
server {
	listen 80 default_server;
	listen [::]:80 default_server;
	location ^~ /.well-known/acme-challenge/ {
		root /var/www/html;
	}
	location / {
		# Redirect all HTTP requests to HTTPS with a 301 Moved Permanently response.
		return 301 https://$host$request_uri;
	}
}
```

在签发之前，确保所有要签发的域名都指向了你自己的服务器！ 然后签发 RSA 证书（如果需要多域名证书，只需要多个 `-d` 即可，下同，不过保存的文件目录以及证书显示名称均为第一个域名）：

$ acme.sh --issue --reloadcmd "nginx -s reload" -w /var/www/html -d example.com --certhome /etc/letsencrypt/rsa

然后再签发 ECDSA 证书：

$ acme.sh --issue --reloadcmd "nginx -s reload" -w /var/www/html -d example.com -k ec-256 --certhome /etc/letsencrypt/ecdsa

卸载 `acme.sh` 自带的 cron，自己重新配置：

$ acme.sh --uninstallcronjob
$ vim /etc/cron.d/renew-letsencrypt

输入以下内容，注意替换 `acme.sh` 的路径为你安装的绝对路径：

```
15 02 * * * root /path/to/acme.sh --cron --certhome /etc/letsencrypt/rsa
20 02 * * * root /path/to/acme.sh --cron --ecc --certhome /etc/letsencrypt/ecdsa
```

然后就完成了，证书会自动续签。

### 给证书添加或删除域名

因为 Let's Encrypt 使用的不是通配符域名，所以会经常遇到有新的子域的情况，此时就需要给证书添加域名，一张证书最多可以添加 100 个域名。最简单的添加方法如下： 首先，修改证书的配置文件，两个证书的配置文件都要修改：

```
$ vim /etc/letsencrypt/rsa/example.com/example.com.conf
$ vim /etc/letsencrypt/ecdsa/example.com\_ecc/example.com.conf
```

找到 `Le_Alt` 一行，将新的域名添加进后面（每个域名用逗号隔开，总共不能超过 100 个）。然后开始重新签发这个证书，需要添加 `-f`。

```
$ acme.sh --renew -d example.com --certhome /etc/letsencrypt/rsa -f
$ acme.sh --renew -d example.com --ecc --certhome /etc/letsencrypt/ecdsa -f
```

要注意的一点是，目前 Let's Encrypt 签发的 ECC 证书的中间证书和根证书暂且不是 ECC 证书，这将会在以后支持，详情见 [Upcoming Features](https://letsencrypt.org/upcoming-features/#ecdsa-intermediates)。

## 配置 Nginx

首先需要生成几个 Key：

```
$ openssl rand 48 > /etc/nginx/ticket.key
$ openssl dhparam -out /etc/nginx/dhparam.pem 2048
```

然后添加以下内容进 Nginx，放在 http 或 server 区块下，虽然不支持 CHACHA20，但是添加进去也没影响。

```
##
# SSL Settings
##
ssl_certificate /etc/letsencrypt/rsa/example.com/fullchain.cer;
ssl_certificate_key /etc/letsencrypt/rsa/tlo.xyz/example.com.key;
ssl_certificate /etc/letsencrypt/ecdsa/example.com_ecc/fullchain.cer;
ssl_certificate_key /etc/letsencrypt/ecdsa/example.com_ecc/example.com.key;
ssl_session_timeout 1d;
ssl_session_cache shared:SSL:50m;
ssl_session_tickets off;
ssl_dhparam dhparam.pem;
ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
ssl_prefer_server_ciphers on;
ssl_ciphers 'ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA256:ECDHE-ECDSA-AES128-SHA:ECDHE-RSA-AES256-SHA384:ECDHE-RSA-AES128-SHA:ECDHE-ECDSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA:ECDHE-RSA-AES256-SHA:DHE-RSA-AES128-SHA256:DHE-RSA-AES128-SHA:DHE-RSA-AES256-SHA256:DHE-RSA-AES256-SHA:ECDHE-ECDSA-DES-CBC3-SHA:ECDHE-RSA-DES-CBC3-SHA:EDH-RSA-DES-CBC3-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384:AES128-SHA256:AES256-SHA256:AES128-SHA:AES256-SHA:DES-CBC3-SHA:!DSS';
ssl_stapling on;
ssl_stapling_verify on;
```

最后不要忘了 `nginx -s reload`，然后 [前往 SSL Labs](https://www.ssllabs.com/ssltest/index.html) 检查配置，可以看到旧的浏览器使用了 RSA 证书（我的服务器有独立 IP，所以无 SNI 支持的也能访问）：

![支持的客户端](https://cdn.ze3kr.com/6T-behmofKYLsxlrK0l_MQ/5d7521dc-aac5-42ad-4fa4-55df51692000/large)

至此，ECDSA/RSA 双证书配置完成，你可以在浏览器里查看到证书类型：

![ECDSA 证书](https://cdn.ze3kr.com/6T-behmofKYLsxlrK0l_MQ/e8b62e3d-921d-453e-1b56-20cd930fa500/large)
