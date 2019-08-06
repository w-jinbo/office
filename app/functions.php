<?php
/*---------------------------------------------------------------------
 * 应用的公共的常用的全局函数
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

/**
 * 判断日期是否合法
 * 
 * @param string $date 日期
 * @param array $formats 格式数组
 * @return bool
 */
function isDateValid(string $date, array $formats = array('Y-m-d', 'Y/m/d')) {
    $unixTime = strtotime($date);
    //无法用strtotime转换，说明日期格式非法
    if (!$unixTime) { 
        return false;
    }

    //校验日期合法性，只要满足其中一个格式就可以
    foreach ($formats as $format) {
        if (date($format, $unixTime) == $date) {
            return true;
        }
    }
    return false;
}

/**
 * 获取请求的类型
 *
 * @return string
 */
function getRequestMethod(){
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return 'AJAX';
    }

    if (!empty($_POST)) {
        return 'POST';
    }

    return 'GET';
}

function chkPower(string $permission) {
    return true;
    $admin = $GLOBALS['admin'];
    if ($admin['is_super'] == 1) {
        return true;
    }
    $permissions = $admin['permissions'];
    return in_array($permission, $permissions);
}

/**
 * 递归处理权限集合，构成成树状结构
 *
 * @param string $pkey
 * @param array $power
 * @return array $resData
 */
function dealPermission(string $pkey, array &$power) {
    $resData = array();
    foreach ($power as $k => $v) {
        if ($pkey === $v['parent_id']) {
            unset($power[$k]);
            $resData['sub'][] = array_merge($v, dealPermission($v['id'], $power));
        }
    }
    return $resData;
}

/**
 * 根据特定值查询二维数组中是否包含该值
 *
 * @param array $array 待查询二维数组
 * @param string $index 要查询的键
 * @param string $value 要查询的值
 * @return void
 */
function filterByValue (array $array, string $index, string $value) { 
    $newArray = array();
    if(is_array($array) && count($array) > 0) { 
        foreach(array_keys($array) as $key){ 
            $temp[$key] = $array[$key][$index]; 
             
            if ($temp[$key] == $value) { 
                $newArray[$key] = $array[$key]; 
            } 
        } 
    } 
    return $newArray; 
} 