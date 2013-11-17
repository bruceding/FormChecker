<?php
/**
 * FormCheck 
 * 字段验证工具类 
 *
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author Tobias Schlitt <toby@php.net> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class FormCheck {

    /**
     *  返回类型：json数据 
     */
    const RETURN_TYPE_JSON = 1;
    /**
     * 返回类型：抛出异常 
     */
    const RETURN_TYPE_EXCEPTION = 2;

    /**
     * type 错误码 
     */
    const ERROR_CODE_TYPE = 1; 

    /**
     * require 错误码
     */
    const ERROR_CODE_REQUIRE = 2;

    /**
     * min_length 错误码 
     */
    const ERROR_CODE_MIN_LENGTH = 3; 

    /**
     * max_length 错误码 
     */
    const ERROR_CODE_MAX_LENGTH = 4;

    /**
     * regexp 是否匹配正则表达式
     */
    const ERROR_CODE_REGEXP = 5;

    /**
     * 允许的操作 
     */
    private static $_operate = array('require', 'min_length', 'max_length', 'type', 'regexp');

    /**
     * 允许的字段值 
     */
    private static $_allow_fields = array('require', 'min_length', 'max_length', 'type', 'regexp', 'field', 'type', 'value');

    /**
     * check 
     * 
     * @param array $fields  $fields = array(
     *                              array('field' => '', 'type'=> '', 'value'=> '', 'require' => true, 'errmsg' =>),
     *                              array('field' => '', 'type'=> '', 'value'=> '', 'min_length' => 8),
     *                              ) 
     * @param mixed $returnType 
     * @static
     * @access public
     * @return void
     */
    public static function check(array $fields, $returnType = self::RETURN_TYPE_JSON) {
        
        if (!$fields || !is_array($fields)) {
            throw new InvalidArgumentException('invalid fields');
        }
        foreach ($fields as $field) {
            if (!is_array($field)) {
                throw new InvalidArgumentException('invalid fields');
            } 

            if (!$field['field']) {
            
                throw new InvalidArgumentException('field is not empty');
            }

            // 检查key的交集
            $fieldsDiff = array_diff(array_keys($field), self::$_allow_fields);
            if ($fieldsDiff) {
                throw new InvalidArgumentException('fields:' . '\'' . join(',', $fieldsDiff) . '\'' . ' not allowed.');
            }

            foreach ($field as $key => $val) {
                if (in_array($key, self::$_operate)) {
                    list($errcode, $errmsg) = forward_static_call_array(array('FormCheck', '_'. $key), array($field));
                    if ($errcode) {
                        return self::_error($errcode,$errmsg, $field, $returnType);
                    }
                } 
            }
        }

        return array('errcode' => 0, 'errmsg' => 'ok');
    }

    /**
     * _type
     * type类型验证,包括string,array,email,url,date
     * 
     * @param mixed $field 
     * @static
     * @access public
     * @return void
     */
    public static function _type($field) {
        
        $errcode = 0;
        if (strtolower($field['type']) == 'string') {

            if (!is_string($field['value'])) {
                $errcode = self::ERROR_CODE_TYPE;
            }
        } else if (strtolower($field['type']) == 'array') {
        
            if (!is_array($field['value'])) {
                $errcode = self::ERROR_CODE_TYPE;
            }
        } else if (strtolower($field['type']) == 'email') {
            if (filter_var($field['value'], FILTER_VALIDATE_EMAIL) === false) {
                $errcode = self::ERROR_CODE_TYPE;
            }
        } else if (strtolower($field['type']) == 'url') {
            if (filter_var($field['value'], FILTER_VALIDATE_URL) === false) {
                $errcode = self::ERROR_CODE_TYPE;
            } 
        } else if (strtolower($field['type']) == 'date') {
            $dateArr = date_parse($field['value']);
            if ($dateArr && $dateArr['error_count'] === 0) {
                if (checkdate($dateArr['month'], $dateArr['day'], $dateArr['year']) === false) {
                    $errcode = self::ERROR_CODE_TYPE;
                }  
            } else {
                $errcode = self::ERROR_CODE_TYPE;
            }
        } else {
            throw new Exception('not support type');
        }

        if ($errcode) {
            $errmsg = "{$field['field']}'s type not right";
        }

        return array($errcode, $errmsg);
    }

    /**
     * _require
     * require 验证, 值为true时，会验证
     * 
     * @param mixed $field 
     * @static
     * @access private
     * @return void
     */
    private static function _require($field) {

        $errcode = 0;

        if ($field['require'] === true && !$field['value']) {
            $errcode = self::ERROR_CODE_REQUIRE;
            $errmsg = "{$field['field']}'s not empty";
        }

        return array($errcode, $errmsg);
    }

    /**
     * _min_length 
     * 判断value的最小长度，一个中文汉字相当于3个字符
     *
     * @param mixed $field 
     * @static
     * @access private
     * @return void
     */
    private static function _min_length($field) {
        
        if (strlen($field['value']) < $field['min_length']) {
            $errcode = self::ERROR_CODE_MIN_LENGTH;
            $errmsg = "{$field['field']}'s length at least {$field['min_length']} length"; 
        }

        return array($errcode, $errmsg);
    }

    /**
     * _max_length 
     * 最多字符判断
     * 
     * @param mixed $field 
     * @static
     * @access private
     * @return void
     */
    private static function _max_length($field) {
    
        if (strlen($field['value']) >  $field['max_length']) {
            $errcode = self::ERROR_CODE_MAX_LENGTH;
            $errmsg = "{$field['field']}'s length at most {$field['max_length']} length"; 
        }

        return array($errcode, $errmsg);
    }

    /**
     * _regexp
     * 正则表达式验证
     * 
     * @param mixed $field 
     * @static
     * @access private
     * @return void
     */
    private static function _regexp($field) {

        if (preg_match($field['regexp'], $field['value']) === 0) {
            $errcode = self::ERROR_CODE_REGEXP;
            $errmsg = "{$field['field']} not match {$field['regexp']} pattern";
        }

        return array($errcode, $errmsg);
    }

    /**
     * _error 
     * 错误处理
     * 
     * @param mixed $errcode 
     * @param mixed $errmsg 
     * @param mixed $field 
     * @param mixed $returnType 
     * @static
     * @access private
     * @return void
     */
    private static function _error($errcode, $errmsg, $field, $returnType) {

        $errmsg = $field['errmsg'] ? $field['errmsg'] : $errmsg;
        if ($returnType == self::RETURN_TYPE_JSON) {
            return array('errcode' => $errcode, 'errmsg' => $errmsg);
        } else if ($returnType == self::RETURN_TYPE_EXCEPTION) {
            throw new Exception($errmsg, $errcode);
        } else {
            throw new Exception('not support return type.');
        }
    }

}
