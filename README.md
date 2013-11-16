FormCheck
======

字段验证辅助工具

# 简介

字段进行简单的验证，目前支持的类型有:
type， 具体类型包括，string，array，email，url，date，date支持闰年的验证;
require， 检查是否为空;
min_length, 最少字符;
max_length, 最多字符;
regexp, 正则匹配。

函数返回包括两种方式，json和抛出异常。

# 测试用例

字段验证正常

`<?php
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'bruce', 'type'=> 'string', 'min_length' => 5);
$fields[] = array('field' => 'date', 'value'=> '2006-1-1 00:00:00', 'type'=> 'date');
$res = FormCheck::check($fields);
print_r($res);
`

`
Array
(
    [errcode] => 0
    [errmsg] => ok
)
`
字段验证失败

`
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'bruce', 'type'=> 'string', 'min_length' => 6);
$fields[] = array('field' => 'date', 'value'=> '2006-1-1 00:00:00', 'type'=> 'date');
$res = FormCheck::check($fields);
print_r($res);
`

`
Array
(
    [errcode] => 3
    [errmsg] => name's length at least 6 length
)
`

验证失败时，还可以选择用异常形式返回

`$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'bruce', 'type'=> 'string', 'min_length' => 6, 'errmsg' => '失败时可以指定errmsg返回消息');
FormCheck::check($fields, FormCheck::RETURN_TYPE_EXCEPTION);
`
# 总结

使用简单，只需调用一个函数就可以进行简单的字段验证，尤其适用于表单验证。
