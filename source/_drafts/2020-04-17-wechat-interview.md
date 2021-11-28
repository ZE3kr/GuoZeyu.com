---
title: 我用两天预 (fù) 习了前端并挂 (guò) 了微信总部面试，就为了写这篇文章
tags:
  - CDN
  - DNS
  - HTML5
  - HTTPS
  - 实习
  - 网站
id: '3882'
categories:
  - - 开发
date: 2020-04-17 20:19:18
---

本文介绍了我 HTML、CSS、JavaScript 两天速成的方法，以及腾讯广州微信 Web 前端组三次技术面试并通过的经历。作者目前大二，北京本科在读。但由于大三的美国交换项目受到疫情（COVID-19）的影响，需要延迟入学（Gap）一学期，7月 - 12月空闲，迫切需要找一个实习工作来填充。
<!-- more -->

两天复习确实不假。时间线如下：

*   **2020-04-10 17:11：**广州微信 Web 前端组 [@Molunerfinn (PiEgg)](https://github.com/Molunerfinn) 通过邮箱联系我，邀请我向他们部门投递简历。
*   **2020-04-10 17:36：**我投递了简历
*   **2020-04-10 19:33：**人事部门打电话给我约面试时间
*   **2020-04-13 15:00：**技术第一次面试
*   **2020-04-14 21:00：**技术第二次面试（组长面）
*   **2020-04-16 20:00：**技术第三次面试（总监面）
*   **2020-04-18 14:30：**HR 面试
*   **2020-04-18 23:00：**确认通过

此外，之前我挂过一次后端的一面：

*   **2020-03-21 10:30：**北京后端开发第一次面试

## 前期准备

先说说我的基础。初三开始接触 Web 前端，HTML、JavaScript、CSS 等原生的语法比较熟悉，DOM 操作、事件监听、AJAX 都有了解，知道一小点 HTML5 和 CSS3 特性。网络相关比较熟悉，毕竟爬虫写了不少，也没少写过后端（PHP）。但是最近一次写 Web 项目是一年多前了，已经忘了不少。但前端框架 Vue.js、React.js、Angular.js **一个不会**，JavaScript 的继承、闭包不了解。

我第一次面腾讯是在 2020-03-21，面的是北京腾讯的后台开发。当时直接挂掉了，面试官的评语是 “C++ STL 不熟悉，多线程基础为 0”，描述的没有问题。因为我这些跟学校学的，期末也拿了非常高的分数（前 3%），但毕竟国内计算机课的教学水平有待提升。这是由于高考筛选制度，把大多数不喜欢编程，不会编程的人招到了计算机专业。学校内没有编程氛围，老师讲课也只能照顾大多数人，无可厚非。

前端就不一样了，这个不是跟着学校学的。我初三开始自学写网站，接触了域名、DNS、Jekyll 静态网页生成器、VPS、Nginx、PHP、写过 WordPress 插件（并发布），大一还做过前端开发的外包，也可以说是有点项目经历了。

我当时也并非从头准备。我买了本适合有一定基础的开发阅读的张容铭的《前端程序员面试秘籍》。由于是周一面试，我周五晚上下的单，周末两天看了看，一共就翻了前一半。知道了很多 CSS3 和 HTML5 特性，问到这个不至于答不上来。第一次听说 JavaScript 的闭包、链式调用、对象的继承、事件冒泡。面试中考到了不少，可以说这是我的救命稻草了。

## 挂掉的后台开发面试

后台开发的投递录用比例是 6:1。问了这几个问题：

1.  CDN 的工作原理
2.  DNS 的工作原理
3.  C++ 多态
4.  C++ 继承时构造函数和析构函数的执行顺序
5.  C++ 继承时析构函数要额外注意哪一点
6.  C++ 多线程（不会）
7.  数据库的 Transaction（一对一转账，会；二对一转账，不会）
8.  说说数据库的 “锁”（不会）
9.  C++ map 迭代器，去除某值的元素（没写出来）
10.  写一下检查链表有无环的程序（一开始答错，后改正）

评语如下：

可能是他面试经验不足得原因吧，好几个笔试题都没回答出来。
他很难过其他人面试的，他才大二，后面再练习练习多掌握点基本功会后会很好的。

不过关于 “很难过其他人面试” 后来被证明打脸，因为我成功被前端组捞走了。不过这也和我**前期面试**准备有关：这次挂掉的面试基本上就没准备。

## 前端面试经历

前端的投递录用比例是 7:1，貌似比后台开发要难一点点。

### 技术第一次面试

第一次面试话不多说，上来发了个短信，在线写代码（俗称：机试/笔试）。当时给我了四道编程题，第一题是闭包、第二题是链式调用、三四题是算法。我头铁，写了闭包和两道算法。

四选三，限时 1 小时，但最后给我延长到 1.25 小时了。

#### 编程第一题：闭包

```
第一题：实现 multiply
要求：
multiply(1,2).result == 2
multiply(1,2)(3).result == 6
multiply(1,2)(3,4).result == 24
multiply(1,2)(3,4)(5).result == 120
```

这个机试是有反馈的：

```
时不时贴出来。我看看你的思路
```

我立即发现这题试闭包。我第一次提交了个有问题的代码（但能实现要求样例）。其实这是我第一次写这种题，有些生疏，实在惨不忍睹：

```
var multiply = function (a, b) {
    var multiply = function (c, d) {
        if(typeof d === 'undefined'){
            return {'result': a * b * c};
        }
        var e1 = 1;
        var multiply = function (e) {
            if(typeof e !== 'undefined'){
                e1 = e;
            }
            return {'result': a * b * c * d * e};
        };
        multiply.result = a * b * c * d * e1;
        return multiply;
    };
    multiply.result = a * b;
    return multiply;
};
```

面试官指出了问题：

第一题希望是，可以传无限参数，以及是调用任意次数的。
即 multiply(1,2,3,4,5,6,7,…)(…)(…)(…)(…)(…).result 都是可以的

我想了想，发现这样反而简单了：

```
function multiplyHelper(arguments, base) {
    let result = 1;
    for (let i = 0; i < arguments.length; i++){
        result *= arguments[i];
    }
    var multiply = function () {
        return multiplyHelper(arguments, result * base);
    };
    multiply.result = result * base;
    return multiply;
}
function multiply() {
    return multiplyHelper(arguments, 1);
}
```

就愉快的通过了。

#### 编程第二题：链式调用

这题我知道考点，但我毕竟没写过，我就给跳过了。

```
第二题：实现一个 superBaby:
要求:
输入:
superBaby("tom")
输出:
I am tom
输入：
superBaby("tom").sleep(10).eat("apple").sleep(4).eat("cake")
输出:
I am tom
(等待10秒)
Eating apple
(等待4秒)
Eating cake
输入：
superBaby("tom").eat("banana").sleepFirst(5).eat("apple")
输出:
(等待5秒后)
I am tom
Eating banana
Eating apple
```

#### 编程第三题：动态规划

```
第三题：给定一个代表每个房屋存放金额的非负整数数组，计算你在不触动警报装置的情况下，能够偷窃到的最高金额
你是一个专业的小偷，计划偷窃沿街的房屋。
每间房内都藏有一定的现金，影响你偷窃的唯一制约因素就是相邻的房屋装有相互连通的防盗系统，如果两间相邻的房屋在同一晚上被小偷闯入，系统会自动报警。
示例 1:
输入: \[1,2,3,1\]
输出: 4
解释: 偷窃 1 号房屋 (金额 = 1) ，然后偷窃 3 号房屋 (金额 = 3)。 偷窃到的最高金额 = 1 + 3 = 4 。
示例 2:
输入: \[2,7,9,3,1\]
输出: 12
解释: 偷窃 1 号房屋 (金额 = 2), 偷窃 3 号房屋 (金额 = 9)，接着偷窃 5 号房屋 (金额 = 1)。偷窃到的最高金额 = 2 + 9 + 1 = 12 。
```

我给出的解：

```
// Dynamic Programming (Recursive + Cache)
// Complexity O(n)
function robHelper(houses, index, cache) {
	if (index >= houses.length) {
		return 0;
	}
	if (typeof cache[index] !== 'undefined') {
		return cache[index];
	}
	let a = houses[index] + robHelper(houses, index + 2, cache);
	let b = houses[index] + robHelper(houses, index + 3, cache);
	let c = houses[index + 1] + robHelper(houses, index + 3, cache);
	let d = houses[index + 1] + robHelper(houses, index + 4, cache);
	let r = a > b ? a : b;
	r = c > r ? c : r;
	r = d > r ? d : r;
	cache[index] = r;
	return r;
}
function rob(houses) {
	let cache = [];
	let a = robHelper(houses, 0, cache);
	let b = robHelper(houses, 1, cache);
	return a > b ? a : b;
}
```

面试官追问如果连环会怎么样（不能同时取第一个和最后一个）。当时想了想，但答的比较糊涂，说出了个大概。现在发现其实就分两种情况讨论就行。第一种是去除数组最后一项计算，第二种是去除数组第一项计算，然后取最大值就行了。

#### 编程第四题：常规算法题

##### 3 Sum

```
第四题：微信负责企业文化的 HR 经常会举办各种活动，去年微信年会的时候，HR 赵组织了一堆的程序员玩起了集卡游戏。
游戏规则非常有意思，每个人会被分配一张卡片，卡片上会有一个整数，每个人需要找到两位同伴（即三个人成组），组成一个小组去领取奖品，要求是这个小组中所有的人的数字相加起来的结果为 0。
此时 HR 赵，拿出了一个随机数组了，需要你吧所有可以分组的答案输出出来。
举个例子：
例如生成的数组是 [ -100, -200, 0, 300, 0, 4，96, 96]，
那么答案是：
[
    [-200, -100, 300],
    [-100, 4, 96]
]
需要注意的是 活动要求组队中大家是平等的，没有先后顺序，答案不可以重复，例如 [-100, 4, 96], [4, 96, -100] 会被认同为同一个答案,因为所包含的数字是一样的
要求：答案不能出错，运行速度尽量的快。
```

这个我以前做过，但我没看过题解，面试时也没敢上网搜（诚信做人）。就写了个复杂度为平方阶的垃圾算法：

```
/**
 * 计算组合情况
 * 3sum
 * Complexity: O(n ^ 2)
 * TODO: skip 0, 0, 0; convert to integer
 * @param nums 随机数组
 * @return array 可以分组在一起的情况
 */
function gameAnswer(nums) {
	let rawAnswer = [];
	for (let i = 0; i < nums.length; i++){
		let two = twoSum(nums, -nums[i], i);
		for (let j = 0; j < two.length; j++){
			rawAnswer.push([two[j], -nums[i] - two[j], nums[i]]);
		}
	}
	let answerDict = {};
	for (let i = 0; i < rawAnswer.length; i++){
		rawAnswer[i].sort();
		answerDict[rawAnswer[i].join('_')] = true;
	}
	let answer = [];
	for (let key in answerDict){
		answer.push(key.split('_'));
	}
	return answer;
}
function twoSum(nums, target, skip) {
	let twoSumDict = {};
	let answer = [];
	for (let i = 0; i < nums.length; i++){
		twoSumDict[nums[i].toString()] = i;
	}
	for (let i = 0; i < nums.length; i++){
		if(typeof twoSumDict[(target - nums[i]).toString()] !== 'undefined' && 	twoSumDict[i] > i && i !== skip && twoSumDict[i] !== skip) {
			answer.push(nums[i]);
		}
	}
	return answer;
}
```

后来面试官提醒我正负数，我说确实必须要有正有负才能凑出 0（三个 0 例外），我说可以分成正负两个数组，能提升性能、降低常数，但我补充道复杂度似乎不变。

#### 问答题汇总

问答题都比较基础，并且比较开放。这里不给出我的解答。这次问答基本都答上来了。

1.  事件代理（没答上来）
2.  HTTP 状态码
3.  缓存 Expires、E-Tag、Cache-Conrtol 的区别
4.  常见攻击类型
5.  XSS 防范
6.  跨域防范
7.  HTTPS
8.  对称加密和非对称加密
9.  HTTP2
10.  用过什么前端框架？（没答上来）
11.  TypeScript 工作原理
12.  性能优化
13.  冒泡、如何阻止继续冒泡
14.  聊了聊之前做过的项目

这次一共一个半小时左右，挂电话前，面试官告诉我一面就给我过了，让我等二面。

后来 PiEgg 跟我说我是他推荐的第一个过一面的，有点小开心。然后看到了他之前分享的面试经验里有第二题链式调用，早知道早点问问了，现在想起来有点不甘心。

### 技术第二次面试

第二次是组长面的。感觉组长很 Friendly，以我自我介绍开始，说到哪里可以展开了，他就打断，深入探讨。我第一次挂掉的面试评语也是他给我念的。他最常说的是 “嗯，这个项目还是蛮有意思的。” 、“既然你说到了……，那我考考你啊……” 以及 “你还做过什么有意思的东西？”。

中间讲项目的时候聊到了很多知识点，基本都是我说到哪里他问到哪里，不问我没说过的，也没有故意难为我，这里列举一下：

1.  REST API 鉴权，Cookie 原理
2.  WebView 与安卓互通原理
3.  通过本地监听 HTTP 服务器实现互通，如何实现同步请求（没答上来）
4.  安卓端通知推送
5.  网页端通知推送
6.  Application Cache 实现原理，具体机制
7.  Application Cache 有哪些问题，为什么没流行起来
8.  数据存储，数据冲突
9.  遇到过哪些反爬虫技术
10.  爬虫如何通过图形验证码认证

一共聊了半小时，是最开心的一次面试，面试完就觉得自己稳了。果然，很快，一面的面试官就发微信告诉我过了，准备等三面。

### 中期准备

一开始那边通知我说预计三面会安排在周五。我一看还有两天时间，打算趁这两天提升一下自己，就开始学 Vue.js，这样三面的时候问我会什么前端框架，我还能答上来。当时买了本刘汉伟的《Vue.js 从入门到项目实战》。但最终第三次面试提前了一天，我就只看完了重点的部分。若说有什么用，也不过就是提升了一点自信，第三次面试时并没有真正考到这一点。

### 技术第三次面试

第三次技术面试和第一次流程类似，也是先做一小时编程题，然后电话问答。不过这次面试题更偏向算法层面，而不是 JavaScript 语言层面。就连编程语言都是可以自选的，以至于我前两道题选的 C++，秀了一手多态。

编程一共两道必做题，一道加分题，限时 60min。

#### 编程第一题：常规算法题

##### 环形队列

```
一、环形缓冲区，又称为环形队列，是一种定长为 N 的先进先出的数据结构。它在进程间的异步数据传输或记录日志文件时十分有用。环形缓冲区通常有一个读指针和一个写指针，读指针指向环形缓冲区中可读的数据，写指针指向环形缓冲区中可写的缓冲区，通过移动读指针和写指针就可以实现缓冲区的数据读取和写入。
请用数组实现一个环形缓冲区，支持以下操作：
1. 环形缓冲区初始化时支持指定缓冲区最大容量
2. int take(): 从缓冲区读指针位置读取数据（总为正数），如果没有数据可读返回 -1
3. boolean put(int value): 写入数据（总为正数）到缓冲区，容量不足无法写入返回 false，写入成功返回 true
```

基础题，我也不知道当时为何不用 STL 的 `vector`。可能是为了秀秀自己的 C++ 基础语法（构造函数、析构函数、深拷贝的重载）？我当时的解答如下：

```
template <class T>
class Queue { // T must can compare with int and can cast from int
public:
	Queue(int len=20);
	~Queue();
	T take();
	bool put(T value);
	// Deep copy
	Queue(const Queue & queue1);
	Queue & operator= (const Queue & queue1);
protected:
	int size{}; // Maximum Size
	int length{};
	T * queue{};
	int begin{};
	int end{};
};
template <class T> Queue<T>::Queue(int len) {
	begin = end = length = 0;
	size = len;
	if(len < 1) {
		throw "Error size";
	}
	queue = new int[len];
	for (int i = 0; i < len; ++i) {
		queue[i] = -1;
	}
}
template <class T> Queue<T>::~Queue() {
	delete [] queue;
}
template <class T> T Queue<T>::take() {
	if(length == 0) {
		return -1;
	}
	int index = (size + begin) % size;
	begin = (begin + 1) % size;
	length--;
	return queue[index];
}
template <class T> bool Queue<T>::put(T value) {
	if(value <= 0) {
		throw "Error put value size";
	}
	if(length == size) {
		return false;
	}
	length++;
	queue[end] = value;
	end = (end + 1) % size;
	return true;
}
template <class T> Queue<T>::Queue(const Queue<T> &queue1) {
	this->operator=(queue1); // Use the Queue::operator= explicitly
}
template <class T> Queue<T> & Queue<T>::operator=(const Queue<T> & queue1) {
	if(this == &queue1){
		return *this;
	}
	this->size = queue1.size;
	this->length = queue1.length;
	this->begin = queue1.size;
	this->end = queue1.size;
	// cout << "Copy " << size << endl;
	queue = new int[size];
	for (int i = 0; i < size; ++i) {
		queue[i] = queue1.queue[i];
	}
	return *this;
}
```

事实证明学校的 “数据结构与算法” 这门课并没有白学：环形队列这个东西在我自学算法的时候没有学到，即便有，我可能也不屑于学。但之后面试官的这个问题，如果没专门学过环形队列，可能就答不上来了：

```
追问：你这里是使用 length 变量来判断队列是否满的，请问有没有别的方法？
```

我当时的解答：由于表空和表满均是 `begin == end`，所以不能直接通过这个条件判断。可以使用一个 `bool` 类型的 `full` 变量，标记是否为满。或者在创建数组的时候多创建一位，表满时有一位是空的。

```
追问：你的 C++ 基础不错，说说计算机内存中的堆和栈的区别。
```

一开始我以为是数据结构，之后在意识到这是计算机底层实现的问题。然而我已经混淆堆和栈了，我就说一个是存储 `new` 创建的变量的，一个是存储局部变量的。全局变量和 `static` 变量存在另一个地方。

#### 编程第二题：常规算法题

##### LRU 缓存

```
二、设计和实现一个 LRU（最近最少使用）缓存机制，支持以下操作：
1. LRUCache 初始化时支持指定缓存最大容量
2. 获取数据 int get(int key)：如果 key 存在于缓存中，则返回缓存值（总为正数），没有对应缓存则返回 -1
3. 设置数据 void put(int key, int value): 如果 key 不存在，写入数据值。当缓存容量达到上限时，需淘汰最近最少使用的缓存。
```

我当时复用了第一题的类，但写完后发现可能错了，但也没改。当时解答如下：

```
#include <unordered_map>
template <class K, class V>
class LRUCache { // K must can compare with int and can cast from int
public:
	LRUCache(int len=20): queue(len){};
	V get(K key);
	void put(K key, V value);
protected:
	Queue<K> queue;
	std::unordered_map<K, V> table;
};
template<class K, class V>
V LRUCache<K, V>::get(K key) {
	auto it = table.find(key);
	if(it != it.end()) {
		return it->second;
	}
	return -1;
}
template<class K, class V>
void LRUCache<K, V>::put(K key, V value) {
	auto it = table.find(key);
	if(it == it.end()) { // Not find. Then it is a new key
		int check = queue.put(key);
		if(!check) { // Queue is full
			K pop = queue.take();
			table.erase(pop); // Remove the oldest one
		}
		check = queue.put(key);
	}
	table[key] = value;
}
```

#### 编程第三题：附加题

##### 可信域名列表

```
三、（加分题）有一个可信域名列表配置，如\['http://mp.weixin.qq.com', 'https://\*.xx.com'\]
//本题只考虑通配符为 \* 的情况
实现函数
function isValidDomain(url, allowPort) {
    return boolean
}
判断输入的url是否为可信域名，allowPort为true时，url中可以带端口
```

一开始理解错了，最终我修改后的解答如下：

```
let urls = ['https://*.qq.com'];
function isValidDomain(url, allowPort) {
	let result = true;
	let wildcard = "[a-zA-Z0-9-.]*";
	for (let i = 0; i < urls.length; i++) {
		let str = urls[i].replace("*", wildcard);
		let append = "";
		if (allowPort) {
			append = "(:[0-9]{1,5})?"; //
		}
		let re = new RegExp("^" + str + append + "($|/)");
		if(!re.test(url)) {
			result = false;
		}
	}
	return result;
}
// https://example.com/.qq.com -> false
// https://sub.qq.com -> true
// https://sub1.sub2.qq.com -> true
// https://www.qq.com -> true
// https://www.qq.coma -> false
// https://www.qq.com/ -> true
// https://www.qq.com/path -> true
```
#### 问答题汇总

1.  用 C++ 写过什么项目？
2.  `iframe` 工作原理
3.  简述 JavaScript 的闭包
4.  说说你刚才提到的闭包使用不当造成的 “内存泄漏”

这里面试交流的感觉不如第二次面试顺畅，自我介绍的时候尤其尴尬。而且我也无法从他的语气中听出来我能不能过。不过第二天（周五）下午，还是收到了面试通过的通知。

### HR 面试

HR 面试问的问题都是常规问题，具体如下：

1.  自我介绍一下你自己
2.  说说他人眼中的你
3.  你自己的缺点
4.  说说自己团队合作的经历
5.  总结一下三次技术面试自己的表现
6.  为什么选择转学
7.  要不要读研
8.  要不要留美
9.  工作地点在广州，是否方便
10.  毕业后能否接受异地工作

### 最终结果

当天晚上收到通知，通过实习面试，短信发放了 Offer。

![不知道是什么的 ???](https://imagedelivery.net/6T-behmofKYLsxlrK0l_MQ/7880dae6-f2a7-4abf-30f0-7c718d2fcf00/large)

## 前端学习推荐

前端需要掌握最基础的 HTML、CSS、JavaScript。请问这三个中哪个是编程语言？回答正确，是 JavaScript 没错（HTML、CSS 不配拥有姓名）。HTML 和 CSS 相对简单，不会有人不会吧？不会的话就多看看现有网站和项目，然后遇到不懂的就去 W3C 或者 Mozilla 上查一查文档就好（确信）。这两个也不是主要考点，重点还是 JavaScript。

JavaScript 入门推荐 Jeremy Keith 的《JavaScript DOM 编程艺术》，原版名称是 _DOM Scripting: Web Design With Javascript and the Document Object Model_。这本书不但讲了使用 JavaScript 操作原生 DOM，也讲了 JavaScript 的基本语法。

一般我用的 IDE 是 PhpStorm，因为它包含了 WebStorm 的所有功能，此外还能写 PHP。当然不写 PHP 的话 WebStorm 就好，价格还更便宜。（没错，我经常 HTML 和 PHP 混在一起写，高度耦合，请见谅）

其实学好前端还是很有用的，在学习前端过程中最有用的就是你浏览器中的 Console（F12）。你可以写写 JavaScript 脚本，上网课实现自动翻页，播放视频改 16 倍速什么的（逃），通过这个你就学会了 CSS 选择器和循环什么的。再分析分析浏览器 Console 中 Networks 标签页的各个请求，筛选有效请求然后再模拟发请求，一键刷课，岂不美滋滋（别跟别人说这是我教的）。这个过程还能学会 HTTP、Cookie、Session、XMLHttpRequest 等相关技术，JSON、URL encode 等各种编码基础，还是很实用的。

最重要的还是多多实践，可以自己做个网站，一次性踩遍域名、DNS、HTTPS 证书、HTTP 服务器等各种坑。此外还可以学学[微信小程序](https://developers.weixin.qq.com/miniprogram/dev/framework/)，和前端技术很相近，上述所有坑都能跳过不踩，更容易上手，更容易传播推广（这句话是广告）。
