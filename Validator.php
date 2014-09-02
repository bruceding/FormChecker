<?php
require_once "Validation.php";
/**
 * Validator 
 * 字段验证工具类 
 *
 * @author bruceding <dingjingdjdj@gmail.com> 
 */
class Validator {

    /**
     *  返回类型：json数据 
     */
    const RETURN_TYPE_JSON = 1;
    /**
     * 返回类型：抛出异常 
     */
    const RETURN_TYPE_EXCEPTION = 2;

    /**
     * 允许的操作 
     */
    protected static $_operate = array('require', 'min_length', 'max_length', 'type', 'regexp');

    /**
     * 允许的字段值 
     */
    protected static $_allow_fields = array('require', 'min_length', 'max_length', 'type', 'regexp', 'field', 'type', 'value', 'errmsg');

    /**
     * validate 
     * 
     * @param array $fields  $fields = array(
     *                              array('field' => '', 'type'=> '', 'value'=> '', 'require' => true, 'errmsg' =>),
     *                              array('field' => '', 'type'=> '', 'value'=> '', 'min_length' => 8),
     *                              ) 
     * @static
     * @access public
     * @return void
     */
    public static function validate(array $fields) {
        
        if (!$fields || !is_array($fields)) {
            return self::_quit('invalid fields');
        }
        foreach ($fields as $field) {
            if (!is_array($field)) {
                return self::_quit('invalid fields');
            } 

            if (!isset($field['field'])) {
                return self::_quit('invalid fields');
            }

            // 检查key的交集
            $fieldsDiff = array_diff(array_keys($field), self::$_allow_fields);
            if ($fieldsDiff) {
                return self::_quit('fields:' . '\'' . join(',', $fieldsDiff) . '\'' . ' not allowed.');
            }

            $validation = new Validation();
            foreach ($field as $key => $val) {
                if (in_array($key, self::$_operate)) {
                    $errmsg = forward_static_call_array(array('Validator', '_'. $key), array($field));
                    if (!empty($errmsg)) {
                        $validation = self::_error($errmsg, $field, $validation);
                    }
                } 

            }
        }

        return $validation;
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
        
        if (!isset($field['value'])) {
            return  "{$field['field']}'s value is not set";
        }

        $errcode = 0;
        $errmsg = '';
        if (strtolower($field['type']) == 'string') {

            if (!is_string($field['value'])) {
                $errcode = -1;
            }
        } else if (strtolower($field['type']) == 'array') {
        
            if (!is_array($field['value'])) {
                $errcode = -1;
            }
        } else if (strtolower($field['type']) == 'email') {
            if (filter_var($field['value'], FILTER_VALIDATE_EMAIL) === false) {
                $errcode = -1;
            }
        } else if (strtolower($field['type']) == 'url') {
            if (filter_var($field['value'], FILTER_VALIDATE_URL) === false) {
                $errcode = -1;
            } 
        } else if (strtolower($field['type']) == 'date') {
            $dateArr = date_parse($field['value']);
            if ($dateArr && $dateArr['error_count'] === 0) {
                if (checkdate($dateArr['month'], $dateArr['day'], $dateArr['year']) === false) {
                    $errcode = -1;
                }  
            } else {
                $errcode = -1;
            }
        } else {
            throw new Exception('not support type');
        }

        if ($errcode !== 0) {
            $errmsg = "{$field['field']}'s type not right";
        }

        return $errmsg;
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

        $errmsg = '';
        if ($field['require'] === true && !isset($field['value'])) {
            $errmsg = "{$field['field']}'s value empty";
        }

        return $errmsg;
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
        
        $errmsg = '';

        if (function_exists('mb_strlen')) {
            if (mb_strlen($field['value'], 'UTF-8') < $field['min_length']) {
                $errmsg = isset($field['errmsg']) ? $field['errmsg'] : "{$field['field']}'s length at least {$field['min_length']} length"; 
            }
        } else {
            if (strlen($field['value']) < $field['min_length']) {
                $errmsg = isset($field['errmsg']) ? $field['errmsg'] : "{$field['field']}'s length at least {$field['min_length']} length"; 
            }
        }

        return $errmsg;
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
    
        $errmsg = '';
        if (function_exists('mb_strlen')) {
            if (mb_strlen($field['value'], 'UTF-8') >  $field['max_length']) {
                $errmsg = "{$field['field']}'s length at most {$field['max_length']} length"; 
            }
        } else {
            if (strlen($field['value']) >  $field['max_length']) {
                $errmsg = "{$field['field']}'s length at most {$field['max_length']} length"; 
            }
        }

        return $errmsg;
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

        $errmsg = '';
        if (preg_match($field['regexp'], $field['value']) === 0) {
            $errmsg = "{$field['field']} not match {$field['regexp']} pattern";
        }

        return $errmsg;
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
    protected static function _error($errmsg, $field, $validation) {
        $errmsg = isset($field['errmsg']) ? $field['errmsg'] : $errmsg;
        $validation->addError($errmsg);
        return $validation;
    }

    private static function _success() {
        $validation = new Validation();
        return $validation;
    }

    private static function _quit($errMsg) {
        $validation = new Validation();
        $validation->addError($errMsg);
        return $validation;
    }

}
