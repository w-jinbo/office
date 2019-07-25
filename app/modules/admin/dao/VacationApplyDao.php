<?php

/*
 * 假期申请Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:03:12 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:03:43
 */

namespace app\admin\dao;



class VacationApplyDao extends BaseDao {
    public function __construct() {
        parent::__construct('vacation_apply');
        $this->primaryKey = 'id';
    }
}