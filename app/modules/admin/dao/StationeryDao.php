<?php

/*
 * 文具管理Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:02:20 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:02:41
 */

namespace app\admin\dao;



class StationeryDao extends BaseDao {
    public function __construct() {
        parent::__construct('stationery');
        $this->primaryKey = 'id';
    }
}