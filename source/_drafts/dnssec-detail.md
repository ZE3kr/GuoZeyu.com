---
title: DNSSEC 的具体实现
tags:
  - DNS
  - 安全
id: '3078'
categories:
  - - 开发
---

[上一篇文章](https://guozeyu.com/2018/03/dnssec/)中介绍了 DNSSEC 的基本原理，这一篇文章中将会介绍给你 DNSSEC 的具体实现方法，我来使用 dig 程序为大家分析 DNSSEC 的实现过程

## 根域名

我有一个域名 `tlo.xyz` 长期部署了 DNSSEC，所以本文就拿这个域名作为例子讲解。首先，需要明确的是如何让 `dig` 程序去显示关于 DNSSEC 的资源类型，幸运的是这很简单，只需要加上 `+dnssec` 参数即可。 在[之前的文章](https://guozeyu.com/2016/12/domain-name-system/)中，我们已经知道了根域名公开了 13 个服务器的 IP 地址。此外，其实根域名还公开了一组 DS 记录，这段记录可以[在这里获得](https://data.iana.org/root-anchors/root-anchors.xml)。

.172800INDS19036 8 2 49AAC11D7B6F6446702E54A1607371607A1A41855200FD2CE1CDDE32F24E8FB5
.172800INDS20326 8 2 E06D44B80B8F1D39A95C0B0D7C65D08458E880409BBC683457104237C7F8EC8D

*   **DS 记录**：DNSSEC Delegation Signer，用于鉴定 DNSSEC 已授权_区域的签名密钥（Zone-signing-key）_，相当于**公钥的哈希值**。

第二条记录是 [2016 年 10 月](https://www.internetsociety.org/blog/2016/10/27th-dns-root-key-ceremony/)份生成，并在 [2018 年 10 月](https://www.icann.org/news/announcement-2018-10-15-en)完成切换。 现在我们利用这个地址查询根域名自身，结果如下（部分无关内容已经被删掉）：

$ dig @a.root-servers.net. . any +dnssec
;; ANSWER SECTION:
.518400INNSa.root-servers.net.
;; 这里省略剩下 12 个根域名服务器
.518400INRRSIGNS 8 0 518400 20170227170000 20170214160000 61045 . HnSVXyC8UZuXnpOsZOv1/GP2byJFG9Y9ch4q0eUw/6CMEJ403spJ67Oo JiAGhdiE6xlONAMQN0Q7LpA7/bgCf29mmVJDcG76b/qaVnmRjKErBwep 68K831Uph2V+Rixcw8mx5XYWuMDyKDiRWlrPyY/bT0a7Us7dTnhkNJ+D g25E0lqXNKY9XgroVoTlwc5tCIe6L8GhoDU+LTLtBySBgQa3kEAI7WUQ CT4l47BCu3zzh8sJtdKGEXnXD0e22pB4ZaYF80iVWL1cRgghn2HphlN0 1kFJr3WuuIKP9r4vZFIjKiinV1KJdBBW2fciGAx+nZbP5sSUlOdiz/56 BZKM3g==
.86400INNSECaaa. NS SOA RRSIG NSEC DNSKEY
.86400INRRSIGNSEC 8 0 86400 20170227170000 20170214160000 61045 . JQQEDSGFolKu38MmdvvDj7Zi2AstqZc2cwhPQE+RRwTBVl3SWQOQ4FaS Wta+CdbhbaRAKQ9dUiOif95LLarewJDF9e4O2zTDsLt5MlgXLGZr3xd4 9HhDkEzjRk4Zro2qquvWmsHUjn+fbru4FsO6sZyS/FWjfh0XImlIYfh4 D50IplgRwv6awu4mO2RzJ0VL94l4WMMnV42vPSfWiNpL+9g7PHmaWkwe EqH7RamPDzw/M3bmts5yWp+cEI4IzE25kmZAHwN9EQHNNtDL3qKtAzrY wj6e8VVw0rI/XJ3DMI5aRk3xB+ac13dQv8cWtQZRImw76A5/N6clBXJS ZpmT+w==
.172800INDNSKEY256 3 8 AwEAAYvgWbYkpeGgdPKaKTJU3Us4YSTRgy7+dzvfArIhi2tKoZ/WR1Df w883SOU6Uw7tpVRkLarN0oIMK/xbOBD1DcXnyfElBwKsz4sVVWmfyr/x +igD/UjrcJ5zEBUrUmVtHyjar7ccaVc1/3ntkhZjI1hcungAlOhPhHlk MeX+5Azx6GdX//An5OgrdyH3o/JmOPMDX1mt806JI/hf0EwAp1pBwo5e 8SrSuR1tD3sgNjr6IzCdrKSgqi92z49zcdis3EaY199WFW60DCS7ydu+ +T5Xa+GyOw1quagwf/JUC/mEpeBQYWrnpkBbpDB3sy4+P2i8iCvavehb RyVm9U0MlIc=
.172800INDNSKEY257 3 8 AwEAAagAIKlVZrpC6Ia7gEzahOR+9W29euxhJhVVLOyQbSEW0O8gcCjF FVQUTf6v58fLjwBd0YI0EzrAcQqBGCzh/RStIoO8g0NfnfL2MTJRkxoX bfDaUeVPQuYEhg37NZWAJQ9VnMVDxP/VHL496M/QZxkjf5/Efucp2gaD X6RS6CXpoY68LsvPVjR0ZSwzz1apAzvN9dlzEheX7ICJBBtuA6G3LQpz W5hOA2hzCTMjJPJ8LbqF6dsV6DoBQzgul0sGIcGOYl7OyQdXfZ57relS Qageu+ipAdTTJ25AsRTAoub8ONGcLmqrAmRLKBP1dfwhYB4N7knNnulq QxA+Uk1ihz0=
.172800INRRSIGDNSKEY 8 0 172800 20170303000000 20170210000000 19036 . KHz7GVvg5DxUv70bUhSjRy1JO5soL+h6M08g8bSKecd+4NmZI87Sn20p uZNRuiASbnG63i89Z2S45NBAR8KtqB6N5CrRhLhfxZcRo5k3Ts6zsC1E J58upPKzFtu/sJBsPDjcRJJKbXlB4hLukQwVhn/MbsXxZdZGI57WoLFx bbR49NlFJrlrbTi2gieRR1SCLfT9aiBGsJA3T4jXap9FIsikNf1DJA8H cnQTW7hFi8l/O2ni2hbjsIE4S3GRTMypqDR/s7piy/qukfWwSknk6YZT bzld6ZgbZK+oOhRgj/W6XW78bJl0onov0F1wD0NQsec+sk2P+JNMc4xg vQmn9g==
.86400INSOAa.root-servers.net. nstld.verisign-grs.com. 2017021401 1800 900 604800 86400
.86400INRRSIGSOA 8 0 86400 20170227170000 20170214160000 61045 . A5CqIucYyfFTzp03EuajDjp5Vw6dd3Oxip60AI7MCs/2xfBu1red4ZvF GfIEGHstG61iAxf7S3WlycHX9xKyfIOUPmMxuvkI9/NXMUHuvjUjv9KW TTkc1HV6PuUB1sv9gsuQ6GFnHCXAgMKXZs9YofRDlBi2jxAvJVc5U7nG sd8UqQs4WcinMHNvFV9+gwfax0Cr9KFDmDUbS+S2wYmNs+SGOn+CbFrD 8gs34GiYao8i0QGw7RVGTVJiuVOuUkeYe4iSXnJjNjeIlm8liq6PRXgM nI+ndPDogA/a8JATfyzQ97VDRwe/FucoTbe5qd2cHxqh1ZxxPkA3K3Fj 8Jv3kg==
;; ADDITIONAL SECTION:
a.root-servers.net.518400INA198.41.0.4
a.root-servers.net.518400INAAAA2001:503:ba3e::2:30
;; 这里省略剩下 12 个根域名服务器

可以看到，此时没有再返回 DS 记录，因为 **DS 记录总是由一个区域的上一级区域的权威服务器返回**，之后还会再次提到这个问题。此处的 DNSKEY、RRSIG、NSEC 是三个关于 DNSSEC 的记录类型：

*   **DNSKEY 记录**：用于 DNSSEC 的记录，内容是一个**公钥**。
*   **NSEC 和 NSEC3 记录**：用于说明该域名下有哪些记录，从而可以用排除法证明该域名下没有哪些记录。
*   **RRSIG 记录**：记录集的**数字签名**，相当于是使用私钥加密后的内容。用于给除去自身外所有的记录集签名。下文有些地方直接将此记录叫做了签名。

可以看到，上方查询的 DNSKEY 记录有两条，这两条的内容的第一项分别是 256 和 257。256 是 ZSK（zone-signing keys），257 是 KSK（key-signing keys）。其中 KSK 是专门用于签名 DNSKEY 记录集（就是 ZSK 与 KSK），而 ZSK 是用于签名该区域下的其他记录集。 仔细观察，就可以看出，每一种记录后面就对应着一个用于签名该种类记录的 RRSIG 记录，比如上面查询结果中的 NS、NSEC、DNSKEY、SOA 记录的后面都跟着一个 RRSIG 记录。 举个例子，客户端解析并验证根域名的 SOA 记录的方法大概如下：

1.  **解析A**：使用根域名服务器解析根域名自身下的 DNSKEY 和 SOA 记录，并要求返回签名
2.  **验证1**：使用已知的 DS 记录验证 DNSKEY 中的 KSK
3.  **验证2**：使用 KSK 及其签名验证 DNSKEY 记录集
4.  **验证3**：使用 ZSK 和 SOA 的签名验证 SOA 记录

但是值得注意的是，根域名服务器所返回的 Glue 记录却没有数字签名，那是因为这是不必要的。就算 Glue 记录被篡改成了别的服务器，那个服务器在解析根域名时也不能篡改任何权威记录（在 ANSWER SECTION 下）。

## 一级域名和二级域名

然后，我们来使用根域名服务器解析一级域名：

$ dig @a.root-servers.net. xyz. any +dnssec
;; AUTHORITY SECTION:
xyz.172800INNSx.nic.xyz.
;; 这里省略剩下 3 个服务器
xyz.86400INDS3599 8 2 B9733869BC84C86BB59D102BA5DA6B27B2088552332A39DCD54BC4E8 D66B0499
xyz.86400INDS3599 8 1 3FA3B264F45DB5F38BEDEAF1A88B76AA318C2C7F
xyz.86400INRRSIGDS 8 1 86400 20170227170000 20170214160000 61045 . gXpaapTu67jlkfOeujL455lFDGLmLkFpnI+f8VNLMehozA7qWQD71oso SXJxkOB6o/ldXqoLGIo1khsYS8SMltOCMisJ6eA2cLokB7Ybzsaw8GWZ rkx64u2JbELWMbwGnY3PnZHGlBT77oAt49KNDfpxhgm3k1Yrcrua25D8 PL4fz6IQYQIMXWiHM/V2jH6E2Vu1Ynrjiu0lPEMf0TnGsK/URnCGE9uZ caT41mNz9kri/wkuQR11XtHjsN/qZgmcxZK+Tf4VQfOOdcfey4wAa1CM HRQ3Pm4mLo4LQwiESeMuqFyriizdMG4piNP7NLuI54GqWCGNSyDYbOdL X0n2Aw==
;; ADDITIONAL SECTION:
x.nic.xyz.172800INA194.169.218.42
x.nic.xyz.172800INAAAA2001:67c:13cc::1:42
;; 这里省略剩下 3 个服务器

这里返回的 DS 记录，虽然是两个，但是其 Key Tag（即第一项，为 3599）是相同的，后面两项算法有所不同，这其实就是同一个 KSK 的两种不同的哈希值算法，这个是为了提高兼容性和有限的安全性。这与刚才根域名的情况不一样，根域名下面的是完全两个不同的 KSK 的不同的 DS。 此时我们发现了不仅是 Glue 记录，NS 记录下也没有签名，这是因为这里返回的 NS 记录是属于委托记录（在 AUTHORITY SECTION 下），也不需要签名，`xyz.` 下 NS 记录的签名应该有 `xyz.` 的解析服务器来完成（而 DS 记录是例外）。我们来使用一级域名服务器来解析其自身：

$ dig @x.nic.xyz. xyz. any +dnssec
;; ANSWER SECTION:
xyz.172800INNSx.nic.xyz.
xyz.172800INNSy.nic.xyz.
xyz.172800INNSz.nic.xyz.
xyz.172800INNSgenerationxyz.nic.xyz.
xyz.172800INRRSIGNS 8 1 172800 20170525152637 20170425162314 47496 xyz. p57paKPWMyhwmz5IkkbZOMC/dIfxyANZ6QzRbEBiOff5JXnrdpKEX4YT zPMzF4SSNHPuK53uuJTtt2E4W3Xd2VjGVUx7V2mP7Hxs0nQblCDbQa51 zr6kYoXEOcdwVx23GyLe0baPELtEkQZHeKx5eWyZTUDCri4DBCZZv9m+ Lbk=
xyz.3600INSOAns0.centralnic.net. hostmaster.centralnic.net. 3000288446 900 1800 6048000 3600
xyz.3600INRRSIGSOA 8 1 3600 20170606000517 20170506113911 47496 xyz. oanexcZRLZ+NEPvSGhl0qyi6LH/3ubP+0JWjlNvcduZWUp7oQt4VWfy/ w0T2Y2/610u7mvcxRty2p6cZq1arVMLOci7ZzMpPHkNDxXHcNRxlMNL1 6mwLgKzOlxp0acEGhqQBhj/XQ2icScf8PMChC7uRsFOz9nqAxelcgJgY D9I=
xyz.3600INDNSKEY257 3 8 AwEAAbYRTzkgLg4oxcFb/+oFQMvluEut45siTtLiNL7t5Fim/ZnYhkxa l6TiCUywnfgiycJyneNmtC/3eoTcz5dlrlRB5dwDehcqiZoFiqjaXGHc ykHGFBDynD0/sRcEAQL+bLMv2qA+o2L7pDPHbCGJVXlUq57oTWfS4esb GDIa+1Bs8gDVMGUZcbRmeeKkc/MH2Oq1ApE5EKjH0ZRvYWS6afsWyvlX D2NXDthS5LltVKqqjhi6dy2O02stOt41z1qwfRlU89b3HXfDghlJ/L33 DE+OcTyK0yRJ+ay4WpBgQJL8GDFKz1hnR2lOjYXLttJD7aHfcYyVO6zY sx2aeHI0OYM=
xyz.3600INDNSKEY256 3 8 AwEAAaAxrInKa1BlzuJsfT/gWfrUUH5OP7IJquNOLRU7LVbKwJEv655b kBBbW53wVXmnWJfPxykrMme8a91FFqXTYepvVN5vJe9QuCfiW/C64jSo 0HNXhbSUkV1ZDcy+zgAmMriPm8g5ki7KJ7KRs+YRoL2NwCm5fJVsAchr WalFB4z3
xyz.3600INDNSKEY256 3 8 AwEAAdNAEAD8rebFpKuiLr0BwTNQoECMnfJjiZ54ZCCke208h9eX7ui7 WFFz9hjmvAgIFavN5vVhR5SnDTRvD5iDsMKvefXbnz4Qeu4GILwJuTqC QAcqw6RUp1+U1KEkwRP/noqA4fSkmnInbQwW+Yq+bxohGQVatZiAiO/G ppSggZX3
xyz.3600INRRSIGDNSKEY 8 1 3600 20170520002252 20170419140553 3599 xyz. h5TV5pu/QAAUal72x8Dm8tgqBzRvDSznaDrRqV0Fu8ponhfXQFjdG3p1 2/IVdkNLtLZq4I2aUMwJeTZcyq5gRcWOror0V6uChW5fgIkH7abj1CYL tSRv3M7mVBduGNIzMuITJu5Pn1BVXiF9FsTw1ks+wDjdPn2OLe5BKRmj d+6GgwwBhg4V2efFcb+peRBCRpk+i3S1dlMyILCCgAvnAaGbh3k+vaKN 2wb528jSvH0QVIXP8PTAxLw86IfFlvLm8Lxo1e8hweI+4hgECNX7UzeG epXE+LpOiZwkhf7JncytOcxw6YzSAQETYJfcK1MlMcH5zNzjhFTNoMV3 M4QTLQ==
;; ADDITIONAL SECTION:
x.nic.xyz.172800INA194.169.218.42
y.nic.xyz.172800INA185.24.64.42
z.nic.xyz.172800INA212.18.248.42
generationxyz.nic.xyz.172800INA212.18.249.42
x.nic.xyz.172800INAAAA2001:67c:13cc::1:42
y.nic.xyz.172800INAAAA2a04:2b00:13cc::1:42
z.nic.xyz.172800INAAAA2a04:2b00:13ee::42
generationxyz.nic.xyz.172800INAAAA2a04:2b00:13ff::42

可以看到， `xyz.` 下的解析服务器就返回了 NS 记录的签名。 然而，`xyz.` 下却有两个 ZSK，这大概是因为 `xyz.` 下有两个私钥，这样的话每一个签名可以使用两个私钥中的任何一个签，灵活性更高。此外，我们也看出来了区分 KSK 和 ZSK 的意义：KSK 和 ZSK 的数量可以不相等。 然后，我们来使用一级域名的服务器来解析我的二级域名：

$ dig @x.nic.xyz. tlo.xyz. any +dnssec
;; AUTHORITY SECTION:
tlo.xyz.3600INNSkami.ns.cloudflare.com.
tlo.xyz.3600INNSgordon.ns.cloudflare.com.
tlo.xyz.3600INDS2371 13 2 913F95FD6716594B19E68352D6A76002FDFA595BB6DF5CAAE7E671EE 028EF346
tlo.xyz.3600INRRSIGDS 8 2 3600 20170303035908 20170201045318 7558 xyz. b69lhRaZM8lWN44qaQCCm4+479ATwt+OlRWD770jmLJnai2ob/0CWPEZ pFQ+y/k6n/X8VPZa2IVwxB6qUTtirtOolBHVA4gmPQffXiYiTbP1dDT9 G7BwNMdOCGkH0bySW9rFpi3zKYvOieNQLlV/i61ox78AgxQeX4k800QN gEE=

由于 NS 记录属于委托记录，所以 NS 下也没有签名。 由于这个域名使用的 NS 是 `kami.ns.cloudflare.com.` ，不属于 `xyz.` 之下，所以没有任何 Glue 记录，于是这需要再按照流程再重头解析一遍 `kami.ns.cloudflare.com.` ，这里就省略了。 最后，我们来使用我二级域名服务器来解析二级域名自身：

$ dig @kami.ns.cloudflare.com. tlo.xyz. any +dnssec
;; ANSWER SECTION:
tlo.xyz.60INA52.84.21.12
tlo.xyz.60INA52.84.21.243
tlo.xyz.60INA52.84.21.67
tlo.xyz.60INA52.84.21.107
tlo.xyz.60INA52.84.21.4
tlo.xyz.60INA52.84.21.46
tlo.xyz.60INA52.84.21.29
tlo.xyz.60INA52.84.21.224
tlo.xyz.60INRRSIGA 13 2 60 20170507145606 20170505125606 35273 tlo.xyz. tcMNEbUGrnCoTK1Z7Xmo15k+pLyZJ+m28nKt/o5s+/ezrcMsgFv1C0bY ABs9M8cqjw+0Ld8DTtAwTQVwpAUe+g==
tlo.xyz.60INAAAA2600:9000:203a:1000:b:fe0:fc00:93a1
tlo.xyz.60INAAAA2600:9000:203a:7e00:b:fe0:fc00:93a1
tlo.xyz.60INAAAA2600:9000:203a:e400:b:fe0:fc00:93a1
tlo.xyz.60INAAAA2600:9000:203a:a200:b:fe0:fc00:93a1
tlo.xyz.60INAAAA2600:9000:203a:4800:b:fe0:fc00:93a1
tlo.xyz.60INAAAA2600:9000:203a:3000:b:fe0:fc00:93a1
tlo.xyz.60INAAAA2600:9000:203a:1a00:b:fe0:fc00:93a1
tlo.xyz.60INAAAA2600:9000:203a:a000:b:fe0:fc00:93a1
tlo.xyz.60INRRSIGAAAA 13 2 60 20170507145630 20170505125630 35273 tlo.xyz. QV5gEUO9NK3W2G4aF/dTZrmsGURyVAiU3eyyuR4lp4YJ7jxGjmCQArPB 4CYz6laN+V6Kd78gi7v50gaf+WCeDQ==
tlo.xyz.3600INSOAgordon.ns.cloudflare.com. dns.cloudflare.com. 2024522030 10000 2400 604800 3600
tlo.xyz.3600INRRSIGSOA 13 2 3600 20170507145653 20170505125653 35273 tlo.xyz. KnJkiBfvb0xhw3mAjKxnWPSMptc+eoN7Qh50HJQYnmycvV1K9ADFKYyq RwhKzWEOFHXtsn8Pxh+d/EY0x4EVEw==
tlo.xyz.86400INNSgordon.ns.cloudflare.com.
tlo.xyz.86400INNSkami.ns.cloudflare.com.
tlo.xyz.86400INRRSIGNS 13 2 86400 20170507145712 20170505125712 35273 tlo.xyz. vQDzeIteIeVdbPS7nxNXCVeGD97+ePvEHdPK263oocoDPY59tVOG6V+a s7k8GHSFJ8KKu8edoWcUayi3aNFY7g==
tlo.xyz.86400INTXT"v=spf1 include:email.freshdesk.com include:\_spf.myorderbox.com include:amazonses.com -all"
tlo.xyz.86400INRRSIGTXT 13 2 86400 20170507145729 20170505125729 35273 tlo.xyz. NDFDF9PHFSSvQu7oF17cNWIrQUrfaPA/019i6hCvj7JJiA21DWp0w5J3 BlxDEN6wIGq4Nzb4IVE0uf+zmdTb0w==
tlo.xyz.3600INDNSKEY257 3 13 mdsswUyr3DPW132mOi8V9xESWE8jTo0dxCjjnopKl+GqJxpVXckHAeF+ KkxLbxILfDLUT0rAK9iUzy1L53eKGQ==
tlo.xyz.3600INDNSKEY256 3 13 koPbw9wmYZ7ggcjnQ6ayHyhHaDNMYELKTqT+qRGrZpWSccr/lBcrm10Z 1PuQHB3Azhii+sb0PYFkH1ruxLhe5g==
tlo.xyz.3600INRRSIGDNSKEY 13 2 3600 20170529092807 20170330092807 2371 tlo.xyz. SDm3eGWVamR+GIZ8TEcYDeik73gMUVyX6TGGtkir6A6TIY+2zvXwtfrN HEvkygTfiOuEn+/Ipj08o8+NyZeAZw==

下面总结一下解析并验证 `tlo.xyz.` 下的全部 A 记录的方法，DNS 在实际解析过程中会尝试尽可能跳过不必要的请求：

1.  **解析A**：使用根域名服务器解析根域名下的 DNSKEY 记录，并要求签名
2.  **验证1**：使用已知的根域名 DS 记录验证根域名的 KSK
3.  **验证2**：使用根域名的 KSK 及其签名验证 DNSKEY 记录集
4.  **解析B**：使用根域名服务器解析 `tlo.xyz.` ，返回的是 `xyz.` 下的 NS 和 DS 记录，包含了签名
5.  **验证3**：使用根域名的 ZSK 和 DS 的签名验证 `xyz.` 的 DS 记录
6.  **解析A**：使用 `xyz.` 服务器解析 `xyz.` 下的 DNSKEY 记录，并要求签名
7.  **验证1**：使用 `xyz.` 的 DS 记录验证 `xyz.` 的 KSK
8.  **验证2**：使用 `xyz.` 的 KSK 及其签名验证 DNSKEY 记录集
9.  **解析B**：使用 `xyz.` 服务器解析 tlo.xyz. 下的 NS 和 DS 记录，并要求签名
10.  **验证3**：使用 `xyz.` 的 ZSK 和 DS 的签名验证 `tlo.xyz.` 的 DS 记录
11.  **解析A**：使用 `tlo.xyz.` 服务器解析 `tlo.xyz.` 下的 DNSKEY 和 A 记录，并要求签名
12.  **验证1**：使用 `tlo.xyz.` 的 DS 记录验证 `tlo.xyz.` 的 KSK
13.  **验证2**：使用 `tlo.xyz.` 的 ZSK 和 A 的签名验证 `tlo.xyz.` 的 A 记录

为了做区分，我把解析分为了两类，验证分为了三类：

*   **解析A**：解析权威记录并要求签名
*   **解析B**：解析委托记录并要求 DS 记录的签名
*   **验证1**：根据 DS 验证 KSK
*   **验证2**：根据 KSK 验证 ZSK
*   **验证3**：根据 ZSK 验证解析记录

## NSEC 记录

NSEC 记录比较特殊，所以单独的讲一下。 在全面普及 DNSSEC 之前，仍然有不少域名并不支持 DNSSEC，此时如何让已经支持 DNSSEC 的网站进行签名认证，拒绝解析签名错误的请求，又同时让没有 DNSSEC 的域名无视签名正常解析呢？HTTPS 的推进是区分了协议：以 `https://` 开头的网站进行签名认证，以 `http://` 开头的网站不进行签名认证，在 HSTS Preload 里的域名则强制进行签名验证。而实际上，HTTP 和 HTTPS 是两种不同的协议，而支持 DNSSEC 的 DNS 与普通的 DNS 是同一种协议，前者是后者的子集。**只有域名下有 DS 记录时，才会进行签名认证，否则还是按照普通的处理**。 那么试想，攻击人可以在解析 `tlo.xyz.` 时的第九步做手脚：删除 DS 记录以及 DS 的签名，这样不就相当于移除了这个域名的 DNSSEC 了吗？（有些类似于 HTTPS 降级攻击），或者直接删除某个域名下的 A 记录，客户端能知道这个域名下是真的没有 A 记录还是被恶意删除了？实际上这样做手脚是没用的，当开启了 DNSSEC 的权威服务器收到了一个不存在的记录的请求时（这可以是不存在的子域名，也可以是某个域名下不存在的一些记录类型），不是返回空的内容，而是**返回一个 NSEC 记录去声明这个域名下没有这种记录**，同时也将这个记录签名。综上所述，开启了 DNSSEC 后对于该区域下的所有的 DNS 请求都会签名，从来不会返回空的内容。 [根据这里公开的数据](http://stats.research.icann.org/dns/tld_report/)，我们来尝试一下解析第一个不支持 DNSSEC 的一级域名：`ae.` 的 DS 记录的结果

$ dig @a.root-servers.net. ae. ds +dnssec
;; AUTHORITY SECTION:
.86400INSOAa.root-servers.net. nstld.verisign-grs.com. 2017021401 1800 900 604800 86400
.86400INRRSIGSOA 8 0 86400 20170227170000 20170214160000 61045 . A5CqIucYyfFTzp03EuajDjp5Vw6dd3Oxip60AI7MCs/2xfBu1red4ZvF GfIEGHstG61iAxf7S3WlycHX9xKyfIOUPmMxuvkI9/NXMUHuvjUjv9KW TTkc1HV6PuUB1sv9gsuQ6GFnHCXAgMKXZs9YofRDlBi2jxAvJVc5U7nG sd8UqQs4WcinMHNvFV9+gwfax0Cr9KFDmDUbS+S2wYmNs+SGOn+CbFrD 8gs34GiYao8i0QGw7RVGTVJiuVOuUkeYe4iSXnJjNjeIlm8liq6PRXgM nI+ndPDogA/a8JATfyzQ97VDRwe/FucoTbe5qd2cHxqh1ZxxPkA3K3Fj 8Jv3kg==
ae.86400INNSECaeg. NS RRSIG NSEC
ae.86400INRRSIGNSEC 8 1 86400 20170227170000 20170214160000 61045 . B03J+aJuEA5r5Va8QiecBHZUucisWgdC8b14Q4MU5oGSdgmK9PmHLKMS mUiGj/OzH51P1l0G6zxG6bxU56tZ4gSME+rcpIntdKyiWU4QLpkiPa32 aApHFmu0pzugGSDWnQUmNDmCig7jJ2J61xlOzx19ni0eJazAthRtGWuK WI9bCVt9Yb7Bd21AedC0gugQWY+LKj7HR3zRhZ5dywpcTQUc78BrJDvh P8UxWprUJozcMYdVDqA5TvSlRHz8aLOnkD/olVsE5cU6qSvCX32E7WuQ IeFfhf1J940hly/3f960Dvm5kwX8l6CkNW083yLCnG8e7zArEUBRthvA a90SJw==

注意新增的 NSEC 记录，这个记录首先声明了一级域名 `ae.` 下只有 NS RRSIG NSEC 这三种记录，也就是说没有 DS 记录。此外，它还说明了 `ae.` 之后的一个一级域名是 `aeg.` ，所以通过这个记录，可以轻松的证明不存在 `aea.` 、`aeb.` 、`aec.` …… 这些一级域名。 那么，如果请求 `aea.` 这个不存在的一级域名，会发生什么情况？

$ dig @a.root-servers.net. aea. any +dnssec
;; AUTHORITY SECTION:
ae.86400INNSECaeg. NS RRSIG NSEC
ae.86400INRRSIGNSEC 8 1 86400 20170228050000 20170215040000 61045 . U2e52sVPmIup4pSfWzg7hupPZb63NdYdsiNEqr2ygDBQrgOQ6rT2SZkP xZVvHc7ZtfggUV1iT6kels8+d3beURz0Vf58x6up+PUF6svaFOmx2Bpu 42owq6wYQH6ll8GLOKiIC/35omIXja0VFj4ueG1HsbHbWVxUcL5bsDrt UWRUU9Hp1ySp36+H7M5NE+YPNk8soH2xyANe+STkymH661m8jJqXbG2X atbCEEOtuXuplvS7Rm/YRS+UEtsamC3A9bDBnus/OiL3KS1ztuvrxQfS 6a1z45UtL0PBBQ5DzNiVd9QHHhSpsaxFUqg0iw21CB6MZaK10EB7EJCQ EWkRkg==
.86400INNSECaaa. NS SOA RRSIG NSEC DNSKEY
.86400INRRSIGNSEC 8 0 86400 20170228050000 20170215040000 61045 . fnA/PW3QvSzI4MXZ+ylGhv/Z+F+u6YdAWnSz1DfbwSZkcpzwZoO1/uiY QtYhYU5GF/dbTk7oGEjStA0dWVzyyf+7opW+DS1+R9pn5N/LynyqZ6Et Swk85MQl04gu5LxLrnn6Nind2ozRMha4Nn7tNlYG59GLH3hXkaQ6xYmE hD0Ya+UE6h2vcQ8Y8m3ccifDO2rBukdsUJ13dZLAScNAVJU6/2YxlyyX fYY7G0Ktqu5Tq10YvfJazZ5VraBzw+bkEzM8UEPGNNfX9FTB7zxhjyhU h1u87Z/nKMoIznzVu6Xk9AC5JM1lU/OIHyYHCp+XzMGuUdjwNZH706ND MGq/rQ==
.86400INSOAa.root-servers.net. nstld.verisign-grs.com. 2017021500 1800 900 604800 86400
.86400INRRSIGSOA 8 0 86400 20170228050000 20170215040000 61045 . Nj7xEVPJ5DtBFRP9Zy0GCIwY/ax3v9n9JV0EsKyAeHPYDw4PBMpXQRxa banAl7DVyytO+xLz1NxY3iYTSPtyFjbAzkipC5BJT0EFovbQJ7VJOS4P nZBFaVltjGnzzrC8+hWESyhcwn2DdsNw94JqlkVPEtbT+u6vgXbIv5lD 1/YJMRcvWR74FzBC/bYyk+s0WWVNWDenioI2F7NCRgKSYm1+6qXK4on7 MFAmJE9TYYyZFFRiQurS1wH+d3/xQTtjd93GYOhpWVND0NyN/t4nkxhT spHrofo9GvzTIcGTcwT4Pp1bdBXL6dS0P+JIDXTKQN7u/3RwoJj/6jPm FOSEKw==

通过请求 `aea.` ，从请求的结果可以得出两个结论：

1.  `ae.` 和 `aeg.` 是两个相邻的一级域名（这也就相当于声明了不存在 `aea.` 这个一级域名），此外也说明了 `ae.` 下只有 NS SOA RRSIG NSEC DNSKEY 这些记录。
2.  根域名之后的一个一级域名是 `aaa.` ，此外也说明了根域名下只有 NS SOA RRSIG NSEC DNSKEY 这些记录。

注意，相邻的域名排序是包含该区域所有子域名的，也就是说所有的子域名都参加到了排序，刚才得出的 “`ae.` 和 `aeg.` 是两个相邻的一级域名” 其实并不准确，而应该是根域名区域下 `ae.` 和 `aeg.` 是两个相邻的子域名，因为 NSEC 结果还相当于说明了 `a.ae.` 这样的二级域名在根域名区域下也不存在。 如果 NSEC 是最后一个记录，那么它的下一个就又是区域自身了。 然而，可以发现，通过这个方法可以轻松的获得已经存在的记录：比如我只是试了一下 aea. 这个一级域名，却一下子就知道了根域名下还有 `aaa.` 、`ae.` 和 `aeg.` 三个一级域名，通过这样一直往下遍历，就能搜索到一个区域下的所有子域名。 知道所有的一级域名对于根域名服务器无所谓，因为一级域名的列表本来就是公开的。可是，这个功能也许不是我们所期望的，有的时候，想要在自己的域名下放置一些只有自己知道的子域名，这些子域名也许就是自己源站服务器的 IP，如果 DNSSEC 就这样实现的话，这就让其他人很容易就能遍历出来你所有的子域名。所以，在 DNSSEC 中，响应记录不存在的话还有两种解决方案，一种是对 NSEC 的 Hack，还有一种是 NSEC3：

#### NSEC 的 Hack

Cloudflare 上就使用了对于 NSEC 的 Hack，这样就能避免其他人遍历你的所有子域名。举个例子，正常的 NSEC 去解析 `a.tlo.xyz` 可能是这个结果（假如我只有 `www` 这一个子域名）：

$ dig @kami.ns.cloudflare.com. x.tlo.xyz. +dnssec
;; AUTHORITY SECTION:
tlo.xyz.3600INNSECwww.tlo.xyz. A MX TXT AAAA DNSKEY RRSIG NSEC
; 省略一个 RRSIG 记录
www.tlo.xyz.3600INNSECtlo.xyz. CNAME RRSIG NSEC
; 省略一个 RRSIG 记录
; 还省略了一个 SOA 以及 SOA 的 RRSIG 记录

通过观察 NSEC 记录，就可以直接看出这个域名下只有 `www` 一个子域名。 然而 Cloudflare 实际返回的结果是这样的：

$ dig @kami.ns.cloudflare.com. x.tlo.xyz. +dnssec
;; AUTHORITY SECTION:
tlo.xyz.3600INSOAgordon.ns.cloudflare.com. dns.cloudflare.com. 2023791623 10000 2400 604800 3600
tlo.xyz.3600INRRSIGSOA 13 2 3600 20170216150135 20170214130135 35273 tlo.xyz. ARUYgesljY5azg1RqFgoKbTN6OOmAQUqTsLiQyTBXAMO4P/CecFGwTKY f1cVTI/s4euNahfGOvc0MnDb2R55TQ==
x.tlo.xyz.3600INNSEC\\000.x.tlo.xyz. RRSIG NSEC
x.tlo.xyz.3600INRRSIGNSEC 13 3 3600 20170216150135 20170214130135 35273 tlo.xyz. y4g0Of3Ir/DqcbRT1ND5kwdGXlW++Zb+c9Cx0z60UAzbI+cpW2DDOmBB 4MMKi4zV9xEBg5Jq/8hwBGVo4ytDDg==

Cloudflare 这样则是告知了 `x.tlo.xyz.` 是存在的，但是只有 RRSIG 和 NSEC 记录，即相当于这个域名下没有任何记录。`x.tlo.xyz.` 之后的下一个域名是 `\000.x.tlo.xyz.` ，而实际上那个域名也是不存在的。这其实相当于 Cloudflare 撒了一个谎，并没有直接告知你这个域名的下一个域名。这虽然解决了问题，但是并不符合规范。

#### NSEC3

NSEC3 使用了在区域内的下一个记录内容的哈希值（按照哈希值的顺序排序）代替了原本的记录内容。从哈希值反推记录内容本身有一定的难度，于是就能够避免其他人遍历出所有的记录内容。（`guozeyu.com` 没有在生产环境中开启 DNSSEC，以下内容仅为测试结果）

$ dig @a.ns.guozeyu.com. ns.guozeyu.com +dnssec
;; AUTHORITY SECTION:
guozeyu.com.3600INSOAa.ns.guozeyu.com. ze3kr.icloud.com. 1 21600 3600 1209600 3600
guozeyu.com.3600INRRSIGSOA 8 2 21600 20170411191942 20170403191942 52311 guozeyu.com. bHSh4a0zcaFEwS5dNEj/JT9Aosuy8Wdh+U2WaPou95iywqG6VhH85BXT EhYnjmeph/CABF5HC2OvUf9HhcnxjPF9NAQ2cfPTr6Ael9aNBGLFSejI 5VmCdp4Q1sYD6hS51k5BY22bJRyu9v8zWHNLYDRJSFBk4kR0RSV5n0CK 4pA=
67uromrbachidk57be8035jf9gqnhmn1.guozeyu.com. 300IN NSEC3 1 0 1 F327CFB1FFD107F1 ENPCB7U0K7KFHLSCEOTOB7RAHS4TCH3V
67uromrbachidk57be8035jf9gqnhmn1.guozeyu.com. 300IN RRSIG NSEC3 8 3 300 20170411191942 20170403191942 52311 guozeyu.com. MpV+6foWp+XQpwJnNCiIE0dqGigqX+2Z7XWuCFAd/TUS1sBHwnTRKmB5 Rl8Wf23ZMMfZh/oRHQbm4vE1RecMd78ZuvQM61iOmwAOmjIhJJh+LPSg 5KXMmYimTmtyd7/N437XYqmBREbz9EQ66ZqGucOahncPfxX2jhErvICN KDc=

#### 标准的 NSEC 相比 NSEC3 的优点

标准的 NSEC 会暴露所有的子域名，而 NSEC3 不会，看起来 NSEC3 的优势明显。然而标准的 NSEC 相比 NSEC3 又有好处：子（Slave）DNS 服务器不需要拥有 DNS 的私钥，这样配置 Slave DNS 后就方便多了，和常规的 Slave 一样，只需要传送（Transer）整个区域即可，也能够正确的响应不存在的子域名。因为**在标准的 NSEC 下 NSEC 和 RRSIG 的数量是有限的**。而 NSEC3 或者 Hacked NSEC 都会根据不同的子域名返回不同的 NSEC(3) 记录，NSEC(3) 和 RRSIG 记录都是无限的。 举个例子，比如你现在可以下载到签名过的[根域名的区域](https://www.internic.net/domain/root.zone)。其中包含了所有的 NSEC 记录，**这样 RRSIG 可以在一台机器上生成**，并**将签名过的整个区域传送给其它子的根域名服务器上**，这样能够有效的确保私钥安全。而用 NSEC3 或者 Hacked NSEC 的话，每一个子 DNS 服务器都需要有私钥。根域名服务器的数量众多，也由各种不同的组织管理着，所以很有必要保护好私钥。所以对于这种不怕被遍历到所有子域名的区域来说，使用标准的 NSEC 也未尝不可。