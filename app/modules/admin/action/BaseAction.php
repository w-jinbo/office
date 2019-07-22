<?php


namespace app\admin\action;


use app\admin\service\UserService;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\session\Session;
use herosphp\utils\JsonResult;
use herosphp\http\HttpRequest;
use app\demo\service\RoleService;

class BaseAction extends Controller {
    protected $userService ;
    protected $roleService ;
    protected $admin ;
    public function __construct() {
        parent::__construct();
        Session::start();
        $this->userService = Loader::service(UserService::class);
        $this->roleService = Loader::service(RoleService::class);
        $this->isLogin();
    }

    /**
     * 判断用户是否已经登录
     */
    public function isLogin() {
        $userId=Session::get('user_id');
        if (!$userId) {
            location('/admin/login/index');
            die();
        }
        $admin = $this->userService->findById($userId);
        if (!$admin) {
            location('/admin/login/index');
            die();
        }
        $admin['permissions'] = array();
        //获取用户拥有的角色权限
        $roleArr = explode(',', $admin['role_ids']);
        if (!empty($roleArr)) {
            $role = $this->roleService->where('is_valid', 1)->where('id', 'in', $roleArr)->fields('permissions')->find();
            $permissions = array();
            foreach ($role as $k => $v) {
                $tempArr = explode(',', $v['permissions']);
                $permissions = array_merge($permissions, $tempArr);
            }
            $permissions = array_unique($permissions);
            $admin['permissions'] = $permissions;
        }
        $this->admin = $admin;
        Session::set('user_id', $admin['id']);
        Session::set('username', $admin['username']);
    }

    /**
     * 获取列表数据(废弃)
     * 
     * @param $service
     * @param HttpRequest $request
     * @return JsonResult $result
     */
    public function getDataList($service, HttpRequest $request) {
        $result = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $total = $service->count();
        $data = $service->page($page, $pageSize)->order('id desc')->find();
        $result->setData($data);
        $result->setCount($total);
        $result->setPage($page);
        $result->setPagesize($pageSize);
        return $result;
    }

    /**
     * 修改记录的是否有效属性
     * 
     * @param int $id
     * @param int $valid
     * @param $service
     * @return int
     */
    protected function changeValid(int $id, int $valid, $service) {
        $res = 0;
        if ($valid == 1) {
            $res = $service->increase('is_valid', 1, $id);
        } else {
            $res = $service->reduce('is_valid', 1, $id);
        }
        return $res;
    }

    /**
     * 检测权限
     *
     * @param string $permission
     * @return bool
     */
    protected function chkPermission(string $permission) {
        if ($this->admin['is_super'] == 1) {
            //超级管理员，无需验证权限
            return true;
        }

        $permissions = $this->admin['permissions'];
        if (!in_array($permission, $permissions)) {
            return false;
        }
        return true;
    } 
}