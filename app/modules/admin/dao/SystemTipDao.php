<?php

/*
 * 消息通知Dao
 * @Author: WangJinBo 
 * @Date: 2019-08-01 15:34:38 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-01 15:35:10
 */

namespace app\admin\dao;

class SystemTipDao extends BaseDao {

    public function __construct() {
        parent::__construct('system_tip');
        $this->primaryKey = 'id';
    }
}