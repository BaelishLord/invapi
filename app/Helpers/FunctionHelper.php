<?php

function excludeColumn($array, $list) {
    return array_values(array_diff( $array, $list ));
}

function removePrefix(array $input, $prefix) {
    $return = array();
    foreach ($input as $str) {
        $return[] = str_replace($prefix, '', $str);       
    }
    return $return;
}

function getIndex($arr, $check) {
    $res = [];
    foreach ($arr as $key => $value) {
        if (in_array($value, $check)) {
            array_push($res, $key);
        }
    }
    return $res;
}

function htmlValue($name, $data) {
    return \Input::old($name) ? \Input::old($name) : ((isset($data) && isset($data[$name])) ? $data[$name] : '');
}

function htmlSelect($name, $data) {
    return \Input::old($name) ? \Input::old($name) : ((isset($data) && isset($data[$name])) ? $data[$name] : null);
}

function setDisable($name, $disabled) {
    if (isset($disabled) && is_array($disabled)) {
        if(in_array($name, $disabled)) {
            return 'disabled';
        }
    }
}