<?php

/*
 * 用户管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:42:54 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-29 16:48:53
 */

namespace app\admin\service;


use herosphp\session\Session;
use app\admin\dao\UserDao;
use herosphp\core\Loader;

class UserService extends BaseService {

    protected $modelClassName = UserDao::class;

    /**
     * 用户登录
     *
     * @param string $userName 用户名
     * @param string $passWord 密码
     * @return array $result
     */
    public function login(string $userName, string $passWord) {
        $result = array(
            'success' => false,
            'message' => ''
        );
        //查询账号是否存在
        $user = $this->isUser($userName);
        if (!$user) {
            $result['message'] = '没有该用户的账号信息';
            return $result;
        }

        if ($user['is_valid'] === 0){
            $result['message'] = '该账号已被禁用';
            return $result;
        }

        $md5Pwd = md5(md5($passWord) . $user['salt']);
        if ($md5Pwd != $user['password']) {
            $result['message'] = '账号或密码错误';
            return $result;
        }

        //通过密码验证，设置session
        Session::set('user_id', $user['id']);
        Session::set('username', $userName);

        $result['success'] = true;
        $result['message'] = '登录成功';
        return $result;
    }

    /**
     * 获取用户列表数据
     *
     * @param sting $keyword 关键词
     * @param int $page 页码
     * @param int $pageSize 分页大小
     * @return array $return
     */
    public function getListData(string $keyword, int $page, int $pageSize) {
        $query = $this->modelDao;
        if (!empty($keyword)) {
            $query->whereOr('username', 'like', '%' . $keyword . '%')
                ->whereOr('realname', 'like', '%' . $keyword . '%')
                ->whereOr('department', 'like', '%' . $keyword . '%');
        }

        $return = array(
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => 0,
            'list' => array()
        );

        //克隆查询对象，防止查询条件丢失
        $countQuery = clone $query;
        $total = $countQuery->count();
        if ($total <= 0) {
            return $return;
        }

        $data = $query->page($page, $pageSize)->order('id desc')->find();
        $return['total'] = $total;
        $return['list'] = $data;
        return $return;
    }

    /**
     * 增加用户操作
     * 
     * @param string $username 登录账号
     * @param string $pwd 密码
     * @param string $realName 用户姓名
     * @param string $tel 联系电话
     * @param string $department 部门
     * @param int $isValid 是否有效
     * @param string $roleIds 角色id集合
     * @return int|bool $result 
     */
    public function addUser(string $username, string $pwd, string $realName, 
        string $tel, string $department, string $isValid, string $roleIds) {
        $data = array(
            'username' => $username,
            'realname' => $realName,
            'tel' => $tel,
            'department' => $department,
            'is_valid' => $isValid,
            'role_ids' => $roleIds
        );

        $date = date('Y-m-d H:i:s');

        $data['salt'] = rand(1000, 9999);
        $data['password'] = md5(md5($pwd) . $data['salt']);
        $data['create_time'] = $date;
        $data['update_time'] = $date;
        $result = $this->modelDao->add($data);
        return $result;
    }

    /**
     * 更新用户信息
     * @param string $realName 用户姓名
     * @param string $tel 联系电话
     * @param string $department 部门
     * @param int $isValid 是否有效
     * @param string $roleIds 角色id集合
     * @param string $userId 用户记录id
     * @return bool|int $result
     */
    public function updateUser(string $realName, string $tel, string $department, 
        int $isValid = null, string $roleIds = '', int $userId = 0) {
        if ($userId <= 0) {
            $user = self::getUser();
            $userId = $user['id'];
        }
        
        $data = array(
            'realname' => $realName,
            'tel' => $tel,
            'department' => $department,
            'update_time' => date('Y-m-d H:i:s')
        );

        //用户修改个人信息时没有以下两项数据
        $isValid === null ? $data['is_valid'] = $isValid : '';
        !empty(roleIds) ? $data['role_ids'] = $roleIds : '';

        $result = $this->modelDao->update($data, $userId);
        return $result;
    }

    /**
     * 修改密码操作
     * 
     * @param string $newPwd 新密码
     * @param int $userId 用户记录id
     * @return int|bool $result
     */
    public function setPwd(string $newPwd, int $userId) {
        $update = array();
        $salt = rand(1000, 9999);
        $update['password'] = md5(md5($newPwd) . $salt);
        $update['salt'] = $salt;
        $update['update_time'] = date('Y-m-d H:i:s');
        $result = $this->modelDao->update($update, $userId);
        return $result;
    }

    /**
     *　退出登录操作
     */
    public function quit() {
        Session::set('user_id', null);
        Session::set('username', null);
    }

    /**
     * 获取用户信息
     * @return bool|array
     */
    public function getUser() {
        $userName = Session::get('username');
        if (empty($userName)) {
            return false;
        }

        $user = $this->isUser($userName);
        if (!$user) {
            return false;
        }

        $user['permissions'] = array();
        //获取用户拥有的角色权限
        $roleArr = explode(',', $user['role_ids']);
        if (!empty($roleArr)) {
            $roleService = Loader::service(RoleService::class);
            $role = $roleService->where('is_valid', 1)->where('id', 'in', $roleArr)->fields('permissions')->find();
            $permissions = array();
            foreach ($role as $k => $v) {
                $tempArr = explode(',', $v['permissions']);
                $permissions = array_merge($permissions, $tempArr);
            }
            $permissions = array_unique($permissions);
            $user['permissions'] = $permissions;
        }
        return $user;
    }

    /**
     * 判断用户是否存在
     * 
     * @param string $userName
     * @return array|bool
     */
    public function isUser (string $userName) {
        $user = $this->modelDao->where('username',$userName)->findOne();
        return empty($user) ? false :$user;
    }
}