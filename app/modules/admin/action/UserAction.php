<?php


namespace app\admin\action;


use app\admin\service\RoleService;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;

class UserAction extends BaseAction {
    protected $roleService ;
    public function __construct() {
        parent::__construct();
        $this->roleService = Loader::service(RoleService::class);
    }

    /**
     * 用户列表页面
     *
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);
        $this->assign('dataUrl', url('/admin/user/getListData?keyword=' . urlencode($keyword)));
        $this->setView('user/index');
    }

    /**
     * 获取用户列表数据接口
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function getListData(HttpRequest $request) {
        $data = $this->userService->getListData($request);

        $result = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $result->setData($data['list']);
        $result->setCount($data['total']);
        $result->setPage($data['page']);
        $result->setPagesize($data['pageSize']);
        $result->output();
        
    }

    /**
     * 增加用户页面
     */
    public function add() {
        $roleList = $this->roleService->roleList();
        $this->assign('roleList', $roleList);
        $this->setView('user/add');
    }

    /**
     * 修改用户信息页面
     *
     * @param HttpRequest $request
     */
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

    /**
     * 重置密码页面
     *
     * @param HttpRequest $request
     */
    public function resetPwd(HttpRequest $request) {
        $userId = $request->getStrParam('id');
        $user = $this->userService->findById($userId);
        $this->assign('user', $user);
        $this->setView('user/reset_pwd');
    }

    /**
     * 增加用户操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doAdd(HttpRequest $request) {
        if (!$this->chkPermission('user_list_add')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        $result = $this->userService->addUser($params);
        $result->output();
    }

    /**
     * 修改用户信息操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doEdit(HttpRequest $request) {
        if (!$this->chkPermission('user_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $userId = $request->getStrParam('id');
        $params = $request->getParameters();
        unset($params['id']);
        $result = $this->userService->updateUser($params, $userId);
        $result->output();
    }

    /**
     * 重置密码操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doResetPwd(HttpRequest $request) {
        if (!$this->chkPermission('user_list_reset_pwd')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
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

    /**
     * 删除用户操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doDel(HttpRequest $request){
        if (!$this->chkPermission('user_list_del')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getStrParam('ids');
        if (empty($params)) {
            JsonResult::fail('请选择要删除的记录');
        }
        $result = $this->userService->delUsers($params);
        $result->output();
    }

    /**
     * 修改用户状态接口
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doChangeValid(HttpRequest $request) {
        if (!$this->chkPermission('user_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        $res = parent::changeValid($params['id'], $params['valid'], $this->userService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }

    /**
     * 判断电子邮箱是否被注册
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
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