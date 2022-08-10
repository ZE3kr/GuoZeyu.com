---
title: SSD U盘、Windows To Go 与 Mac
tags:
 - 硬件
id: '3776'
categories:
  - 科技
date: 2019-02-26 16:49:52
languages:
  en-US: https://ze3kr.com/2019/02/ssd-usb-and-wtg/
cover: <img src="https://cdn.tloxygen.com/6T-behmofKYLsxlrK0l_MQ/69a9a1ba-9671-4f8f-3fd7-50bed271db01/extra" alt="SSD 连接样式" width="3360" height="2240"/>
---

最近，我先后购买了 SSD U盘和 SSD 硬盘。我在我的 SSD U盘上安装了 Windows To Go，并在 Mac 设备上良好使用。本文将分享一下我的体验。
<!-- more -->

## SSD U盘/移动硬盘

### SSD VS HDD

TL;DR：固态硬盘（SSD）的性能比机械硬盘（HDD）高 10 倍左右，体积与重量也明显小于 HDD，但单位容量的价格也高了近 10 倍。

固态硬盘（SSD）已经成为了一个比较流行的概念。通常，固态硬盘的各个性能要好于传统机械硬盘（HDD），包括但不限于读写速度、IOPS 等指标。一个普通的机械硬盘的读写速度大概在 100Mbyte/s 左右，而固态硬盘则能达到 1000Mbyte/s 以上，速度比移动硬盘快了近 10 倍。SSD 的 IOPS 性能相比 HDD 可以提升近 100 倍。这些性能提升体现在各个方面：拷贝文件、打开存储在硬盘上的文档和软件软件、启动系统等，最高能有十几倍的速度提升（如果瓶颈是在硬盘读写的话）。当然 SSD 的价格也能比 HDD 高近 10 倍。此外，HDD 相比 SSD 噪音要大很多，SSD 基本是无声的。

市面上销售的移动硬盘多数都是低速的 HDD，因为普通消费者最常关心的是容量和价格，HDD 的每 GB 价格更低，SSD 则贵很多，所以 SSD 的销量也相对的低很多。花 300 块可以买到一个 1TB 的 HDD，而 1TB 的 SSD 可能需要 3000 块。

**我是否需要 SSD？**要看是否有高性能需求。比如，**操作系统和软件就适合安装在 SSD 上**，这样启动时间会大大缩短。一些文档、图片和视频等数据则可能不是那么需要 SSD。如果你是图像/视频工作者，需要存储高画质的图像/视频，那么 SSD 就会很有用，它可以大大缩短文件的加载时间；当然很多软件也为低性能设备做了优化，比如 Lightroom Classic CC 可以生成预览（Preview）以减少文件大小；Final Cut Pro 和 Premiere CC 可以为原始素材生成代理（Proxy）以减少文件大小，你可以将预览或代理存储在 SSD 上，原始文件存储在 HDD 上以节省成本。此外，一些访问频率很低的数据也可以存储在 HDD 上以节省成本，如数据备份、监控视频、日志等。

<img src="https://cdn.tloxygen.com/6T-behmofKYLsxlrK0l_MQ/93412ae0-8a11-4eb6-9436-e0cb9fef4201/extra" alt="mSATA mini 型的 SSD（来自 amazon.com）3.0cm * 2.7cm * 0.4cm" width="620" height="558"/>

此外，SSD 能有更轻的重量，更小的体积，以至于 SSD 可以做成 U 盘大小。而 HDD 就会大很多。以现在常用的最小的 2.5英寸 HDD 为例，其长度为 10cm，宽度为 7cm，这还不包含外壳部分。上图所示的 SSD 的长宽高仅有 3.0cm \* 2.7cm \* 0.4cm，重量也不到 40g。其长度不到 HDD 的三分之一。

SSD 也可以做到很大的容量，比如 15英寸 MacBook Pro 甚至可以选择高达 4TB 容量的 SSD。但由于 SSD 的价格过于昂贵，导致大容量的 SSD 并不流行。

### SSD U盘与移动硬盘

