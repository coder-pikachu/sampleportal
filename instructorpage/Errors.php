<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of error
 *
 * @author sangrammohite
 */
class Errors {

    public $errors_dict = array();

    public function checkErrorKey($key) {
        return array_key_exists($key, $this->errors_dict);
    }

    public function addError($key, $errorText) {
        $this->errors_dict[$key] = $errorText;
    }

    public function getErrorValue($key) {
        return $this->errors_dict[$key];
    }

    public function removeError($key) {
        $keyIndex = array_search($key, $this->errors_dict);
        unset($this->errors_dict[$keyIndex]);
    }

    public function getErrorsLength() {
        return count($this->errors_dict);
    }

}
