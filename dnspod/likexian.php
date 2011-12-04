<?php
/*
 * DNSPod API PHP Web 示例
 * http://www.zhetenga.com/
 *
 * Copyright 2011, Kexian Li
 * Released under the MIT, BSD, and GPL Licenses.
 *
 */

$head = <<<TPL
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DNSPod API PHP Web 示例-李院长</title>
<style type="text/css">
body {
	background: #fff;
	color: #000;
	font: font:13px 'Helvetica Neue',Arial,Sans-serif;
	margin:30px;
	padding:0;
}
a {
	color: #133DB6;
	text-decoration:none;
}
a:hover {
	color: #133DB6;
	text-decoration:underline;
}
#likexian_box {
	margin: auto;
	width: 800px;
}
</style>
</head>
<body>
<div id="likexian_box">
TPL;

$foot = <<<TPL
</div>
</body>
</html>
TPL;

$login_form =<<<TPL
$head
<form name="login" method="post" action="?action=domainlist">
<div>账号：<input type="text" name="login_email" /></div>
<div>密码：<input type="password" name="login_password" /></div>
<div><input type="submit" value="登录" /></div>
</form>
$foot
TPL;

$domain_list = <<<TPL
$head
<form name="login" method="post" action="?action=domaincreate">
<div>域名：<input type="text" name="domain" /><input type="submit" value="添加" /></div>
</form>
<table cellspacing="0" cellpadding="5" border="1" width="100%">
	<tr>
		<th>编号</th><th>域名</th><th>等级</th><th>状态</th><th>扩展状态</th><th>记录</th><th>星标</th><th>更新</th><th>操作</th>
	</tr>
	{domain_list}
</table>
$foot
TPL;

$record_list = <<<TPL
$head
<div><a href="?action=domainlist">域名管理</a></div>
<table cellspacing="0" cellpadding="5" border="1" width="100%">
	<tr>
		<th>编号</th><th>子域名</th><th>类型</th><th>线路</th><th>记录</th><th>状态</th><th>MX</th><th>TTL</th><th>操作</th>
	</tr>
	{record_list}
</table>
$foot
TPL;

