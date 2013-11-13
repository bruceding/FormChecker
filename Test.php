<?php
require 'FormCheck.php';

$fields = array();

$fields[] = array('field' => 'name', 'value'=> array(1), 'type'=> 'string');

$res = FormCheck::check($fields);

print_r($res);
