<?php


namespace app\admin\action;

use herosphp\http\HttpRequest;
use herosphp\session\Session;

class MainAction extends BaseAction {
    public function __construct() {
        parent::__construct();
    }

    /**
     * 首页
     */
    public function index() {
        $this->setView('main/index');
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
        $userId = Session::get('user_id');
        $user = $this->userService->findById($userId);
        $this->assign('user', $user);
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
        $this->userService->updateUser($parameters);
    }
}