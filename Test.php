<?php
require 'FormCheck.php';

$fields = array();

$fields[] = array('field' => 'name', 'value'=> array(1), 'type'=> 'string');

// 测试type 操作 默认返回json
$res = FormCheck::check($fields);
print_r($res);

// 测试正常返回
$fields = array();
$fields[] = array('field' => 'name', 'value'=> array(1), 'type'=> 'array');

$res = FormCheck::check($fields);
print_r($res);

// 测试异常抛出
$fields = array();
$fields[] = array('field' => 'name', 'value'=> array(1), 'type'=> 'array');

$res = FormCheck::check($fields, FormCheck::RETURN_TYPE_EXCEPTION);
print_r($res);
