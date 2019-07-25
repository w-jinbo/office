<?php

/*
 * 文具申请Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:00:59 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:02:09
 */

namespace app\admin\dao;



class StationeryApplyDao extends BaseDao {
    public function __construct() {
        parent::__construct('stationery_apply');
        $this->primaryKey = 'id';
    }
}