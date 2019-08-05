<?php

/*
 * 角色管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:41:59 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-05 14:16:20
 */

namespace app\admin\service;

use app\admin\dao\RoleDao;
use app\admin\dao\RolePermissionDao;

class RoleService extends BaseService {
    protected $modelClassName = RoleDao::class;

    /**
     * 获取角色选项列表
     *
     * @return array $list
     */
    public function roleList() {
        $list = $this->modelDao->fields('id, name')->where('is_valid', '1')->find();
        return $list;
    }

    /**
     * 新增角色
     *
     * @param string $name 角色名
     * @param integer $isValid 是否有效
     * @param string $summary 描述
     * @param string $permissions 权限id集合 1,2,3
     * @return int
     */
    public function addRole(string $name, int $isValid, string $summary, string $permissions) {
        $query = $this->modelDao;
        $date = date('Y-m-d H:i:s');
        $data = array(
            'name' => $name,
            'permissions' => $permissions,
            'is_valid' => $isValid,
            'summary' => $summary,
            'create_time' => $date,
            'update_time' => $date
        );
        $query->beginTransaction();
        $roleId = $query->add($data);
        if ($roleId <= 0) {
            $query->rollback();
            return false;
        }
        $permissionDao = new RolePermissionDao();
        $permissionArr = explode(',', $permissions);
        foreach ($permissionArr as $k=>$v) {
            $res = $permissionDao->addRow($roleId, $v);
            if ($res <= 0) {
                $query->rollback();
                return false;
            }
        }
        $query->commit();
        return $roleId;
    }

    /**
     * 更新角色记录信息
     *
     * @param int $roleId 记录id
     * @param integer $isValid 是否有效
     * @param string $summary 描述
     * @param string $permissions 权限集合
     * @return int
     */
    public function updateRole(int $roleId, int $isValid, string $summary, string $permissions) {
        $query = $this->modelDao;
        $data = array(
            'is_valid' => $isValid,
            'summary' => $summary,
            'permissions' => $permissions,
            'update_time' => date('Y-m-d H:i:s')
        );
        $query->beginTransaction();
        $result = $query->update($data, $roleId);
        if ($result <= 0) {
            $query->rollback();
            return false;
        }
        $permissionDao = new RolePermissionDao();
        $permissionDao->delRow($roleId);
        $permissionArr = explode(',', $permissions);
        foreach ($permissionArr as $k=>$v) {
            $res = $permissionDao->addRow($roleId, $v);
            if ($res <= 0) {
                $query->rollback();
                return false;
            }
        }
        $query->commit();
        return $result;
    }

    /**
     * 删除多条数据
     *
     * @param string $ids
     * @return JsonResult
     */
    public function delRows(string $ids) {
        $query = $this->modelDao;
        $idsArr = explode(',', $ids);
        $count = count($idsArr);
        $query->beginTransaction();
        $result = $query->where('id', 'in', $idsArr)->deletes();
        if ($count != $result) {
            $query->rollback();
            return false;
        }
        $permissionDao = new RolePermissionDao();
        foreach ($idsArr as $k => $v) {
            $res = $permissionDao->delRow($v);
            if ($res <= 0) {
                $query->rollback();
                return false;
            }
        }
        $query->commit();
        return $result;
    }
}