---
title: 关于建站和服务选购的一些建议
tags:
  - VPS
  - 网站
  - 网络
id: '1752'
categories:
  - - 科技
date: 2017-01-07 07:00:18
---

建立网站、建立软件（或游戏）的服务器等，可能会有很多纠结的地方，也有很多坑，这篇文章根据我个人的经验来帮助大家选择最适合自己的方案，以及一些建议，在最后列出来一些我推荐的服务。

## 服务的三种类型

一般的，建站所需要购买的服务也是属于这三种服务之间的。从 SaaS、Paas 到 IaaS，依次从使用简便到复杂，从可拓展性低到高。了解这三种服务类型有助于选择合适的服务。
<!-- more -->

+ **SaaS，软件即服务**：比如 WordPress.com 等提供了一整套服务（包括了域名、主机、平台和软件）的，就算是 SaaS，这种服务对于初学者使用起来最方便，专业人士也能玩出不少花样，但可拓展性受到软件的限制，通常这种服务都有较高的可用性，并且是分布式的。
+ **PaaS，平台即服务**：比如基于 cPanel、Plesk 面板的虚拟主机，你可以在平台上安装已有的开源软件，或者将自己软件代码上传，总体使用几乎和 SaaS 一样简单，使用特定的操作系统和代码语言，安装各种程序。PaaS 有普通的也有分布式的，后者的在可用性和数据保障，以及可拓展性上都要好于前者。
+ **IaaS，基础设施即服务**：包括 VPS、独立服务器、云 VPS 和共有/私有云服务器。VPS 和独立服务器相比主要差距是在配置上，包括 CPU、内存、硬盘等，后者的配置会高很多，可用性方面没有太大区别。云 VPS 和共有/私有云实际上是基于云端分布式的 VPS 和独立服务器，一样的，他们的在可用性和数据保障，以及可拓展性上都要好于 VPS 和独立服务器。IaaS 需要自己选择操作系统，所有的服务和软件都是自己配置，通常都给你 root 权限。

## 服务的选择