SSD 外设有着如此之高的读写速度，以至于只有使用 5Gbps 的 USB3.0 和 USB3.1 Gen1，或者 10Gbps USB3.1 Gen2 才能体现出它的性能，这些接口的速度对于 SATA SSD 已经够了。然而，若要发挥出其全部性能，则需要使用 20Gbps Thunderbolt 2 或者 40Gbps Thunderbolt 3 接口，因为多数的 NVMe SSD 读写性能大于 10Gbps 而不超过 20Gbps。使用 480Mbps USB2.0 的 SSD 几乎是无意义的，所以你也很难找到这种产品。不同接口的 SSD 外设价格也区分的很明确，使用的接口越高端就越贵。使用 Thunderbolt 接口的移动 SSD 价格会比使用 USB 接口的贵 2~3 倍（速度快 2~4 倍）。

<img src="https://cdn.tloxygen.com/6T-behmofKYLsxlrK0l_MQ/622b1ba8-5858-4ac1-de22-4d8e721bf701/extra" alt="接口协议对比图（来自 thunderbolttechnology.net）" width="3915" height="3000"/>

Thunderbolt3 接口的外形与 USB Type-C 外形完全一样，有些移动硬盘使用了 Type-C 接口，但不代表它就是 Thunderbolt3。你可能需要通过观察其是否有 Thunderbolt 的 ⚡️标志来判断是否支持 Thunderbolt。此外，使用 Thunderbolt3 接口的移动硬盘未必支持 USB 协议，例如我购买的 HP P800 移动硬盘，就不支持 USB 协议，且不能转接成 USB，同时也不能转接成 Thunderbolt2，所以其兼容性就大打折扣。而 USB3.x 的设备通常兼容 USB2.0，且 USB Type-A 与 Type-C 也能相互转接，所以 USB 设备有着更好的兼容性。

**SSD 移动硬盘**类似与普通的移动硬盘（基于 HDD），但有着更高的性能。通常，SSD 移动硬盘的体积也会更小。

**SSD U盘**相当于更小的 SSD 移动硬盘，其体积可能和普通的 U 盘相近。由于体积的减小，SSD U盘读写性能与散热性能通常不如 SSD 移动硬盘。且更少有支持 Thunderbolt 的 U 盘。

<img src="https://cdn.tloxygen.com/6T-behmofKYLsxlrK0l_MQ/5204750b-f04c-4074-68ff-bec393072301/extra" alt="从左到右依次为 iPhone XS、HP P800 Thunderbolt3 SSD、CHIPFANCIER USB3.1 Gen2 Type-C SSD" width="3360" height="2240"/>

|  | HP P800 SSD | CHIPFANCIER SSD |
| ----- | ----- | ----- |
| 接口 | Thunderbolt3 | USB3.1 Gen2 Type-C |
| 类型 | 移动硬盘 | U 盘 |
| 大小（毫米） | 141x72x19 | 72x18x9 |
| 读取速度 | 2400Mbyte/s |
| 约 500Mbyte/s |
| 写入速度 | 1200Mbyte/s | 约 450Mbyte/s |
| 容量 | 256GB/512GB/1TB/2TB |
| （2TB 版本尚未发布） | 128GB/256GB/512GB/1TB |

<img src="https://cdn.tloxygen.com/6T-behmofKYLsxlrK0l_MQ/69a9a1ba-9671-4f8f-3fd7-50bed271db01/extra" alt="在 MacBook Pro 上，即使一个接口安装上了 CHIPFANCIER 的 SSD U盘，其旁边的接口仍然有空间连接其他 Type-C 设备。" width="3360" height="2240"/>

## 磁盘格式的选择

通常硬盘需要格式化后再使用。在格式化硬盘时，你就需要选择磁盘格式。在这里比较一下四种格式：APFS、NTFS、exFAT 和 FAT32。（某些软件和文档只能安装或存储到特定的磁盘格式中，就不再表里列举了）

