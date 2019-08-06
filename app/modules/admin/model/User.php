<?php

namespace app\admin\model;

use herosphp\core\Loader;
use app\admin\dao\RolePermissionDao;
use app\admin\dao\RoleDao;
use app\admin\dao\PermissionDao;

class User {
    private $id;
    private $username;
    private $password;
    private $salt;
    private $roleIds;
    private $realname;
    private $tel;
    private $isSuper;
    private $isValid;
    private $department;

    /**
     * 获取用户拥有的权限
     *
     * @param integer $type 类型，1：菜单，2：操作
     * @return array $result
     */
    public function getPermissions(int $type = 0) {
        if ($this->isSuper == 1) {
            //超级管理员，返回所有权限
            $permissionDao = new PermissionDao();
            return $permissionDao->getPermission($type);
        }
        $result = array();
        $roleArr = explode(',', $this->roleIds);
        //过滤失效的角色
        $roleDao = new RoleDao();
        $unValid = $roleDao->where('is_valid', 0)->where('id', 'in', $this->roleIds)->fields('id')->find();
        if (!empty($unValid)) {
            $temp = $unValid;
            $unValid = array();
            foreach ($temp as $k => $v) {
                array_push($unValid, $v['id']);
            }
            $roleArr = array_diff($roleArr, $unValid);
        }
        //获取用户拥有的角色权限
        if (!empty($roleArr)) {
            $rolePermissionDao = new RolePermissionDao();
            $rolePermissionDao
                ->alias('a')
                ->join('permission as b', MYSQL_JOIN_INNER)
                ->on('a.permission_id = b.id')
                ->where('a.role_id', 'in', $roleArr)
                ->fields('b.id, b.parent_id, b.name, b.permission, b.url');

            if ($type > 0) {
                $rolePermissionDao->where('b.type', $type); 
            }

            $permissions = $rolePermissionDao->find();
            
            //去除重复值
            for ($i = 0; $i < count($permissions); $i ++) {
                $source = $permissions[$i];
                if (array_search($source, $permissions) == $i && $source <> "" ) {
                    $result[]=$source;
                }
            }
        }
        return $result;
    }

    /**
     * 获取左侧菜单列表
     *
     * @return void
     */
    public function getMenu() {
        $permission = $this->getPermissions(1);
        $menu = dealPermission('0', $permission);
        return $menu;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt) {
        $this->salt = $salt;
    }

    /**
     * @return mixed
     */
    public function getRoleIds() {
        return $this->roleIds;
    }

    /**
     * @param mixed $roleIds
     */
    public function setRoleIds($roleIds) {
        $this->roleIds = $roleIds;
    }

    /**
     * @return mixed
     */
    public function getRealname() {
        return $this->realname;
    }

    /**
     * @param mixed $realname
     */
    public function setRealname($realname) {
        $this->realname = $realname;
    }

    /**
     * @return mixed
     */
    public function getTel() {
        return $this->tel;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
    }

    /**
     * @return mixed
     */
    public function getIsSuper() {
        return $this->isSuper;
    }

    /**
     * @param mixed $isSuper
     */
    public function setIsSuper($isSuper) {
        $this->isSuper = $isSuper;
    }

    /**
     * @return mixed
     */
    public function getIsValid() {
        return $this->isValid;
    }

    /**
     * @param mixed $isValid
     */
    public function setIsValid($isValid) {
        $this->isValid = $isValid;
    }

    /**
     * @return mixed
     */
    public function getDepartment() {
        return $this->department;
    }

    /**
     * @param mixed $department
     */
    public function setDepartment($department) {
        $this->department = $department;
    }
}