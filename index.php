<?php
/*
 * Copyright 2011-2019 Li Kexian
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * DNSPod API PHP Web 示例
 * https://www.likexian.com/
 */

error_reporting(0);
header('Content-type:text/html; charset=utf-8');

require './dnspod.php';
$dnspod = new dnspod();
@session_start();

if ($_GET['action'] == 'domainlist') {
    if ($_POST['token_id'] == '') {
        if ($_SESSION['token_id'] == '') {
            $dnspod->message('danger', '请输入Token ID。', -1);
        }
    } else {
        $_SESSION['token_id'] = $_POST['token_id'];
    }

    if ($_POST['token_key'] == '') {
        if ($_SESSION['token_key'] == '') {
            $dnspod->message('danger', '请输入Token Key。', -1);
        }
    } else {
        $_SESSION['token_key'] = $_POST['token_key'];
    }

    $response = $dnspod->api_call('Domain.List', array());

    $list = '';
    $domain_sub = file_get_contents('./template/domain_sub.html');
    foreach ($response['domains'] as $id => $domain) {
        $list_sub = str_replace('{{id}}', $domain['id'], $domain_sub);
        $list_sub = str_replace('{{domain}}', $domain['name'], $list_sub);
        $list_sub = str_replace('{{grade}}', $dnspod->grade_list[$domain['grade']], $list_sub);
        $list_sub = str_replace('{{status}}', $dnspod->status_list[$domain['status']], $list_sub);
        $list_sub = str_replace('{{status_new}}', $domain['status'] == 'pause' ? 'enable' : 'disable', $list_sub);
        $list_sub = str_replace('{{status_text}}', $domain['status'] == 'pause' ? '启用' : '暂停', $list_sub);
        $list_sub = str_replace('{{records}}', $domain['records'], $list_sub);
        $list_sub = str_replace('{{updated_on}}', $domain['updated_on'], $list_sub);
        $list .= $list_sub;
    }

    $text = $dnspod->get_template('domain');
    $text = str_replace('{{title}}', '域名列表', $text);
    $text = str_replace('{{list}}', $list, $text);
} elseif ($_GET['action'] == 'domaincreate') {
    if ($_POST['domain'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }

    $response = $dnspod->api_call('Domain.Create', array('domain' => $_POST['domain']));

    $dnspod->message('success', '添加成功。', '?action=domainlist');
} elseif ($_GET['action'] == 'domainstatus') {
    if ($_GET['domain_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }
    if ($_GET['status'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }

    $response = $dnspod->api_call('Domain.Status', array('domain_id' => $_GET['domain_id'], 'status' => $_GET['status']));

    $dnspod->message('success', ($_GET['status'] == 'enable' ? '启用' : '暂停') . '成功。', '?action=domainlist');
} elseif ($_GET['action'] == 'domainremove') {
    if ($_GET['domain_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }

    $response = $dnspod->api_call('Domain.Remove', array('domain_id' => $_GET['domain_id']));

    $dnspod->message('success', '删除成功。', '?action=domainlist');
} elseif ($_GET['action'] == 'recordlist') {
    if ($_GET['domain_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }

    $response = $dnspod->api_call('Record.List', array('domain_id' => $_GET['domain_id']));
    $list = '';
    $record_sub = file_get_contents('./template/record_sub.html');
    foreach ($response['records'] as $id => $record) {
        $list_sub = str_replace('{{domain_id}}', $_GET['domain_id'], $record_sub);
        $list_sub = str_replace('{{id}}', $record['id'], $list_sub);
        $list_sub = str_replace('{{name}}', $record['name'], $list_sub);
        $list_sub = str_replace('{{value}}', $record['value'], $list_sub);
        $list_sub = str_replace('{{type}}', $record['type'], $list_sub);
        $list_sub = str_replace('{{line}}', $record['line'], $list_sub);
        $list_sub = str_replace('{{enabled}}', $record['enabled'] ? '启用' : '暂停', $list_sub);
        $list_sub = str_replace('{{status_new}}', $record['enabled'] ? 'disable' : 'enable', $list_sub);
        $list_sub = str_replace('{{status_text}}', $record['enabled'] ? '暂停' : '启用', $list_sub);
        $list_sub = str_replace('{{mx}}', $record['mx'] ? $record['mx'] : '-', $list_sub);
        $list_sub = str_replace('{{ttl}}', $record['ttl'], $list_sub);
        $list .= $list_sub;
    }

    $text = $dnspod->get_template('record');
    $text = str_replace('{{title}}', '记录列表 - ' . $response['domain']['name'], $text);
    $text = str_replace('{{list}}', $list, $text);
    $text = str_replace('{{domain_id}}', $response['domain']['id'], $text);
    $text = str_replace('{{grade}}', $response['domain']['grade'], $text);
} elseif ($_GET['action'] == 'recordcreatef') {
    if ($_GET['domain_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }

    if (!$_SESSION['type_' . $_GET['grade']]) {
        $response = $dnspod->api_call('Record.Type', array('domain_grade' => $_GET['grade']));
        $_SESSION['type_' . $_GET['grade']] = $response['types'];
    }

    if (!$_SESSION['line_' . $_GET['grade']]) {
        $response = $dnspod->api_call('Record.Line', array('domain_grade' => $_GET['grade']));
        $_SESSION['line_' . $_GET['grade']] = $response['lines'];
    }

    $type_list = '';
    foreach ($_SESSION['type_' . $_GET['grade']] as $key => $value) {
        $type_list .= '<option value="' . $value . '">' . $value . '</option>';
    }

    $line_list = '';
    foreach ($_SESSION['line_' . $_GET['grade']] as $key => $value) {
        $line_list .= '<option value="' . $value . '">' . $value . '</option>';
    }

    $text = $dnspod->get_template('recordcreatef');
    $text = str_replace('{{title}}', '添加记录', $text);
    $text = str_replace('{{action}}', 'recordcreate', $text);
    $text = str_replace('{{domain_id}}', $_GET['domain_id'], $text);
    $text = str_replace('{{record_id}}', $_GET['record_id'], $text);
    $text = str_replace('{{type_list}}', $type_list, $text);
    $text = str_replace('{{line_list}}', $line_list, $text);
    $text = str_replace('{{sub_domain}}', '', $text);
    $text = str_replace('{{value}}', '', $text);
    $text = str_replace('{{mx}}', '10', $text);
    $text = str_replace('{{ttl}}', '600', $text);
} elseif ($_GET['action'] == 'recordcreate') {
    if ($_GET['domain_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }

    if (!$_POST['sub_domain']) {
        $_POST['sub_domain'] = '@';
    }

    if (!$_POST['value']) {
        $dnspod->message('danger', '请输入记录值。', -1);
    }

    if ($_POST['type'] == 'MX' && !$_POST['mx']) {
        $_POST['mx'] = 10;
    }

    if (!$_POST['ttl']) {
        $_POST['ttl'] = 600;
    }

    $response = $dnspod->api_call('Record.Create',
        array('domain_id' => $_GET['domain_id'],
            'sub_domain' => $_POST['sub_domain'],
            'record_type' => $_POST['type'],
            'record_line' => $_POST['line'],
            'value' => $_POST['value'],
            'mx' => $_POST['mx'],
            'ttl' => $_POST['ttl'],
        )
    );

    $dnspod->message('success', '添加成功。', '?action=recordlist&domain_id=' . $_GET['domain_id']);
} elseif ($_GET['action'] == 'recordeditf') {
    if ($_GET['domain_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }
    if ($_GET['record_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }

    $response = $dnspod->api_call('Record.Info', array('domain_id' => $_GET['domain_id'], 'record_id' => $_GET['record_id']));
    $record = $response['record'];

    if (!$_SESSION['type_' . $_GET['grade']]) {
        $response = $dnspod->api_call('Record.Type', array('domain_grade' => $_GET['grade']));
        $_SESSION['type_' . $_GET['grade']] = $response['types'];
    }

    if (!$_SESSION['line_' . $_GET['grade']]) {
        $response = $dnspod->api_call('Record.Line', array('domain_grade' => $_GET['grade'],'domain_id' => $_GET['domain_id']));
        $_SESSION['line_' . $_GET['grade']] = $response['lines'];
    }

    $type_list = '';
    foreach ($_SESSION['type_' . $_GET['grade']] as $key => $value) {
        $type_list .= '<option value="' . $value . '" ' . ($record['record_type'] == $value ? 'selected="selected"' : '') . '>' . $value . '</option>';
    }

    $line_list = '';
    foreach ($_SESSION['line_' . $_GET['grade']] as $key => $value) {
        $line_list .= '<option value="' . $value . '" ' . ($record['record_line'] == $value ? 'selected="selected"' : '') . '>' . $value . '</option>';
    }

    $text = $dnspod->get_template('recordcreatef');
    $text = str_replace('{{title}}', '修改记录', $text);
    $text = str_replace('{{action}}', 'recordedit', $text);
    $text = str_replace('{{domain_id}}', $_GET['domain_id'], $text);
    $text = str_replace('{{record_id}}', $_GET['record_id'], $text);
    $text = str_replace('{{type_list}}', $type_list, $text);
    $text = str_replace('{{line_list}}', $line_list, $text);
    $text = str_replace('{{sub_domain}}', $record['sub_domain'], $text);
    $text = str_replace('{{value}}', $record['value'], $text);
    $text = str_replace('{{mx}}', $record['mx'], $text);
    $text = str_replace('{{ttl}}', $record['ttl'], $text);
} elseif ($_GET['action'] == 'recordedit') {
    if ($_GET['domain_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }
    if ($_GET['record_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }

    if (!$_POST['sub_domain']) {
        $_POST['sub_domain'] = '@';
    }

    if (!$_POST['value']) {
        $dnspod->message('danger', '请输入记录值。', -1);
    }

    if ($_POST['type'] == 'MX' && !$_POST['mx']) {
        $_POST['mx'] = 10;
    }

    if (!$_POST['ttl']) {
        $_POST['ttl'] = 600;
    }

    $response = $dnspod->api_call('Record.Modify',
        array('domain_id' => $_GET['domain_id'],
            'record_id' => $_GET['record_id'],
            'sub_domain' => $_POST['sub_domain'],
            'record_type' => $_POST['type'],
            'record_line' => $_POST['line'],
            'value' => $_POST['value'],
            'mx' => $_POST['mx'],
            'ttl' => $_POST['ttl'],
        )
    );

    $dnspod->message('success', '修改成功。', '?action=recordlist&domain_id=' . $_GET['domain_id']);
} elseif ($_GET['action'] == 'recordremove') {
    if ($_GET['domain_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }
    if ($_GET['record_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }

    $response = $dnspod->api_call('Record.Remove',
        array('domain_id' => $_GET['domain_id'],
            'record_id' => $_GET['record_id'],
        )
    );

    $dnspod->message('success', '删除成功。', '?action=recordlist&domain_id=' . $_GET['domain_id']);
} elseif ($_GET['action'] == 'recordstatus') {
    if ($_GET['domain_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }
    if ($_GET['record_id'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }
    if ($_GET['status'] == '') {
        $dnspod->message('danger', '参数错误。', -1);
    }

    $response = $dnspod->api_call('Record.Status',
        array('domain_id' => $_GET['domain_id'],
            'record_id' => $_GET['record_id'],
            'status' => $_GET['status'],
        )
    );

    $dnspod->message('success', ($_GET['status'] == 'enable' ? '启用' : '暂停') . '成功。', '?action=recordlist&domain_id=' . $_GET['domain_id']);
} else {
    $text = $dnspod->get_template('login');
    $text = str_replace('{{title}}', '用户登录', $text);
}

echo $text;
