<?php

/*
 * 用户管理Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:02:45 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:03:08
 */

namespace app\admin\dao;



class UserDao extends BaseDao {
    public function __construct() {
        parent::__construct('user');
        $this->primaryKey = 'id';
    }
}