<?php

/*
 * 办公室申请Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:58:44 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:59:16
 */

namespace app\admin\dao;



class OfficeApplyDao extends BaseDao {
    public function __construct() {
        parent::__construct('office_apply');
        $this->primaryKey = 'id';
    }
}