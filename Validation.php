<?php
/**
 * Validation 
 * 字段验证返回类
 * 
 * @author bruce ding <dingjingdjdj@gmail.com> 
 */
class Validation {

    protected $valid;

    protected $errors;

    protected $last_error;

    public function __construct() {
        $this->valid = true;
        $this->errors = array();
    }

    public function addError($errMsg) {
        
        $this->errors[] = $errMsg;
        $this->last_error = $errMsg;
        $this->valid = false;
    }

    public function errors() {
        return $this->errors;
    }

    public function isValid() {
        return $this->valid;
    }

    public function lastError() {
        return $this->last_error;
    }
}
