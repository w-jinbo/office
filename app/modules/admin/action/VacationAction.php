<?php


namespace app\admin\action;

use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;
use app\admin\service\VacationService;

class VacationAction extends BaseAction {
    protected $vacationService ;
    public function __construct() {
        parent::__construct();
        $this->vacationService = Loader::service(VacationService::class);
    }

    /**
     * 假期类型列表页
     *
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);
        $this->assign('dataUrl', url('/admin/vacation/getListData?keyword=' . urlencode($keyword)));
        $this->setView('vacation/index');
    }

    /**
     * 获取列表页数据接口
     *
     * @param HttpRequest $request
     */
    public function getListData(HttpRequest $request) {
        $data = $this->vacationService->getListData($request);

        $request = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $request->setData($data['list']);
        $request->setCount($data['total']);
        $request->setPage($data['page']);
        $request->setPagesize($data['pageSize']);
        $request->output();
    }

    /**
     * 新增假期类型页面
     */
    public function add() {
        $this->setView('vacation/add');
    }

    /**
     * 修改假期类型信息页面
     *
     * @param HttpRequest $request
     */
    public function edit(HttpRequest $request) {
        $vacationId = $request->getStrParam('id');
        $vacation = $this->vacationService->findById($vacationId);
        $this->assign('vacation', $vacation);
        $this->setView('vacation/edit');
    }

    /**
     * 新增假期操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doAdd(HttpRequest $request) {
        if (!$this->chkPermission('vacation_list_add')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        $result = $this->vacationService->addRow($params);
        $result->output();
    }

    /**
     * 修改假期信息操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doEdit(HttpRequest $request) {
        if (!$this->chkPermission('vacation_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        $result = $this->vacationService->updateRow($params);
        $result->output();
    }

    /**
     * 删除操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doDel(HttpRequest $request) {
        if (!$this->chkPermission('vacation_list_del')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getStrParam('ids');
        if (empty($params)) {
            JsonResult::fail('请选择要删除的记录');
        }
        $result = $this->vacationService->delRows($params);
        $result->output();
    }

    /**
     * 修改用户状态接口
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doChangeValid(HttpRequest $request) {
        if (!$this->chkPermission('vacation_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        $res = parent::changeValid($params['id'], $params['valid'], $this->vacationService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }
}