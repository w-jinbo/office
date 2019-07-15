<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-9-1
 * Time: 下午5:12
 */

namespace app\demo\action;


use app\demo\service\RoleService;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;

class RoleAction extends ControllerRepeat
{
    private  $roleServer ;
    private  $pageSize ;
    private  $count ;
    private  $menu ;

    public function  __construct()
    {
        parent::__construct();
        $this->isLogin();
        $this->menu  = $this->roleString($_SESSION['roleAuth']);
        $this->pageSize = 10;
        $this->roleServer  = Loader::service(RoleService::class);
        $this->count = $this->roleServer->allCount();
    }
    /**
     * 查看角色
     */
    public function allRole(HttpRequest $request)
    {
        $uri = $request->getRequestUri();
        $this->isAuth($_SESSION['roleAuth'],$uri);
        $page = $request->getParameter('page');
        $data['page'] = $this->nowPage($this->count,$this->pageSize,$page);
        $data['menu'] = $this->menu;
        $data['role'] = $this->roleServer->allRole($page,$this->pageSize);
        $data['rolename'] = $this->_common();
        $this->assign('data',$data);
        $this->setView('role/roleAll');
    }


    /**
     * 添加角色入口
     */
    public function addRole()
    {
        $data['menu'] = $this->menu;
        $role = $this->mainMenu();
        $data['role'] = $this->roleServer->convert($role);
        $this->assign('data',$data);
        $this->setView('role/addRole');
    }

    /**
     * 添加角色操作
    */
    public function insertRole(HttpRequest $request)
    {
        $gets = $request->getParameters();
        $data['menu'] = $this->menu;
        $data['tip']  = $this->roleServer->isAddRole($gets);
        $data['page'] = $this->nowPage($this->count,$this->pageSize,$gets['page']);
        $data['role'] = $this->roleServer->allRole($gets['page'],$this->pageSize);
        $data['rolename'] = $this->_common();
        $this->assign('data',$data);
        $this->setView('role/roleAll');
    }

    /**
     * 公共
    */
    private function _common()
    {
        //所有权限数据 ['角色ID'=>'对应权限ID']
        $auth = $this->roleServer->authority();
        //权限菜单配置
        $main = $this->mainMenu();
        // ['角色ID'=>'对应权限文字']
        $data = $this->roleServer->handle($auth,$main);
        return $data;
    }

    /**
     * 修改入口
     * */
    public function updateRole(HttpRequest $request)
    {
        $gets = $request->getParameters();
        $data['roleDate'] = $this->roleServer->roleDate($gets['id']);
        $data['authority'] = $this->roleServer->authID($gets['id']);
        $data['menu'] = $this->menu;
        $role = $this->mainMenu();
        $data['role'] = $this->roleServer->convert($role);
        $this->assign('data',$data);
        $this->setView('role/updateRole');
    }
    /**
     * 修改操作
     * */
    public function update(HttpRequest $request)
    {
        $gets = $request->getParameters();
        $data['tip']  =$this->roleServer->updateRole($gets);
        $data['page'] = $this->nowPage($this->count,$this->pageSize,$gets['page']);
        $data['menu'] = $this->menu;
        $data['role'] = $this->roleServer->allRole($gets['page'],$this->pageSize);
        $data['rolename'] = $this->_common();
        $this->assign('data',$data);
        $this->setView('role/roleAll');
    }


    /**
     * 删除操作
     * */
    public function delRole(HttpRequest $request)
    {
        $gets = $request->getParameters();
        $data['menu'] = $this->menu;
        $data['tip']    = $this->roleServer->deleteRole($gets['id']);
        $data['page'] = $this->nowPage($this->count,$this->pageSize,$gets['page']);
        $data['role'] = $this->roleServer->allRole($gets['page'],$this->pageSize);
        $data['rolename'] = $this->_common();
        $this->assign('data',$data);
        $this->setView('role/roleAll');
    }



}