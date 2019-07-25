<?php

/*
 * 办公室管理控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:35:31 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:54:25
 */

namespace app\admin\action;

use herosphp\core\Loader;
use app\admin\service\OfficeService;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;

class OfficeAction extends BaseAction {
    protected $officeService ;
    public function __construct() {
        parent::__construct();
        $this->officeService = Loader::service(OfficeService::class);
    }

    /**
     * 办公室列表页
     *
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);
        $this->assign('dataUrl', url('/admin/office/getListData?keyword=' . urlencode($keyword)));
        $this->setView('office/index');
    }

    /**
     * 获取办公室列表数据
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function getListData(HttpRequest $request) {
        $data = $this->officeService->getListData($request);

        $request = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $request->setData($data['list']);
        $request->setCount($data['total']);
        $request->setPage($data['page']);
        $request->setPagesize($data['pageSize']);
        $request->output();
    }

    /**
     * 新增办公室页面
     */
    public function add() {
        $this->setView('office/add');
    }

    /**
     * 修改办公室信息页面
     *
     * @param HttpRequest $request
     */
    public function edit(HttpRequest $request) {
        $officeId = $request->getStrParam('id');
        $office = $this->officeService->findById($officeId);
        $this->assign('office', $office);
        $this->setView('office/edit');
    }

    /**
     * 新增操作
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doAdd(HttpRequest $request) {
        if (!$this->chkPermission('office_list_add')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        if (empty($params['summary'])) {
            unset($params['summary']);
        }
        $result = $this->officeService->addRow($params);
        $result->output();
    }

    /**
     * 更新操作
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doEdit(HttpRequest $request) {
        if (!$this->chkPermission('office_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        if (empty($params['summary'])) {
            unset($params['summary']);
        }
        $result = $this->officeService->updateRow($params);
        $result->output();
    }

    /**
     * 删除操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doDel(HttpRequest $request) {
        if (!$this->chkPermission('office_list_del')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getStrParam('ids');
        if (empty($params)) {
            JsonResult::fail('请选择要删除的记录');
        }
        $result = $this->officeService->delRows($params);
        $result->output();
    }

    /**
     * 修改记录状态
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doChangeValid(HttpRequest $request) {
        if (!$this->chkPermission('office_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        $res = parent::changeValid($params['id'], $params['valid'], $this->officeService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }
}