<?php

/*
 * 假期管理Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:03:48 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:04:13
 */

namespace app\admin\dao;



class VacationDao extends BaseDao {
    public function __construct() {
        parent::__construct('vacation');
        $this->primaryKey = 'id';
    }
}