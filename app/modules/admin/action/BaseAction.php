<?php

/*
 * 基类控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:24:29 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-29 16:49:15
 */

namespace app\admin\action;


use app\admin\service\UserService;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\utils\JsonResult;
use herosphp\http\HttpRequest;
use app\demo\service\RoleService;
use app\admin\traits\DataFilterTraits;
use app\admin\service\SystemTipService;

class BaseAction extends Controller {

    use DataFilterTraits;

    protected $userService ;
    protected $roleService ;
    protected $admin ;
    public function __construct() {
        parent::__construct();
        $this->userService = Loader::service(UserService::class);
        $this->roleService = Loader::service(RoleService::class);
        $this->isLogin();
    }

    /**
     * 判断用户是否已经登录
     */
    public function isLogin() {
        $admin = $this->userService->getUser();
        if (!$admin) {
            location('/admin/login/index');
            die();
        }
        $this->admin = $admin;
    }

    /**
     * 删除操作
     *
     * @param service $service 服务类对象
     * @param string $delIds 要删除的记录id集合 1,2,3
     * @return JsonResult
     */
    protected function doDel($service, string $delIds) {
        if (empty($delIds)) {
            JsonResult::fail('请选择要删除的记录');
        }
        $result = $service->delRows($delIds);
        if (!$result) {
            JsonResult::fail('删除失败，请稍后重试');
        }
        JsonResult::success();
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

    protected function addSystemTip(int $type, int $logId, int $userId = 0) {
        $systemTipModel = new SystemTipService();
        $systemTipModel->addTip($type, $logId, $userId);
    }
}