+ **位置选择之国内**：首先需要考虑的是国内还是国外？如果选择国内，首先你得域名需要经过备案，这可能需要将近一个月时间，这无疑为建网站增加了不少成本。而且国内的服务器普遍较贵。不过如果要选择国内，我比较推荐的主机是新浪的 SAE，它价格很低，是按需付费，并且还有免费额度，它属于分布式的 PaaS，提供高可用率的服务，**支持 Docker 容器**、DDOS 防御。不过 SAE 对于非企业用户不提供 CDN 和自定义域名 HTTPS，我推荐你使用 UPYUN 来实现 CDN 和自定义域名 HTTPS 功能。国内的优点：可以保证网络延迟低，并能符合目前的一些法律政策。
+ **位置选择之国外**：不少亚洲的主机（包括但不限于香港、日本、新加坡）从大陆连接会 “绕道”，导致延迟很高，购买前需要先了解清楚，香港便宜的共享主机推荐 [TlOxygen](https://domain.tloxygen.com/web-hosting/index.php?promo=ze3kr)。亚洲的主机普遍网络带宽不大，流量比较少，如果不选择亚洲的，那就建议先选北美的。国外的主机通常会有比国内很多主机更低的价格，更好的国际带宽。 如果有条件，可以配置多个服务器，**两个服务器**推荐的解决方案是**亚洲 + 北美**东岸，并将亚洲和大洋洲都指向亚洲，其余的都指向北美。如果有条件配置更多服务器，那么可以同时配置**北美东岸 + 北美西岸 + 欧洲 + 亚洲**的四个服务器并分区解析，就我的实际测试而言，把非洲指向欧洲，把大洋洲指向北美西岸能达到最快的速度。 

**一些可能需要的特性**：基于要实现的不同功能，可能需要在以下方面有要求，在选择服务的时候需要优先考虑。

*   **高数据可靠性**：几乎所有的网站或是和网络相关的软件，都需要存储用户的数据，所以高数据可靠性是十分重要的。所以磁盘阵列为 RAID1 和 RAID10 是首选。而且用户数据的备份也是十分重要的，可以存储在第三方如 AWS S3。一些服务提供商提供了备份功能，其实也是能够保证高数据可靠性。
*   **防御攻击的能力**：攻击的成本已经越来越低，种类也很多。通常最难防御的攻击种类是 DDOS，其余的几乎都可以通过软件实现。建议在选择服务器时就要选择有 DDOS 防御的服务器，或者直接使用 CloudFlare 之类的基于第七层的防御。
*   **可伸缩，按需付费**：最简单的例子就是网络的计费方式不是按照带宽计费，而是按照每 GB 多少钱计费，不限带宽，能够避免不必要的支出的同时还会让用户享受最快的速度。同样的道理，按照系统资源的使用来计费的模式是最好的选择。一般的，基于云端的 PaaS 都是这种计费方式，IaaS 则需要自行配置来实现资源不够时升级配置，资源过剩时回收配置（也可以是 Auto Balancing），所以按小时（或分钟、秒种）级别的计费 IaaS 计费方式最适合这样伸缩以达到按需付费的目的。
*   **网络可靠性**：网络可靠性也十分重要，如果服务的网络不好，那么将极大的影响用户体验，导致用户不可以使用服务。
*   **SLA**：重要的服务都需要 SLA，SLA 能给你承诺服务的可用率，当服务没有满足承诺时，服务提供商会给你赔偿。
*   **IPv6**：就目前来看，如果不支持 IPv6 也没什么问题。但是能有原生的 IPv6 支持总比没有好，而且现在正在有越来越多的运营商支持 IPv6，使用 IPv6 有可能会有更低的网络延迟，同时配置 IPsec 也有优势。

## 其他

+ **域名相关**：不管你是选择使用虚拟主机、VPS 或是独立服务器，通常你还需要自己买一个域名，配置 DNS 解析等等，推荐免费的 DNS 解析有 CloudFlare、Rage4，国内还有 CloudXNS，一些虚拟主机商以及域名注册商也提供免费的解析，但不如前面介绍的那几个（[详细关于 DNS 的介绍请见此](https://guozeyu.com/2016/05/rage4-best-dns/)）。如果选择使用 CloudFlare，那么你还可以在 DNS 后台选择开启 Proxy（CDN），这样能为你的网站缓存并加速（但实际上对于国内来说有可能反而减速），并获得一些基础的 DDOS 防护，并隐藏你的源站 IP 地址。
+ **域名注册商的选择**：首先，要保证总的域名保有量大；其次，要域名后缀种类齐全；第三，最好能附加免费的企业邮箱和高级的隐私保护；第四，域名注册后要能删除并退款，防止手抖注册错了；第五，价格不能太贵。在我做的 [TlOxygen](https://domain.tloxygen.com/?promo=ze3kr) 这个公共域名注册服务上，使用的域名注册商是全球前十大域名注册商；有 500 多种域名后缀，数量超过 GoDaddy；赠送企业邮箱，可以配置无限个转发邮箱，甚至还可以配置子域名邮箱，还支持 DKIM；大多数种类域名删除可以拿到退款；价格比 Godaddy 便宜很多，续费价格没那么坑，基本上是 7 折；域名隐私保护只有 5 元，但功能比很多免费的高级：即使开了域名隐私保护，别人给你发邮件时，会收到表格，还能继续填写表格与你联系。

## 一些服务商的推荐

排列的顺序只是与添加在这个列表里的时间有关，新添加的会被放在后面。此处添加的服务商全是个人的推荐的，以后还会持续更新。 标注列表：

*   (6)：全面支持 IPv6
*   (6)：部分支持 IPv6
*   (D)：支持免费 DDOS 防御

### SaaS

#### 建立网站

*   [WordPress.com](https://wordpress.com)
*   [Tumblr](https://www.tumblr.coom) (6)
*   [GitHub Pages](https://pages.github.com)

#### CDN

以下 CDN 都支持 HTTPS 与 IPv6

*   [UPYUN/又拍云](https://www.upyun.com) (6)
*   [CloudFront](https://aws.amazon.com/cloudfront/) (6)
*   [CloudFlare](https://www.cloudflare.com) (6、D)

#### DNS

*   [Route 53](https://aws.amazon.com/route53/) (6)
*   [Rage4](https://rage4.com) (6、D)

#### 数据存储

*   [AWS S3](https://aws.amazon.com/s3/)

#### 代码托管

*   [GitHub](https://github.com)
*   [GitLab.com](https://gitlab.com)
*   [Coding](https://coding.net)

SaaS 还有很多，太多的东西都是 SaaS 了，所以这里只能算举几个例子。

### PaaS

#### 虚拟主机

*   [BlueHost](https://www.bluehost.com/shared)

#### 云主机

或者说是分布式的虚拟主机，但是有独立的 CPU 和内存资源。

*   [ResellerClub](https://www.resellerclub.com/cloud-hosting)
*   [BlueHost](https://cloud.bluehost.com/products/cloud-sites/)

#### 可自选软件语言的/Docker 容器

*   [新浪 SAE](https://sae.sina.com.cn) (6¹、D)
*   [OpenShift](https://www.openshift.com)
*   [AWS Elastic Beanstalk](https://aws.amazon.com/elasticbeanstalk/)
*   [AWS EC2 Container Registry](https://aws.amazon.com/ecr/)

### IaaS

#### VPS/Cloud

标注列表：

*   (U)：独享 CPU³
*   (C)：基于云端的技术，高可用性
*   (S)：关机后不计费

*   [Vultr](https://www.vultr.com/?ref=6886257) (6、D⁶)
*   [OVH](https://www.ovh.com) (6²、D、C⁴)
*   [Google Compute Engine](https://cloud.google.com/compute/) (D⁵、C、U、S)
*   [Linode](https://www.linode.com) (6)
*   [DigitalOcean](https://www.digitalocean.com) (6)
*   [AWS EC2](https://aws.amazon.com/ec2/) (S)
*   [QingCloud/青云](https://www.qingcloud.com) (S)
*   [腾讯云](https://www.qcloud.com)

### 备注

1.  SAE 的 IPv6 的激活需要额外的月付
2.  OVH 的 SSD VPS 暂时不支持 IPv6
3.  在购买某种配置时，若告知你是共享 CPU，那么还是共享 CPU
4.  OVH 只有 Cloud VPS 是基于云端的 VPS
5.  Google 官方并没有明确说明提供 DDOS 防御，但实际上是有能力防御的
6.  Vultr 的 DDOS 防御需要额外购买，且仅部分地区支持，最大防御仅 10Gbps
