<?php
/*
 * DNSPod API PHP Web 示例
 * http://www.zhetenga.com/
 *
 * Copyright 2011, Kexian Li
 * Released under the MIT, BSD, and GPL Licenses.
 *
 */

error_reporting(0);
header('Content-type:text/html; charset=utf-8');

require './dnspod/dnspod.php';
require './dnspod/likexian.php';

$dnspod = new dnspod();
@session_start();

if ($_GET['action'] == 'domainlist') {
	if ($_POST['login_email'] == '') {
		if ($_SESSION['login_email'] == '') {
			exit('请输入登录账号。');
		}
	} else {
		$_SESSION['login_email'] = $_POST['login_email'];
	}
	
	if ($_POST['login_password'] == '') {
		if ($_SESSION['login_password'] == '') {
			exit('请输入登录密码。');
		}
	} else {
		$_SESSION['login_password'] = $_POST['login_password'];
	}
	
	$response = $dnspod->api_call('Domain.List', array());
	
	foreach ($response['domains'] as $id => $domain) {
		$list .= "<tr><td>{$domain['id']}</td><td>{$domain['name']}</td><td>{$domain['grade']}</td><td>{$domain['status']}</td><td>{$domain['ext_status']}</td><td>{$domain['records']}</td><td>{$domain['is_mark']}</td><td>{$domain['updated_on']}</td><td><a href='?action=recordlist&domain_id={$domain['id']}'>记录</a> <a href='?action=domainremove&domain_id={$domain['id']}'>删除</a></td></tr>";
	}
	
	echo str_replace('{domain_list}', $list, $domain_list);
} elseif ($_GET['action'] == 'domaincreate') {
	if ($_POST['domain'] == '') {
		exit('参数错误。');
	}
	
	$response = $dnspod->api_call('Domain.Create', array('domain' => $_POST['domain']));
	
	exit('添加成功，<a href="?action=domainlist">点击返回</a>。');
} elseif ($_GET['action'] == 'domainremove') {
	if ($_GET['domain_id'] == '') {
		exit('参数错误。');
	}
	
	$response = $dnspod->api_call('Domain.Remove', array('domain_id' => $_GET['domain_id']));
	
	exit('删除成功，<a href="?action=domainlist">点击返回</a>。');
} elseif ($_GET['action'] == 'recordlist') {
	if ($_GET['domain_id'] == '') {
		exit('参数错误。');
	}
	
	$response = $dnspod->api_call('Record.List', array('domain_id' => $_GET['domain_id']));
	
	foreach ($response['records'] as $id => $record) {
		$list .= "<tr><td>{$record['id']}</td><td>{$record['name']}</td><td>{$record['type']}</td><td>{$record['line']}</td><td>{$record['value']}</td><td>{$record['enabled']}</td><td>{$record['mx']}</td><td>{$record['ttl']}</td><td></td></tr>";
	}
	
	echo str_replace('{record_list}', $list, $record_list);
} else {
	echo $login_form;
}

