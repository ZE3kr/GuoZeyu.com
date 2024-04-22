---
title: 低价冷数据存储方案 - Lightsail + S3 + Nextcloud 实现 $1/TB/月
date: 2023-04-05 00:30:00
tags: 
 - 备份
 - 云服务
categories:
 - 开发
---

如果你有大量的数据——大于 2TB 的数据，需要非常安全可靠的存储，同时这些文件的访问频率低于一年两次，那么最佳的选择是什么呢？本文将介绍我结合 AWS Lightsail，使用 S3 Glacier Deep Archive 存储的方案，实现了最低 $0.99/TB/月的低价云存储。

## 市面云存储价格对比

> TL;DR 考虑到成本和最大容量问题，我使用 iCloud 作为小数据的 “热备份”，使用 S3 作为大文件的 “冷备份”

在进入正题前，我们来对比一下目前市面上的几个存储方案，这里只对比面向个人的存储方案；你也可以直接[跳到 Nextcloud 章节](#Nextcloud)。Google Workspace、Apple Business 等面向企业的存储方案往往溢价更高，这里不再做对比。这里所展示的价格为美国区不含税的价格。一些服务会对其他国家有价格歧视 (低价区)，这不是本文所讨论的范围。

<table>
   <thead>
      <tr>
         <th>提供商</th>
         <th>容量</th>
         <th>价格 (美元)</th>
         <th>单价 (美元/TB/月)</th>
      </tr>
   </thead>
   <tbody>
      <tr>
         <td rowspan="5">iCloud</td>
         <td>50GB</td>
         <td>0.99</td>
         <td>19.80</td>
      </tr>
      <tr>
         <td>200GB</td>
         <td>2.99</td>
         <td>14.95</td>
      </tr>
      <tr>
         <td>2TB</td>
         <td>9.99</td>
         <td>5.00</td>
      </tr>
      <tr>
         <td>6TB</td>
         <td>29.99</td>
         <td>5.00</td>
      </tr>
      <tr>
         <td>12TB</td>
         <td>59.99</td>
         <td>5.00</td>
      </tr>
      <tr>
         <td rowspan="3">Google Drive <sup>1</sup></td>
         <td>100G</td>
         <td>1.99</td>
         <td>19.90</td>
      </tr>
      <tr>
         <td>200G</td>
         <td>2.99</td>
         <td>14.95</td>
      </tr>
      <tr>
         <td>2TB</td>
         <td>9.99</td>
         <td>5.00</td>
      </tr>
      <tr>
         <td rowspan="3">OneDrive <sup>1</sup></td>
         <td>100GB</td>
         <td>1.99</td>
         <td>19.90</td>
      </tr>
      <tr>
         <td>1TB</td>
         <td>6.99</td>
         <td>6.99</td>
      </tr>
      <tr>
         <td>6TB <sup>2</sup></td>
         <td>9.99</td>
         <td>1.67</td>
      </tr>
      <tr>
         <td rowspan="2">Dropbox</td>
         <td>2TB</td>
         <td>11.99</td>
         <td>6.00</td>
      </tr>
      <tr>
         <td>3TB</td>
         <td>19.99</td>
         <td>6.66</td>
      </tr>
      <tr>
         <td rowspan="5">Mega</td>
         <td>400GB <sup>3</sup></td>
         <td>5.41</td>
         <td>13.53</td>
      </tr>
      <tr>
         <td>2TB <sup>3</sup></td>
         <td>10.84</td>
         <td>5.42</td>
      </tr>
      <tr>
         <td>8TB <sup>3</sup></td>
         <td>21.68</td>
         <td>2.71</td>
      </tr>
      <tr>
         <td>16TB <sup>3</sup></td>
         <td>32.53</td>
         <td>2.03</td>
      </tr>
      <tr>
         <td>Pay-as-you-go <sup>4</sup></td>
         <td>16.27/3TB 起</td>
         <td>2.71</td>
      </tr>
      <tr>
         <td rowspan="4">Adobe Creative Cloud <sup>4</sup></td>
         <td>2TB</td>
         <td>9.99</td>
         <td>5.00</td>
      </tr>
      <tr>
         <td>4TB</td>
         <td>19.99</td>
         <td>5.00</td>
      </tr>
      <tr>
         <td>10TB</td>
         <td>49.99</td>
         <td>5.00</td>
      </tr>
      <tr>
         <td>20TB</td>
         <td>99.99</td>
         <td>5.00</td>
      </tr>
   </tbody>
</table>

> 1. 使用年费方案，Google Drive 和 OneDrive 会更便宜一些，详情参见官网。
> 2. OneDrive 并不提供 6TB 的个人套餐。该套餐为家庭套餐，可供 6 个账户使用，每个账户提供 1TB 存储空间。
> 3. Mega 的流量是有所限制的，详情参见官网
> 4. Adobe 需要配合其他订阅 (至少 $9.99/月) 才可以购买空间

### 价格分析

苹果和谷歌都提供了 $9.99/2TB/月 的存储方案，2TB 对于绝大多数人已经够用，甚至还有点多。苹果和谷歌的更高级的套餐可以与包含自己在内的 6 个人进行共享。微软提供了 $6.99/1TB/月的存储方案，该方案还包含了 Office 套件。相比之下，微软的家庭版套餐提供更高的总容量，但每人限制 1TB 而非 6TB 共享，使得其不是那么的灵活。

根据自己的使用习惯，苹果、谷歌和微软可能也没有多少选择。如果全家都是苹果全家桶用户，那么就直接苹果的 2TB 家人共享就很合适。如果是 Windows 用户，同时是 Office 使用者，那微软的方案是最合适的。如果是 Google Photos 的用户，或者经常使用 Google Workspace 来写文档，那么选择谷歌是更好的。

此外，苹果最新的系统中可以开启 iCloud 端到端加密，开启后所有文件、照片都是端到端加密的。而上述其他的服务商则可能不提供端到端加密，或者是只有部分文件夹是端到端加密。

Dropbox 的跨平台做的不错，我用过它的 Mac 和 Windows 客户端，都非常高效好用。我曾经在 Mac 上使用微软的 OneDrive，体验就极其糟糕。

Google Workspace、Dropbox Teams 则提供了所谓 “无限容量” 的套餐，但所谓无限容量，其实是价格不透明。当使用量足够大的时候，不排除被限制的情况。因此本文只对比有明确标明容量和价格的套餐。

如果需要更大的套餐 (>2TB)，那么可以将目光转向 Mega。Mega 提供单位价格更低的网盘服务，同时支持完整的端到端加密。

值得一提的是，Mega 最近也推出了 Pro Flexi 套餐，起价 €15.00/3TB/月，在此基础上 €2.50/TB（流量也是 €2.50/TB），最高可以用到 10,000TB。

如果你使用 Lightroom CC 管理照片，且你的照片是存储大户；或者使用 Premiere Rush 剪辑视频，并且视频是存储大户，那可以选择 Adobe Creative Cloud 的存储空间，最高可以存储 20TB。

### 我的选择

如果 2TB 对你而言足够，且在可预见的未来也不会很快超过 2TB 的存储需求，那么直接选苹果、谷歌、微软、Dropbox 中的一个就行了。如果有大量的 RAW 照片需要存储，那么 Adobe Lightroom CC 则是不错的选择，$9.99/月的 Lightroom 就包含了 1TB 存储空间，加到 $19.99/月就可以有 3TB 存储空间，能随时随地访问到整个图库还是很方便的。

我就有在用苹果的 2TB 套餐，用 iCloud 同步一些小，但经常访问的文件还是很方便的。我也用 iCloud Photos 来同步 iPhone 拍摄的照片和视频，以及导出的全尺寸的 JPEG 和剪辑后的视频。此外，我还用 iCloud 对我的 iOS 设备进行备份。**iCloud 是我的 “热备份”**。

此时我还需要备份相机录制的 MP4、RAW 视频和 RAW 图片，这些文件有数 TB，并且每年还会以几百 GB 的速度增长。这些文件通常不需要随时访问，此时就需要 “冷备份” 了。我体验过一年 1TB 的 Lightroom CC，1TB 的容量对我而言虽然够用，但我的存储量还是会持续增长，不想将来不停的升级到越来越贵的套餐。因此最近又换成了同样价格的 Photography Plan，少了 1TB 的云存储，但多了 Photoshop。

我没有怎么体验过 Mega，但 Mega 确实也曾在我的考虑范围内。最终我没有选择 Mega 的原因是因为它不够便宜。的确，$2.03/TB/月的 16TB 套餐是很便宜了，但如果你只需要用 9TB 呢？此时还是得订阅 16TB 的套餐。而且哪怕用到 16TB，它还是比 S3 Glacier Deep Archive 贵了一倍多。

因此我选择了**部署在 Lightsail 上的 Nextcloud 作为冷备份**的系统，S3 作为存储后端。同时通过 S3 桶生命周期，实现智能调整文件的存储级别，实现了最低 $0.99/TB/月的云存储。Nextcloud 可以选择开启试验性功能 “Virtual File Support”，可以有选择性的同步文件。（Mac 上需要修改 `~/Library/Preferences/Nextcloud/nextcloud.cfg`，在 `[General]` 下增加 `showExperimentalOptions=true`）

我使用的图像编辑软件是 Adobe Lightroom Classic，我的所有 RAW 照片都是用 Lightroom 管理，原图存储在 Nextcloud 同步的文件夹中。我使用 Lightroom 对所有图像生成了 Smart Preview。在本地空间不足，Nextcloud 清除了老文件时，仍可以使用 Smart Preview 预览和编辑图片，并导出小尺寸的图片。Lightroom Classic 也可以通过 Adobe Creative Cloud，将 Smart Preview 同步到支持移动端的 Lightroom CC 上。这部分的 Smart Preview 是不占用 Adobe 的云空间的。Lightroom Catalog 我存放在本地的默认位置，但其备份存储在 iCloud。

视频剪辑软件我使用的是 Final Cut Pro，我的相机产生的所有视频都使用 Final Cut Pro 管理。Final Cut Pro 可以配置资料库中媒体的存放位置，我将其设置为 Nextcloud 的文件夹。这也是主要占空间的文件。Final Cut Pro 的资料库我存放在本地，其备份存储在 iCloud。

## 为什么不用 NAS？

前年的时候，我在[Mac mini 有什么用？组建家庭服务器！](/2021/11/mac-mini/) 里提到我使用 Mac mini 作为 NAS 使用。但当时我只是用它来进行备份，而非云存储。如果用自建 NAS 做云存储的话，出门在外访问则很成问题：最主要的就是受到 NAS 所在网络的上传速度限制，从而体验很差。在我的印象里，哪怕是成熟的 NAS 解决方案，也没有一个 NAS 能提供像 Dropbox 这样好用的全平台客户端。此外，单个的 NAS 并不可靠，因为它没有在物理上跨区域。NAS 中的硬盘也有购置成本，硬盘老化后也有更换成本，自己维护起来还有时间成本，最终未必划算。

如今，我把我的所有文件都放到了云存储，不再需要备份，因此也不再需要用 NAS 再备份一遍云存储中的东西了，只用来做 Mac 的 Time Machine。

## Nextcloud

Nextcloud 是一整套自建云存储的开源解决方案，从服务端到客户端均有，并仍在持续更新中。跟它极为相近的还有 ownCloud 也是可选的方案。本文将简单介绍我选择的 Nextcloud。不过既然使用 S3 了，为什么还需要服务端？为什么不在本地直接使用 S3 备份工具/挂载工具？因为我是想自建一个类似 Dropbox 的云存储，可多个设备本地实时的同步，没有额外服务端是实现不了或者很难实现的。

为 Nextcloud 选择一个服务器尤为重要。这里我选择 Lightsail 的原因是，Lightsail 可以与 S3 在同一个区域 (Region)，之间的延迟很低，并且 Lightsail 与同可用区的 S3 之间的流量是免费的。此外，Lightsail 的很便宜的套餐也有很多的流量可以使用。

我在[云服务推荐及选择指南](/2022/08/choose-a-web-service/)这个文章中也列举了一些其他服务商，他们也可以作为替代 Lightsail 的选择。虽然他们并没有连接 S3 的优势，但一些服务商有提供自己的对象存储解决方案（比如 BuyVM 提供 Block Storage Slab，Google Cloud 提供 Cloud Storage 等）。

### 对象存储对比

除了亚马逊的 S3 外，微软 Azure、谷歌云也提供与 S3 类似的对象存储。微软 Azure 的 Archive 与 S3 Glacier Deep Archive 在功能和价格上都十分相似，在此不再额外介绍 Azure 的对象存储。谷歌云则有些不一样，我在这里对比一下价格。这里以最便宜的区域为例：

| 冷存储 | S3 Glacier | S3 Glacier | Google | Google |
| --- | --- | --- | --- | --- |
| 级别名称 | Deep Archive | Instant Retrieval | Archive | Coldline | 
| 单价 (美元/TB/月) | 0.99 | 4.00 | 1.20 | 4.00 |
| 最短存储周期（天） | 180 | 90 | 365 | 90 |
| 立即取回价格（美元/TB） | 不支持 | 20 | 50 | 20 |
| 12 小时取回价格（美元/TB） | 20 | 不支持 | 不支持 | 不支持 |
| 48 小时取回价格（美元/TB） | 2.5 | 不支持 | 不支持 | 不支持 |
| 流量传输到公网费用（美元/TB） | 90 | 90 | 120<sup>1</sup> | 120<sup>1</sup> |

> 1. 传输到中国大陆地区为 $230/TB，传输到澳洲为 $190/TB。下面计算费用是以其他地区的 $120/TB 计算的。

假设需要存储 10 TB 的文件 10 年，S3 Glacier Deep Archive 的总费用为 $0.99 x 12 x 10 = $118.8；Google Archive 的总费用为 $1.20 x 12 x 10 = $144；S3 Glacier Instant Retrieval 或 Google Coldline 的总费用为 $4.00 x 12 x 10 = $480。

+ 需要**立即**下载一个 1TB 的文件在我的电脑上，S3 Glacier Deep Archive 是不支持的，S3 Glacier Instant Retrieval 需要 $20 + $90 = $110，Google Coldline 需要 $20 + $120 = $140，Google Archive 需要 $50 + $120 = $170。
+ 如果可以接受在 **12 小时**后下载一个 1TB 的文件在我的电脑上，S3 需要 $20 + $90 = $110。对于不需要立即访问的数据，Deep Archive 之外的没有优惠。
+ 如果可以接受在 **48 小时**后下载一个 1TB 的文件在我的电脑上，S3 需要 $2.5 + $90 = $92.5。

所以，对于不需要立即访问的文件，S3  Glacier Deep Archive有着绝对价格优势。但如果有立即访问的需求，S3 的 Glacier Deep Archive 是不支持的，Glacier Instant Retrieval 存储费用相比 Google 更贵，但取回费用相比 Google 更便宜。

由于 S3 的 Glacier Deep Archive 更加便宜，因此我最终选择了 S3。

### Lightsail 与 Nextcloud 服务器配置

Lightsail 我选择了 $5/月的套餐，这个套餐是最具性价比的，包含了 2TB 的流量，同时 CPU 基线也来到了 10%。我这几天实际体验下来，Nextcloud 在不间断上传文件时 CPU 占用在 15-20% 左右，其他时候保持同步占用仅为 1%。也就是说每天可以有 12 小时的上传文件。

Nextcloud 的安装[参考 Nextcloud 官网](https://docs.nextcloud.com/server/latest/admin_manual/installation/index.html)即可。我使用的是 Ubuntu 22.04，[官网上给出了这个发行版的安装指南](https://docs.nextcloud.com/server/latest/admin_manual/installation/example_ubuntu.html)。安装完成后，可以参考 [Server tuning](https://docs.nextcloud.com/server/latest/admin_manual/installation/server_tuning.html) 进行优化。我配置了 Redis 和 APCu，开启了 JIT 等，减少 CPU 负载。

Nextcloud 可以选择将 S3 作为 Primary Storage。我不推荐这种做法，因为 S3 作为 Primary Storage 时，S3 存储桶里只保存文件本身，不包含文件的元数据 (包括文件名、目录，因为此时元数据只存在于数据库中)，也就是说这时的 S3 的桶是不能直接访问的，只能通过 Nextcloud 访问文件。此外，将 S3 作为 Primary Storage 还会导致性能下降，因为 Nextcloud 会将分段上传的临时文件和 App data 也存在 S3 中，分段上传大文件 CPU 占用极大，且经常出现超时 ([已经提优化 issue](https://github.com/nextcloud/desktop/issues/5554)，欢迎点赞支持)。

Nextcloud 还提供 External Storage 功能，安装完毕后，通过 External Storage 挂载的 S3，其文件结构是保留的，也就是说此时 S3 的存储桶是可以直接访问的。我觉得这个还是很重要的，省去了我备份 Nextcloud 数据库的烦恼。此时就算 Nextcloud 整个实例挂了，我的所有文件都可以在 S3 存储桶里访问。如果将 External Storage 的挂载点配置为 `/`，那么根目录下所有文件和文件夹都是和 S3 同步的，十分方便。

<img src="https://cdn.tlo.xyz/6T-behmofKYLsxlrK0l_MQ/d65db469-0f31-473d-e915-cfff92a32001/extra" alt="Nextcloud External Storage 挂载配置" width="2842" height="996"/>

此外，我会关闭 Nextcloud 的 Deleted Files 功能，并通过 [S3 Versioning](https://apps.nextcloud.com/apps/files_versions_s3) 实现版本管理。因为 Deleted Files 开启后，删除文件时，Nextcloud 会将 External Storage 中的文件移动到本地，这样会费时、费钱且占用本地空间。

### 本地 SSD 空间不够

尽管已经通过 External Storage 将所有文件存储在 S3，但 Nextcloud 仍会将分段上传的视频存储在本地的 `/var/www/nextcloud/data` 中，此外 PHP 也会将上传的文件临时存储在 `/tmp`，除非另行配置。因此，当上传的文件很大时 (比如同时上传多个十几 G 的文件)，会超出 Lightsail 本地 SSD 的限制，导致本地 SSD 空间不够。

升级 Lightsail 的配置，或者在 Lightsail 上挂载 SSD 都是增加固定的存储容量，这并不划算。因此，推荐使用 EFS，将网络存储挂载到本机，修改 Nextcloud 的 data 目录到挂载点。参考 AWS 指南 [How to Use Amazon EFS with Amazon Lightsail](https://aws.amazon.com/getting-started/hands-on/efs-and-lightsail/)。EFS 是根据使用量按量计费的。只有存在尚未完成的分段上传时，Nextcloud 的 data 目录才会有大文件在，因此 EFS 按量计费十分划算。

EFS 使用 Burst、One Zone 模式即可，这也是最便宜的方式。实测 EFS 的性能比 `s3fs` 挂载 S3 好很多。

### S3 生命周期

更多内容待补充，如有问题欢迎在下方评论
