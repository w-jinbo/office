<?php

/*
 * 办公室管理Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:59:20 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:59:50
 */

namespace app\admin\dao;



class OfficeDao extends BaseDao {
    public function __construct() {
        parent::__construct('office');
        $this->primaryKey = 'id';
    }
}