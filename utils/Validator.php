<?php

class Validator {
    
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function phone($phone) {
        return preg_match('/^\+212[67]\d{8}$/', $phone);
    }
    
    public static function date($date) {
        return strtotime($date) !== false;
    }
    
    public static function required($value) {
        return !empty(trim($value));
    }
    
    public static function number($value) {
        return is_numeric($value);
    }
    
    public static function integer($value) {
        return filter_var($value, FILTER_VALIDATE_INT);
    }
    
    public static function positive($value) {
        return is_numeric($value) && $value > 0;
    }
    
    public static function length($value, $min = 1, $max = 255) {
        $len = strlen(trim($value));
        return $len >= $min && $len <= $max;
    }
    
    public static function alpha($value) {
        return preg_match('/^[a-zA-Z\s\-]+$/', $value);
    }
    
    public static function alphanumeric($value) {
        return preg_match('/^[a-zA-Z0-9\s\-\.,]+$/', $value);
    }
    
    public static function confirm($value1, $value2) {
        return $value1 === $value2;
    }
    
    public static function inArray($value, $array) {
        return in_array($value, $array);
    }
}