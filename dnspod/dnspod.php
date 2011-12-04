<?php
/*
 * DNSPod API PHP Web 示例
 * http://www.zhetenga.com/
 *
 * Copyright 2011, Kexian Li
 * Released under the MIT, BSD, and GPL Licenses.
 *
 */

class dnspod {
	public function api_call($api, $data) {
		if ($api == '' || !is_array($data)) {
			exit('内部错误：参数错误');
		}
		
		$api = 'https://dnsapi.cn/' . $api;
		$data = array_merge($data, array('login_email' => $_SESSION['login_email'], 'login_password' => $_SESSION['login_password'], 'format' => 'json', 'lang' => 'cn', 'error_on_empty' => 'no'));
		
		$result = $this->post_data($api, $data);
		if (!$result) {
			exit('内部错误：调用失败');
		}
		
		$results = @json_decode($result, 1);
		if (!is_array($results)) {
			exit('内部错误：返回错误');
		}
		
		if ($results['status']['code'] != 1) {
			exit($results['status']['message']);
		}
		
		return $results;
	}
	
	private function post_data($url, $data) {
		if ($url == '' || !is_array($data)) {
			return false;
		}
		
		$ch = @curl_init();
		if (!$ch) {
			exit('内部错误：服务器不支持CURL');
		}
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_USERAGENT, 'DNSPod API PHP Web Client/0.1 (shallwedance@126.com)');
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}
}

