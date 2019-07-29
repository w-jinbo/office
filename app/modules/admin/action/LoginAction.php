<?php

/*
 * 登录控制器
 * @Author: WangJinBo 
 * @Date: 2019-07-25 17:29:47 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-29 16:49:10
 */

namespace app\admin\action;


use app\admin\service\UserService;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\session\Session;
use herosphp\utils\JsonResult;

class LoginAction extends Controller {

    private $userService ;

    public function __construct() {
        parent::__construct();
        $this->userService = Loader::service(UserService::class);
    }

    public function index() {
        //判断用户是否已经登录
        $user = $this->userService->getUser();
        if (is_array($user)) {
            location('/admin/main/index');
            die();
        }
        $this->assign('title', '登录');
        $this->setView('login/index');
    }

    /**
     * 登录操作
     * 
     * @param HttpRequest $request
     */
    public function doLogin(HttpRequest $request) {
        $userName = $request->getParameter('username', 'trim');
        $passWord = $request->getParameter('password', 'trim');
        if (empty($userName) || empty($passWord)) {
            JsonResult::fail('账号或密码不能为空');
        }

        $result=$this->userService->login($userName, $passWord);

        $result->output();
    }
}