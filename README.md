# 微精弘

> 微精弘是是碎片化、一站式、一体化校园移动门户，适用于浙江工业大学学生，集课表、成绩、考试、空教室、一卡通、借阅等功能于一身。
>
> 微精弘有更好用的课表查询，本周视图、学期视图、对应日期及时钟轴，清晰明了。
>
> 微精弘有更方便的考试安排，帮你折算出考试周对应的日期，以及考试时间倒计时，一目了然。
>
> 微精弘还是校园生活助手与管家，一卡通余额及消费一览无余，让同学们更合理安排生活费。微精弘更是/更有... （期待您的发现）

## 简述

本服务端大致上分为两个模块，`微信服务`和`微精弘服务`，一个是管理微信的消息处理服务，一个是微精弘主站的服务。其中微信服务采用第三方包[EasyWeChat](https://easywechat.org/zh-cn/docs/) 进行开发，此包几乎已集成微信端开发的所有功能，代码也合理，请放心使用，github仓库[点击这里](https://github.com/overtrue/laravel-wechat)。

同时包括模板消息的队列，采用Redis进行队列管理，Laravel已集成这方面的功能。

~~初步设想此次重构将mysql改为mongoDB，主要是以前被mysql坑过好多次，所以这次干脆试试新东西。~~
mongoDB不太稳定，同时对机器配置要求较高，最优的解决方法是使用两台机器，其中一台机器只部署mongoDB，但是目前并没有太多的服务器。

所以目前依然使用mysql，因为要用到json类型来实现扩展性，所以请务必使用mysql5.7以上的版本。

同时为了使用emoji，使用了[laravel-emoji](https://github.com/unicodeveloper/laravel-emoji)，所以务必使用PHP7进行开发，因为mongodb不支持utf8mb4所以要做一定的转换

## 框架

微精弘服务端使用Laravel作为框架，使用PHP可以减少入门门槛，方便集中开发人员。

Laravel是目前最优雅的PHP框架，目录结构清晰，使用composer进行包管理。

## 最佳实践

开发前请先阅读[最佳实践指南](https://zjutjh.gitbooks.io/document/content/1.3-Laravel/1.3.1-%E6%9C%80%E4%BD%B3%E5%AE%9E%E8%B7%B5.html)

## 开始开发
首先安装相关包
> composer install

复制.env.example的内容至.env（新建）
> cp .env.example .env

然后生成laravel应用的key
> php artisan key:generate

接下来配置好服务器输入相关域名就可以开始开发了ß
