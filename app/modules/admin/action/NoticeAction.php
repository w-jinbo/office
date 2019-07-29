<?php

namespace app\admin\action;

use herosphp\core\Loader;
use app\admin\service\NoticeService;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;

class NoticeAction extends BaseAction {

    protected $noticeService ;

    public function __construct() {
        parent::__construct();
        $this->noticeService = Loader::service(NoticeService::class);
    }

    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);
        $this->assign('dataUrl', url('/admin/notice/getDataList?keyword=' . urlencode($keyword)));
        $this->setView('notice/index');
    }

    public function getDataList(HttpRequest $request) {
        $data = $this->noticeService->getListData($request);

        $result = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $result->setData($data['list']);
        $result->setCount($data['total']);
        $result->setPage($data['page']);
        $result->setPagesize($data['pageSize']);
        $result->output();
    }

    public function add() {
        $this->setView('notice/add');
    }

    public function edit(HttpRequest $request) {
        $id = $request->getIntParam('id');
        $notice = $this->noticeService->findById($id);
        $this->assign('notice', $notice);
        $this->setView('notice/edit');
    }

    public function doAdd(HttpRequest $request) {exit;
        if (!$this->chkPermission('notice_list_add')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        if (empty($params['summary'])) {
            unset($params['summary']);
        }
        $result = $this->noticeService->addRow($params);
        $result->output();
    }

    /**
     * 修改公告信息操作
     *
     * @param HttpRequest $request
     */
    public function doEdit(HttpRequest $request) {
        if (!$this->chkPermission('notice_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        if (empty($params['summary'])) {
            unset($params['summary']);
        }
        $result = $this->noticeService->updateRow($params);
        $result->output();
    }

    /**
     * 删除公告操作
     *
     * @param HttpRequest $request
     */
    public function doDel(HttpRequest $request){
        if (!$this->chkPermission('notice_list_del')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getStrParam('ids');
        if (empty($params)) {
            JsonResult::fail('请选择要删除的记录');
        }
        $result = $this->noticeService->delRows($params);
        $result->output();
    }

    /**
     * 修改公告状态接口
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doChangeValid(HttpRequest $request) {
        if (!$this->chkPermission('notice_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        $res = parent::changeValid($params['id'], $params['valid'], $this->noticeService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }
}