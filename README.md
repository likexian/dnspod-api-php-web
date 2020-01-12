# DNSPod API PHP Web 示例

[![License](https://img.shields.io/badge/license-Apache%202.0-blue.svg)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/likexian/dnspod-web.svg)](https://packagist.org/packages/likexian/dnspod-web)

## 功能说明

用 PHP 实现了一个 DNSPod API 的 Web 示例，已完成对域名和记录的基本操作，可直接使用。

*已调整为只支持通过 Token 登录，请到 DNSPod 用户中心创建 API Token 获取 Token ID 及 Token Key。*

功能包括：
- 用户登录
- 域名列表
- 域名暂停/启用
- 域名添加
- 域名删除
- 记录列表
- 记录暂停/启用
- 记录添加
- 记录修改
- 记录删除

## 环境要求

- PHP5.x/7.x
- SESSION
- CURL

## 安装说明

直接下载放到网站的任何目录，然后在浏览器打开即可查看示例。

您还可以通过 composer 来安装它：

    $ composer create-project likexian/dnspod-web

## DEMO

请打开 [demo](demo) 目录查看相关截图。

## LICENSE

Copyright 2011-2020 Li Kexian

Licensed under the Apache License 2.0

## About

- [Li Kexian](https://www.likexian.com/)
