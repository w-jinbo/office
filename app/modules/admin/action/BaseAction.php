<?php


namespace app\admin\action;


use app\admin\service\UserService;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\session\Session;
use herosphp\utils\JsonResult;

class BaseAction extends Controller {
    protected $userService ;
    public function __construct() {
        parent::__construct();
        Session::start();
        $this->userService = Loader::service(UserService::class);
        $this->isLogin();
    }

    /**
     * 判断用户是否已经登录
     */
    public function isLogin() {
        $userId=Session::get('user_id');
        if (!$userId) {
            location('/admin/login/index');
            die();
        }
    }

    /**
     * 获取列表数据
     * @param $service
     * @param $request
     * @return JsonResult
     */
    public function getDataList($service, $request) {
        $result = new JsonResult(JsonResult::CODE_SUCCESS);
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $total = $service->count();
        $data = $service->page($page, $pageSize)->order('id desc')->find();
        $result->setData($data);
        $result->setCount($total);
        $result->setPage($page);
        $result->setPagesize($pageSize);
        $result->setMessage('获取数据成功');
        return $result;
    }

    /**
     * 修改记录的是否有效属性
     * @param $id
     * @param $valid
     * @param $service
     * @return int
     */
    protected function changeValid($id, $valid, $service) {
        $res = 0;
        if ($valid == 1) {
            $res = $service->increase('is_valid', 1, $id);
        } else {
            $res = $service->reduce('is_valid', 1, $id);
        }
        return $res;
    }
}