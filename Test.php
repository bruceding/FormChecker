<?php
require 'FormCheck.php';

// 测试type 操作 默认返回json
$fields = array();
$fields[] = array('field' => 'name', 'value'=> array(1), 'type'=> 'string');
$res = FormCheck::check($fields);
print_r($res);

// 测试正常返回
$fields = array();
$fields[] = array('field' => 'name', 'value'=> array(1), 'type'=> 'array');

$res = FormCheck::check($fields);
print_r($res);

// 测试 email 类型
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'demo@gmail.com', 'type'=> 'email');

$res = FormCheck::check($fields);
print_r($res);

// 测试 url 类型
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'http://www.google.com?q=hi', 'type'=> 'url');

$res = FormCheck::check($fields);
print_r($res);

// 测试 date 类型
$fields = array();
$fields[] = array('field' => 'name', 'value'=> '2006-1-1 00:00:00', 'type'=> 'date');

$res = FormCheck::check($fields);
print_r($res);

// 测试异常抛出
$fields = array();
$fields[] = array('field' => 'name', 'value'=> array(1), 'type'=> 'string');

try {
    $res = FormCheck::check($fields, FormCheck::RETURN_TYPE_EXCEPTION);
} catch (Exception $e) {
    print_r($e->getMessage());
}

// 测试require 操作
$fields = array();
$fields[] = array('field' => 'name', 'value'=> array(1), 'require' => true, 'type' => 'string');

$res = FormCheck::check($fields);
print_r($res);

// 测试min_length
$fields = array();
$fields[] = array('field' => 'name', 'value'=> '丁','min_length' => 6 );

$res = FormCheck::check($fields);
print_r($res);

// 测试max_length
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'bruce','max_length' => 6 );

$res = FormCheck::check($fields);
print_r($res);

// 测试regexp
$fields = array();
$fields[] = array('field' => 'name', 'value'=> '123','regexp' => '/^\d{3}$/', 'errmsg' => '格式不正确');

$res = FormCheck::check($fields);
print_r($res);
