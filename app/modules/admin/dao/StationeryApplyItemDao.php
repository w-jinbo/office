<?php

/*
 * 文具申请子项Dao
 * @Author: WangJinBo <wangjb@ovc123.com>
 * @Date: 2019-07-25 18:01:27 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:02:18
 */

namespace app\admin\dao;



class StationeryApplyItemDao extends BaseDao {
    public function __construct() {
        parent::__construct('stationery_apply_item');
        $this->primaryKey = 'id';
    }
}