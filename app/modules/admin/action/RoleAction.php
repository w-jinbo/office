<?php

/*
 * 角色管理控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:37:10 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-06 11:02:32
 */

namespace app\admin\action;

use app\admin\service\RoleService;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;
use app\admin\dao\RoleDao;
use app\admin\dao\PermissionDao;

class RoleAction extends BaseAction {
    protected $roleService ;
    public function __construct() {
        parent::__construct();
        $this->roleService = Loader::service(RoleService::class);
    }

    /**
     * 角色列表页
     * 
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);

        //设置数据接口
        $this->assign('dataUrl', url('/admin/role/findAll?keyword=' . urlencode($keyword)));
        $this->setView('role/index');
    }

    /**
     * 获取角色列表数据接口
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function findAll(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $data = $this->roleService->getListData($keyword, $page, $pageSize);

        $result = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $result->setData($data['list']);
        $result->setCount($data['total']);
        $result->setPage($data['page']);
        $result->setPagesize($data['pageSize']);
        $result->output();
    }

    /**
     * 增加角色页面
     *
     * @param HttpRequest $request
     */
    public function add(HttpRequest $request) {
        $this->assignPower();
        $this->setView('role/add');
    }

    /**
     * 修改角色信息页面
     *
     * @param HttpRequest $request
     */
    public function edit(HttpRequest $request) {
        $id = $request->getStrParam('id');
        $role = $this->roleService->findById($id);
        if (empty($role)) {
            $this->error('没有找到对应的记录');
        }
        $role['power_array'] = explode(',', $role['permissions']);
        $this->assign('role', $role);
        $this->assign('isEdit', true);
        $this->assignPower();
        $this->setView('role/edit');
    }

    /**
     * 增加角色操作
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doAdd(HttpRequest $request) {
        $params = self::getParams($request);
        $data = $this->dataFilter(RoleDao::$filter, $params);

        if (!is_array($data)) {
            JsonResult::fail($data);
        }

        if (!isset($data['summary'])) {
            $data['summary'] = '';
        }
        
        $result = $this->roleService->addRole($data['name'], $data['is_valid'], $data['summary'], $data['permissions']);
        if ($result <= 0) {
            JsonResult::fail('添加失败');
        }
        JsonResult::success('添加成功');
    }

    /**
     * 修改角色信息操作
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doEdit(HttpRequest $request) {
        $roleId = $request->getIntParam('id');
        $params = self::getParams($request);
        $data = $this->dataFilter(RoleDao::$filter, $params);

        if (!is_array($data)) {
            JsonResult::fail($data);
        }

        if (!isset($data['summary'])) {
            $data['summary'] = '';
        }
        $result = $this->roleService->updateRole($roleId, $data['is_valid'], $data['summary'], $data['permissions']);
        if ($result <= 0) {
            JsonResult::fail('修改失败');
        }
        JsonResult::success('修改成功');
    }

    /**
     * 删除角色操作
     *
     * @param HttpRequest $request
     */
    public function doDel(HttpRequest $request){
        $ids = $request->getStrParam('ids');
        $result = $this->roleService->delRows($ids);
        if ($result <= 0) {
            JsonResult::fail('删除失败');
        }
        JsonResult::success('删除成功');
    }

    /**
     * 修改角色状态接口
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doChangeValid(HttpRequest $request) {
        $params = $request->getParameters();
        $res = parent::changeValid($params['id'], $params['valid'], $this->roleService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }

    /**
     * 获取表单提交的参数
     *
     * @param HttpRequest $request
     * @return array $params
     */
    private function getParams(HttpRequest $request) {
        $name = $request->getParameter('name', 'trim|urldecode');
        $summary = $request->getParameter('summary', 'trim|urldecode');
        $isValid = $request->getIntParam('is_valid');
        $permissions = $request->getParameter('permissions');
        
        $params = array(
            'is_valid' => $isValid,
            'permissions' => implode(',', (array)$permissions)
        );
        if (!empty($name)) {
            $params['name'] = $name;
        }
        if (!empty($summary)) {
            $params['summary'] = $summary;
        }
        return $params;
    }

    /**
     * 获取权限数据并赋值到模板
     *
     * @return void
     */
    private function assignPower() {
        //获取权限
        $permissionDao = Loader::model(PermissionDao::class);
        $power = $permissionDao->getPermission();
        $power = dealPermission('0', $power);
        $this->assign('powerArray', $power);
    }
}