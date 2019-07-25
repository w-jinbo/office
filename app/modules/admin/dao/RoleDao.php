<?php

/*
 * 角色管理Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:00:35 
 * @Last Modified by:   WangJinBo 
 * @Last Modified time: 2019-07-25 18:00:35 
 */

namespace app\admin\dao;



class RoleDao extends BaseDao {
    public function __construct() {
        parent::__construct('role');
        $this->primaryKey = 'id';
    }
}