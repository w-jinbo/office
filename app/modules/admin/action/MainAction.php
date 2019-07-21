<?php


namespace app\admin\action;

use herosphp\http\HttpRequest;
use herosphp\session\Session;

class MainAction extends BaseAction {
    private $admin ;
    public function __construct() {
        parent::__construct();
        $userId = Session::get('user_id');
        $admin = $this->userService->findById($userId);
        $this->admin = $admin;
        $this->assign('admin', $admin);
    }

    /**
     * 首页
     */
    public function index() {
        $this->setView('main/index');
    }

    public function main() {
        
    }

    /**
     *　左侧菜单栏
     */
    public function left() {
        $this->setView('main/left');
    }

    /**
     *　修改用户信息页面
     */
    public function setUserInfo() {
        $this->setView('main/set_user_info');
    }

    /**
     *　修改用户密码页面
     */
    public function setPwd() {
        $this->setView('main/set_pwd');
    }

    /**
     *　退出登录
     */
    public function quit() {
        $this->userService->quit();
        die();
    }

    public function doSetUserInfo(HttpRequest $request) {
        $parameters = $request->getParameters();
        $result = $this->userService->updateUser($parameters);
        $result->output();
    }

    public function doSetPwd(HttpRequest $request) {
        $newPwd = $request->getStrParam('new_pwd');
        $oldPwd = $request->getStrParam('old_pwd');
        $result = $this->userService->setPwd($newPwd, $oldPwd);
        $result->output();
    }
}