|  | APFS | NTFS (v3.1) | exFAT | FAT32 |
| ----- | ----- | ----- | ----- | ----- |
| 发布年份 | 2017 | 2001 | 2006 | 1977 |
| 适用介质 | 建议 SSD |
| 不限 | 不限 | 不限 |
| Windows 兼容 | – | 支持 | 7+**** |
| 支持 |
| macOS 兼容 | 10.12+ |
| (Sierra) | 支持* | 支持 | 支持 |
| Linux 兼容 | – | 支持* | – | 支持 |
| 移动兼容 | – | – | 支持 | 支持 |
| 快照 | Snapshots | Shadow Copy** | – | – |
| 加密 | FileVault | BitLocker** | – | – |
| Copy-on-Write | 支持 | – | – | – |
| 空间共享 | 支持 | – | – | – |
| 最大文件大小*** | ∞ | ∞ | ∞ | 4GB |
| 最大分区大小*** | ∞ | ∞ | ∞ | 32GB |

*   \* macOS 和 Linux 默认只能读取 NTFS 磁盘数据，而不能写入。Mac 可以通过调整系统设置实现读写，Mac 与 Linux 也均通过第三方软件实现 NTFS 的读写。
*   \*\* Shadow Copy 和 BitLocker 通常只支持 Windows
*   \*\*\* 这里使用字节数而非比特数。大于 1000TB 的这里就当作 ∞。**FAT32 在部分操作系统中可以有更大的分区大小（如 2TB）。**
*   \*\*\*\* 尽管原始版本的 Windows XP 和 WIndows Vista，但 Windows XP 在安装 KB955704 后，Windows Vista 在安装 SP1 或 SP2 更新后，也可部在某种程度上支持 exFAT。

**关于操作系统兼容**，若该操作系统绝大多数主流版本都支持，则算作 “支持”。_移动兼容_只要同时兼容 iOS 和安卓就算 “支持”。

**关于 iOS**，目前 iOS 系统仅支持导入外置存储的图片和视频，且外置存储的图片和视频必须按照相机文件结构命名；尽管 iOS 内置存储使用的是 APFS，但还不支持基于 APFS 的外部存储。

磁盘格式对硬盘性能影响不大，主要看其兼容性与功能。但兼容性好的功能少，功能多的兼容性差。

## 进阶: Windows To Go

通过使用 SSD 外设，可以在保证较高的性能的同时扩展电脑的储存。然而，你甚至可以把电脑操作系统安装在 U 盘上，这样你不用分区也能使用目标操作系统开机。如果你将操作系统安装到 HDD 移动硬盘上，其启动速度就会很慢；如果安装到普通 U盘上，其启动速度几乎是不能忍受的。因此**建议将操作系统安装到 SSD 外设上**。

macOS、Windows 和 Linux 都可以被安装在 U盘或者移动硬盘上。但 Windows 对各种硬件兼容的最好，如果你需要在 PC 机上使用，那么只推荐 Windows 系统。Windows 10 企业版支持 Windows To Go 功能，专门为安装在 U 盘或移动硬盘上时进行了优化。

