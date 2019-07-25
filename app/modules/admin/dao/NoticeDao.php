<?php

/*
 * 系统公共Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:00:08 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:00:30
 */

namespace app\admin\dao;



class NoticeDao extends BaseDao {
    public function __construct() {
        parent::__construct('notice');
        $this->primaryKey = 'id';
    }
}