<?php

/*
 * 角色权限关联Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-08-05 13:59:07 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-05 14:16:13
 */

namespace app\admin\dao;

class RolePermissionDao extends BaseDao {

    public function __construct(){
        parent::__construct('role_permission');
        $this->primaryKey = 'id';
    }

    /**
     * 新增记录
     *
     * @param integer $roleId 角色id
     * @param integer $permissionId 权限id
     * @return int|bool
     */
    public function addRow(int $roleId, int $permissionId) {
        $data = array(
            'role_id' => $roleId,
            'permission_id' => $permissionId,
            'create_time' => date('Y-m-d H:i:s')
        );
        $result = $this->add($data);
        return $result;
    }

    /**
     * 删除一条或多条记录
     *
     * @param integer $roleId 角色id
     * @return int|bool
     */
    public function delRow(int $roleId) {
        $result = $this->where('role_id', $roleId)->deletes();
        return $result;
    }
}