要安装 Windows To Go（WTG） 需要有 Windows 环境，可以是安装 Windows 的 PC、使用 BootCamp 的 Mac，或者是运行有 Windows 的虚拟机。如果你恰好使用 Windows 10 企业版，那么你可以使用系统控制面板中的 WTG 工具（然而我用系统自带的工具安装失败了）。否则你只能使用第三方工具安装 WTG。这里推荐[这个 WTG 辅助工具](https://bbs.luobotou.org/thread-761-1-1.html)。

安装成功后，你可以在电脑启动时选择从 U 盘启动，或者在虚拟机里从 U 盘启动。这里展示一下在 Mac 上启动 WTG 以及 macOS 上的 VMware Fusion 中启动 WTG。

<figure class="my-video">
  <div style="position: relative; padding-top: 56.25%;"><iframe src="https://iframe.cloudflarestream.com/d99af30ada995905d245761ca8ea3fd9?muted=true&preload=metadata" style="border: none; position: absolute; top: 0; left: 0; height: 100%; width: 100%;"  allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen="true"></iframe></div>
  <figcaption>MacBook Pro 开机启动到 Windows To Go。</figcaption>
</figure>

以上视频是在 Mac 上开机直接进入 WTG。如果你的 Mac 配备了 T2 安全芯片，那么你需要[关闭启动安全性检查](https://support.apple.com/zh-cn/HT208330)（无安全性、允许从外部介质启动）才能够从外部设备启动 Windows。你可以考虑开启固件密码以继续保证安全性（没有 T2 芯片的 Mac 也可以开启固件密码）。提示：请牢记固件密码，固件密码一旦忘记只能返厂修。

刚安装完的 Windows 可能没有 Mac 的鼠标和触摸板的驱动。你需要通过 USB 鼠标和触摸板来完成安装工作（Magic Keyboard 2 和 Magic Trackpad 2 连线后也可作为 USB 外设）。

你需要为 Windows 安装 Mac 的驱动。在 Mac 的 “启动转换”（Boot Camp）软件中的 “操作” 菜单里可以手动 “下载 Windows 支持软件”（驱动）。注意，不同型号的 Mac 的驱动不同，建议使用系统的软件下载。如果你需要在多个 Mac 上使用一个 WTG，你需要安装多个驱动。启动转换助理是帮助用户在 Mac 本地硬盘上安装 Windows 双系统的，**不能**用来安装 Windows To Go，这里**只是用它安装驱动**。

<img src="https://cdn.tloxygen.com/6T-behmofKYLsxlrK0l_MQ/b3992268-7239-40c0-0174-7d54aff08201/extra" alt="BootCamp 下载 Windows 支持软件截图" width="1570" height="1144"/>

**建议将驱动保存在另一个 Windows 可以识别的 U盘上**（见本文的 “磁盘格式的选择”）。然后再次开机进入 WTG 后就能安装了。

<figure class="my-video">
  <div style="position: relative; padding-top: 56.25%;"><iframe src="https://iframe.cloudflarestream.com/aaf2fac6d0f2848c78c39c6e633b9af7?muted=true&preload=metadata" style="border: none; position: absolute; top: 0; left: 0; height: 100%; width: 100%;"  allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen="true"></iframe></div>
  <figcaption>macOS 使用 VMware Fusion 进入 WTG。</figcaption>
</figure>

如果你有虚拟机，你也可以在虚拟机里从 U 盘启动。

### BitLocker

如果不启用 BitLocker，那么 U盘上所有用户文档都是明文存储的，也就是说只要有了你的 U盘，就能读取所有的用户数据。若启用了 BitLocker，那么磁盘数据安全性就能得到保证。然而 Mac 没有 BitLocker 的相关硬件支持，默认无法启动 WTG 中的 BitLocker，需要修改 Windows 下的配置文件。

启用 BitLocker 后会些许增加系统的 CPU 占用，略微降低磁盘性能。此外，启动了 BitLocker 的磁盘 macOS 无法识别，意味着在 macOS 系统上无法读取开启了 BitLocker 的 Windows 分区。建议谨慎开启。

### 使用体验

将 Windows 安装到 U盘不但节省了电脑内的磁盘空间，还有了一个可以随身携带的操作系统，可以在 Mac 和 PC 机上使用。我没有开启 BitLocker。我在 macOS 上开启了 NTFS 读写，所以其 Windows 分区还可以被当作 U盘共享数据使用。

我在 WTG 上安装了一些常用软件，在其他 PC 机上未必需要进入 WTG 系统以使用这些软件，而直接进入 WTG 根目录下 Program Files 文件夹里访问这些软件就行了。

我将 Windows 安装到了 CHIPFANCIER 出的 SSD U盘上，其特色是使用了 USB3.1 Gen2 Type-C 接口，这样不需要转接就能够在新 Mac 上使用。然后我又购买了绿联的 Type-C 转 Type-A 的转接头（USB 3.0），可以将此 U 盘转接到 Type-A 设备上。

<img src="https://cdn.tloxygen.com/6T-behmofKYLsxlrK0l_MQ/9a968ae3-8c52-46aa-1384-db54746d4e01/extra" alt="绿联 Type-C 转 Type-A" width="3360" height="2240"/>

<img src="https://cdn.tloxygen.com/6T-behmofKYLsxlrK0l_MQ/4ba97fd8-2ffe-49e5-e6fa-e7871e765001/extra" alt="绿联 Type-C 转 Type-A 连接后" width="3360" height="2240"/>
