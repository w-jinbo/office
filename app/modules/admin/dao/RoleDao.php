<?php

/*
 * 角色管理Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:00:35 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-05 13:47:23
 */

namespace app\admin\dao;

use herosphp\filter\Filter;

class RoleDao extends BaseDao {

    public static $filter = array(
        'name' => array(Filter::DFILTER_STRING, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '角色名不能为空', 'length' => '角色名称长度必须在1~20之间')),
        'permissions' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES,
            array('require' => '权限集合不能为空')),
        'summary' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, null),
    );

    public function __construct() {
        parent::__construct('role');
        $this->primaryKey = 'id';
    }
}