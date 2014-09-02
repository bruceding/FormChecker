<?php
require 'Validator.php';

// 测试allow_fields 
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'bruce', 'valid' => true, 'passwd' => true);
$res = Validator::validate($fields);
var_dump($res);

// 测试type 操作 默认返回json
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'bruce', 'type'=> 'string', 'min_length' => 6);
$fields[] = array('field' => 'date', 'value'=> '2006-1-1 00:00:00', 'type'=> 'date');
$res = Validator::validate($fields);
print_r($res);

// 测试正常返回
$fields = array();
$fields[] = array('field' => 'name', 'value'=> array(1), 'type'=> 'array');

$res = Validator::validate($fields);
print_r($res);

// 测试 email 类型
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'demo@gmail.com', 'type'=> 'email', 'require' => true);
$res = Validator::validate($fields);
print_r($res);

// 测试 url 类型
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'http://www.google.com?q=hi', 'type'=> 'url');

$res = Validator::validate($fields);
print_r($res);

// 测试 date 类型
$fields = array();
$fields[] = array('field' => 'name', 'value'=> '2006-1-1 00:00:00', 'type'=> 'date');

$res = Validator::validate($fields);
print_r($res);

// 测试require 操作
$fields = array();
$fields[] = array('field' => 'name', 'value'=> array(1), 'require' => true, 'type' => 'string');

$res = Validator::validate($fields);
print_r($res);

// 测试min_length
$fields = array();
$fields[] = array('field' => 'name', 'value'=> '中国','min_length' => 6 );

$res = Validator::validate($fields);
print_r($res);

// 测试max_length
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'bruce','max_length' => 6 );

$res = Validator::validate($fields);
print_r($res);

// 测试regexp
$fields = array();
$fields[] = array('field' => 'name', 'value'=> '123','regexp' => '/^\d{3}$/', 'errmsg' => '格式不正确');

$res = Validator::validate($fields);
print_r($res);
