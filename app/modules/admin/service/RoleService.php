<?php

/*
 * 角色管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:41:59 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-29 17:47:05
 */

namespace app\admin\service;

use app\admin\dao\RoleDao;

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
     * @param string $permissions 权限集合
     * @return int
     */
    public function addRole(string $name, int $isValid, string $summary, string $permissions) {
        $date = date('Y-m-d H:i:s');
        $data = array(
            'name' => $name,
            'is_valid' => $isValid,
            'summary' => $summary,
            'permissions' => $permissions,
            'create_time' => $date,
            'update_time' => $date
        );
        $result = $this->modelDao->add($data);
        return $result;
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
        $data = array(
            'is_valid' => $isValid,
            'summary' => $summary,
            'permissions' => $permissions,
            'update_time' => date('Y-m-d H:i:s')
        );
        $result = $this->modelDao->update($data, $roleId);
        return $result;
    }
}