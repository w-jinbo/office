<?php


namespace app\admin\action;


use app\admin\service\RoleService;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;

class UserAction extends BaseAction {
    private $roleService ;
    public function __construct() {
        parent::__construct();
        $this->roleService = Loader::service(RoleService::class);
    }

    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);
        $this->assign('dataUrl', url('/admin/user/getListData'));
        $this->setView('user/index');
    }

    public function getListData(HttpRequest $request) {
        $result = parent::getDataList($this->userService, $request);
        $result->output();
    }

    public function add() {
        $roleList = $this->roleService->roleList();
        $this->assign('roleList', $roleList);
        $this->setView('user/add');
    }

    public function doChangeValid(HttpRequest $request) {
        $params = $request->getParameters();
        $res = $this->changeValid($params['id'], $params['valid'], $this->userService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }
}