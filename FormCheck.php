<?php

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
     * 允许的操作 
     */
    private static $_operate = array('require', 'min_length', 'max_length', 'type');


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
            throw new InvalidException('invalid fields');
        }
        foreach ($fields as $field) {
            if (!is_array($field)) {
                throw new InvalidException('invalid fields');
            } 

            if (!$field['field']) {
            
                throw new InvalidException('field is not empty');
            }

            // 检查key的交集
            foreach ($field as $key => $val) {
                if (in_array($key, self::$_operate)) {
                    list($errcode, $errmsg) = forward_static_call_array(array('FormCheck', '_'. $key), array($field));
                    if ($errcode) {
                        return self::_error($errcode,$errmsg, $fields, $returnType);
                    }
                } 
            }
        }

        return array('errcode' => 0, 'errmsg' => 'ok');
    }

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
        } else {
            throw new Exception('not support type');
        }

        if ($errcode) {
            $errmsg = "{$field['field']}'s type not right";
        }

        return array($errcode, $errmsg);
    }

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
            $errmsg = "{$field['field']}'s length at least {$field['min_length']}"; 
        }

        return array($errcode, $errmsg);
    }

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
