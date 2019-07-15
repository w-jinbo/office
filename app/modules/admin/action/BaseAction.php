<?php


namespace app\admin\action;


use app\admin\service\UserService;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\session\Session;

class BaseAction extends Controller {
    public $userService ;
    public function __construct() {
        parent::__construct();
        Session::start();
        $this->userService = Loader::service(UserService::class);
        $this->isLogin();
    }

    public function isLogin() {
        $userId=Session::get('user_id');
        if (!$userId) {
            location('/admin/login/index');
            die();
        }
    }
}