<?php

/*
 * 文具管理控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 16:44:18 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:59:58
 */

namespace app\admin\action;

use herosphp\core\Loader;
use app\admin\service\StationeryService;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;

class StationeryAction extends BaseAction {
    protected $stationeryService ;
    public function __construct() {
        parent::__construct();
        $this->stationeryService = Loader::service(StationeryService::class);
    }

    /**
     * 文具列表页
     *
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);
        $this->assign('dataUrl', url('/admin/stationery/getListData?keyword=' . urlencode($keyword)));
        $this->setView('stationery/index');
    }

    /**
     * 获取列表页数据接口
     *
     * @param HttpRequest $request
     */
    public function getListData(HttpRequest $request) {
        $data = $this->stationeryService->getListData($request);

        $request = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $request->setData($data['list']);
        $request->setCount($data['total']);
        $request->setPage($data['page']);
        $request->setPagesize($data['pageSize']);
        $request->output();
    }

    /**
     * 新增文具页面
     */
    public function add() {
        $this->setView('stationery/add');
    }

    /**
     * 修改文具信息页面
     *
     * @param HttpRequest $request
     */
    public function edit(HttpRequest $request) {
        $stationeryId = $request->getStrParam('id');
        $stationery = $this->stationeryService->findById($stationeryId);
        $this->assign('stationery', $stationery);
        $this->setView('stationery/edit');
    }

    /**
     * 新增文具操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doAdd(HttpRequest $request) {
        if (!$this->chkPermission('stationery_list_add')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        if (empty($params['summary'])) {
            unset($params['summary']);
        }
        $result = $this->stationeryService->addRow($params);
        $result->output();
    }

    /**
     * 修改文具信息操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doEdit(HttpRequest $request) {
        if (!$this->chkPermission('stationery_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        if (empty($params['summary'])) {
            unset($params['summary']);
        }
        $result = $this->stationeryService->updateRow($params);
        $result->output();
    }

    /**
     * 删除操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doDel(HttpRequest $request) {
        if (!$this->chkPermission('stationery_list_del')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getStrParam('ids');
        if (empty($params)) {
            JsonResult::fail('请选择要删除的记录');
        }
        $result = $this->stationeryService->delRows($params);
        $result->output();
    }

    /**
     * 修改记录状态
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doChangeValid(HttpRequest $request) {
        if (!$this->chkPermission('stationery_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        $res = parent::changeValid($params['id'], $params['valid'], $this->stationeryService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }
}
