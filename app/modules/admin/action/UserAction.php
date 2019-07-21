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

    public function edit(HttpRequest $request) {
        $userId = $request->getStrParam('id');
        $user = $this->userService->findById($userId);
        $user['role_ids_arr'] = explode(',', $user['role_ids']);
        $roleList = $this->roleService->roleList();
        $this->assign('roleList', $roleList);
        $this->assign('isEdit', true);
        $this->assign('user', $user);
        $this->setView('user/edit');
    }

    public function resetPwd(HttpRequest $request) {
        $userId = $request->getStrParam('id');
        $user = $this->userService->findById($userId);
        $this->assign('user', $user);
        $this->setView('user/reset_pwd');
    }

    public function doAdd(HttpRequest $request) {
        $params = $request->getParameters();
        $result = $this->userService->addUser($params);
        $result->output();
    }

    public function doEdit(HttpRequest $request) {
        $userId = $request->getStrParam('id');
        $params = $request->getParameters();
        unset($params['id']);
        $result = $this->userService->updateUser($params, $userId);
        $result->output();
    }

    public function doResetPwd(HttpRequest $request) {
        $result = new JsonResult(JsonResult::CODE_FAIL);
        $userId = $request->getStrParam('id');
        $pwd = $request->getStrParam('new_pwd');
        $user = $this->userService->findById($userId);
        if (!$user) {
            $result->setMessage('没有找到用户信息');
            return $result;
        }
        $update = array();
        $salt = rand(1000, 9999);
        $update['password'] = md5(md5($pwd).$salt);
        $update['salt'] = $salt;
        $update['update_time'] = date('Y-m-d H:i:s');
        $res = $this->userService->update($update,$userId);
        if(!$res){
            //数据更新失败
            $result->setMessage('修改失败，请稍后重试');
            return $result;
        }
        //数据更新成功
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('修改成功');
        $result->output();
    }

    public function doDel(HttpRequest $request){
        $params = $request->getStrParam('ids');
        if (empty($params)) {
            JsonResult::fail('请选择要删除的记录');
        }
        $result = $this->userService->delUsers($params);
        $result->output();
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

    public function chkFirst(HttpRequest $request) {
        $userName = $request->getStrParam('username');
        if (empty($userName)) {
            JsonResult::fail('请输入电子邮箱地址');
        }
        $user = $this->userService->isUser($userName);
        if ($user) {
            JsonResult::fail('该电子邮箱已被注册');
        }
        JsonResult::success('该电子邮箱可以注册');
    }
}