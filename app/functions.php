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

function chkPower(string $permission) {
    $admin = $GLOBALS['admin'];
    if ($admin['is_super'] == 1) {
        return true;
    }
    $permissions = $admin['permissions'];
    return in_array($permission, $permissions);
}