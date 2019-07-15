<?php
namespace app\demo\action;


use app\demo\service\RoleService;
use app\demo\service\UserService;
use DeepCopyTest\H;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;



class IndexAction extends ControllerRepeat {

    private  $userServer ;

    private $menu;

    public function  __construct()
    {
        parent::__construct();
        $this->isLogin();
        $this->userServer  = Loader::service(UserService::class);
        $this->menu  = $this->roleString($_SESSION['roleAuth']);
    }


    /**
     * 个人资料
     */
    public function user(HttpRequest $request)
    {
        $uri = $request->getRequestUri();
        $this->isAuth($_SESSION['roleAuth'],$uri);
        $data['user'] = $this->userServer->userDate($_SESSION['userID']);
        $data['menu'] = $this->menu;
        $this->assign("data",$data);
        $this->setView("back/back");
    }

    public function out()
    {
        session_destroy();
        $this->setView('login/login');
    }


    /***
     * 添加用户入口
    */
    public function userInsert(HttpRequest $request)
    {
        $uri = $request->getRequestUri();
        $this->isAuth($_SESSION['roleAuth'],$uri);
        $data['menu'] = $this->menu;
        $roleService = Loader::service(RoleService::class);
        $data['role'] = $roleService->role();
        $this->assign('data',$data);
        $this->setView('user/user');
    }

    public function userAdd(HttpRequest $request)
    {
        $data['menu'] = $this->menu;
        $userData = $request->getParameters();
        $data['tip'] =$this->userServer->isFirst($userData);
        $this->assign('data',$data);
        $this->setView('user/user');
    }
    /**
     * 查看所有用户
    */
    public function allUser(HttpRequest $request)
    {
        $pageSize = 10;
        $page = $request->getParameter('page');
        $count = $this->userServer->allCount();
        $data['page'] = $this->nowPage($count,$pageSize,$page);
        $data['menu'] = $this->menu;
        $data['allUser'] = $this->userServer->allUser($page,$pageSize);
        $this->assign('data',$data);
        $this->setView('user/userAll');
    }

    /**
     * 修改用户
    */
    public function updateUser(HttpRequest $request)
    {
        $id = $request->getParameter('id');
        $data['menu'] = $this->menu;
        $data['user'] = $this->userServer->userDate($id);
        $roleService = Loader::service(RoleService::class);
        $data['role'] = $roleService->role();
        $this->assign('data',$data);
        $this->setView('user/updateUser');
    }
    public function update(HttpRequest $request)
    {
        $gets = $request->getParameters();
        $data['menu'] = $this->menu;
        $data['tip'] = $this->userServer->updateUser($gets);
        $this->assign('data',$data);
        $this->setView('user/user');
    }

    /**
     * 删除用户
    */
    public function delUser(HttpRequest $request)
    {
        $pageSize = 10;
        $page = $request->getParameter('page');
        $id   = $request->getParameter('id');
        $data['tip']    = $this->userServer->delUser($id);
        $count = $this->userServer->allCount();
        $data['page'] = $this->nowPage($count,$pageSize,$page);
        $data['menu'] = $this->menu;
        $data['allUser'] = $this->userServer->allUser($page,$pageSize);
        $this->assign('data',$data);
        $this->setView('user/userAll');
    }






}
