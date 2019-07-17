<?php


namespace app\admin\action;


use app\admin\model\AdminPower;
use app\admin\service\RoleService;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;

class RoleAction extends BaseAction {
    private $roleService ;
    public function __construct() {
        parent::__construct();
        $this->roleService = Loader::service(RoleService::class);
    }

    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);

        //设置数据接口
        $this->assign('dataUrl', url('/admin/role/getDataList'));
        $this->setView('role/index');
    }

    public function getDataList(HttpRequest $request) {
        $result = parent::getDataList($this->roleService, $request);
        $result->output();
    }

    public function add(HttpRequest $request) {
        $powerArray = AdminPower::getPowerArray();
        $power = $this->getPowerList('', $powerArray);
        $this->assign('powerArray', $power);
//        print_r($power);exit;
        $this->setView('role/add');
    }

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

    public function doAdd(HttpRequest $request) {
        $params = $request->getParameters();
        $result = $this->roleService->addRole($params);
        $result->output();
    }

    public function doEdit(HttpRequest $request) {
        $params = $request->getParameters();
        $result = $this->roleService->updateRole($params);
        $result->output();
    }

    public function doDel(HttpRequest $request){
        $params = $request->getStrParam('ids');
        if (empty($params)) {
            JsonResult::fail('请选择要删除的记录');
        }
        $result = $this->roleService->delRoles($params);
        $result->output();
    }

    public function doChangeValid(HttpRequest $request) {
        $params = $request->getParameters();
        $res = $this->changeValid($params['id'], $params['valid'], $this->roleService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }

    private function getPowerList($pkey, &$power) {
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