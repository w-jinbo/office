<?php

/*
 * 权限配置Dao
 * @Author: WangJinBo <wangjb@pvc123.com> 
 * @Date: 2019-08-05 11:05:22 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-05 18:03:54
 */

namespace app\admin\dao;

class PermissionDao extends BaseDao {

    public function __construct() {
        parent::__construct('permission');
        $this->primaryKey = 'id';
    }

    /**
     * 获取权限
     *
     * @param integer $type 
     * @return array
     */
    public function getPermission(int $type = 0) {
        $query = $this;
        if ($type > 0) {
            $query->where('type', $type);
        }
        $permissions = $query->fields('id, name, parent_id, permission, url')->find();
        return $permissions;
    }
}