<?php

/*
 * 角色管理控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:37:10 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-29 18:02:29
 */

namespace app\admin\action;


use app\admin\model\AdminPower;
use app\admin\service\RoleService;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;
use app\admin\dao\RoleDao;

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
        $this->assign('dataUrl', url('/admin/role/getDataList?keyword=' . urlencode($keyword)));
        $this->setView('role/index');
    }

    /**
     * 获取角色列表数据接口
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function getDataList(HttpRequest $request) {
        $data = $this->roleService->getListData($request);

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
        $powerArray = AdminPower::getPowerArray();
        $power = $this->getPowerList('', $powerArray);
        $this->assign('powerArray', $power);
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
        $role['power_array'] = explode(',', $role['permissions']);
        $this->assign('role', $role);

        //获取权限
        $powerArray = AdminPower::getPowerArray();
        $power = $this->getPowerList('', $powerArray);
        $this->assign('powerArray', $power);
        $this->setView('role/edit');
    }

    /**
     * 增加角色操作
     *
     * @param HttpRequest $request
     */
    public function doAdd(HttpRequest $request) {
        if (!$this->chkPermission('role_list_add')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $this->getParams($request);
        $data = $this->roleService->dataFilter(RoleDao::$filter, $params);

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
     */
    public function doEdit(HttpRequest $request) {
        if (!$this->chkPermission('role_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        if (empty($params['summary'])) {
            unset($params['summary']);
        }
        $result = $this->roleService->updateRole($params);
        $result->output();
    }

    /**
     * 删除角色操作
     *
     * @param HttpRequest $request
     */
    public function doDel(HttpRequest $request){
        if (!$this->chkPermission('role_list_del')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getStrParam('ids');
        if (empty($params)) {
            JsonResult::fail('请选择要删除的记录');
        }
        $result = $this->roleService->delRoles($params);
        $result->output();
    }

    /**
     * 修改角色状态接口
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doChangeValid(HttpRequest $request) {
        if (!$this->chkPermission('role_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        $res = parent::changeValid($params['id'], $params['valid'], $this->roleService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }

    private function getParams(HttpRequest $request) {
        $name = $request->getStrParam('name', 'trim|urldecode');
        $summary = $request->getStrParam('summary', 'trim|urldecode');
        $isValid = $request->getIntParam('is_valid');
        $permissions = $request->getParameter('permissions');
        
        $params = array(
            'name' => $name,
            'is_valid' => $isValid,
            'permissions' => implode(',', (array)$permissions)
        );
        if (!empty($summary)) {
            $params['summary'] = $summary;
        }
        return $params;
    }

    /**
     * 递归处理权限集合，构成成树状结构
     *
     * @param string $pkey
     * @param array $power
     * @return array $resData
     */
    private function getPowerList(string $pkey, array &$power) {
        $resData = array();
        foreach ($power as $k => $v) {
            if ($pkey === $v['pId']) {
                unset($power[$k]);
                $resData['sub'][] = array_merge($v, $this->getPowerList($v['id'], $power));
            }
        }
        return $resData;
    }
}