Validator
======

字段验证辅助工具

# 简介

字段进行简单的验证，目前支持的类型如下.

type， 具体类型包括，string，array，email，url，date，date支持闰年的验证.
require， 检查是否为空.
min_length, 最少字符.
max_length, 最多字符.
regexp, 正则匹配.

# 测试用例

字段验证正常

```
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'bruce', 'type'=> 'string', 'min_length' => 5);
$fields[] = array('field' => 'date', 'value'=> '2006-1-1 00:00:00', 'type'=> 'date');
$validation = Validator::validate($fields);

// 查看结果
$validation->isValid()
```
字段验证失败

```
$fields = array();
$fields[] = array('field' => 'name', 'value'=> 'bruce', 'type'=> 'string', 'min_length' => 6);
$fields[] = array('field' => 'date', 'value'=> '2006-1-1 00:00:00', 'type'=> 'date');
$validation = Validator::validate($fields);

if (!validation->isValid()) {
    $errors = $validation->errors();
}
```

# 总结

使用简单，只需调用一个函数就可以进行简单的字段验证，尤其适用于表单验证。
