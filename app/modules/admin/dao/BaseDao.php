<?php

/*
 * dao基础类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:58:00 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:58:31
 */

namespace app\admin\dao;


use herosphp\model\MysqlModel;

class BaseDao extends MysqlModel {

    public function __clone() {
        // 强制复制一份this->sqlBuilder， 否则仍然指向同一个对象
        $this->sqlBuilder = clone $this->sqlBuilder;
    }
}