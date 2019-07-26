<?php

/*
 * 文具申请控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-26 08:42:53 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-26 17:40:21
 */

namespace app\admin\action;

use herosphp\core\Loader;
use app\admin\service\StationeryService;
use app\admin\service\StationeryApplyService;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;

class StationeryApplyAction extends BaseAction {
    
    protected $stationeryService ;
    protected $stationeryApplyService ;

    public function __construct() {
        parent::__construct();
        $this->stationeryService = Loader::service(StationeryService::class);
        $this->stationeryApplyService = Loader::service(StationeryApplyService::class);
    }

    /**
     * 申请/审批列表页
     *
     * @param HttpRequest $request
     * @return void
     */
    public function index(HttpRequest $request) {
        $searchDate = $request->getParameter('searchDate','trim|urldecode');
        if (empty($searchDate)){
            $beginDate = date('Y-m-d',strtotime('-15 day'));
            $endDate = date('Y-m-d',strtotime('+15 day'));
            $searchDate = $beginDate . ' - ' . $endDate;
        }
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        //列表类型，1：我的申请，2：审批列表
        $type = $request->getStrParam('type');
        $status = $request->getStrParam('status');
        $this->assign('statusList', StationeryApplyService::getStatusArr());
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('type', $type);
        $this->assign('searchDate', $searchDate);
        $this->assign('dataUrl', '/admin/stationeryApply/getListData?type=' . $type 
            . '&searchDate=' . urlencode($searchDate) . '&keyword=' . urlencode($keyword) . '&status=' . $status);
        $this->setView('stationery_apply/index');
    }

    /**
     * 获取列表数据
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function getListData(HttpRequest $request) {
        $data = $this->stationeryApplyService->getListData($request);

        $result = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $result->setData($data['list']);
        $result->setCount($data['total']);
        $result->setPage($data['page']);
        $result->setPagesize($data['pageSize']);
        $result->output();
    }

    /**
     * 新增申请页面
     *
     * @return void
     */
    public function add() {
        $stationeryList = $this->stationeryService->stationeryList();
        $this->assign('stationeryList', $stationeryList);
        $this->setView('stationery_apply/add');
    }

    /**
     * 申请详情页面
     *
     * @param HttpRequest $request
     * @return void
     */
    public function detail(HttpRequest $request) {
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->stationeryApplyService->getApplyInfo($applyId);
        $this->assign('applyInfo', $applyInfo);
        $this->setView('stationery_apply/detail');
    }

    /**
     * 审核页面
     *
     * @param HttpRequest $request
     * @return void
     */
    public function audit(HttpRequest $request) {
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->stationeryApplyService->getApplyInfo($applyId);
        $this->assign('applyInfo', $applyInfo);
        $this->assign('auditFlag', true);
        $this->setView('stationery_apply/detail');
    }

    /**
     * 发放文具页面
     *
     * @param HttpRequest $request
     * @return void
     */
    public function grant(HttpRequest $request) {
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->stationeryApplyService->getApplyInfo($applyId);
        $this->assign('applyInfo', $applyInfo);
        $this->setView('stationery_apply/grant');
    }

    /**
     * 新增操作
     *
     * @param HttpRequest $request
     * @return void
     */
    public function doAdd(HttpRequest $request) {
        if (!$this->chkPermission('stationery_apply_add')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        if (empty($params)) {
            JsonResult::fail('请选择要申请的项目');
        }
        $result = $this->stationeryApplyService->addApply($params);
        $result->output();
    }

    /**
     * 审批操作
     *
     * @param HttpRequest $request
     * @return void
     */
    public function doAudit(HttpRequest $request) {
        if (!$this->chkPermission('stationery_apply_audit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->stationeryApplyService->findById($applyId);
        if (!$applyInfo) {
            JsonResult::fail('记录不存在，请刷新后重试');
        }
        if ($applyInfo['status'] != StationeryApplyService::APPLIED) {
            JsonResult::fail('该申请目前状态不支持审批');
        }
        $update = array();
        $update['status'] = $request->getIntParam('status');
        $update['audit_user_id'] = $this->admin['id'];
        $update['audit_user_realname'] = $this->admin['realname'];
        $update['audit_opinion'] = $request->getStrParam('audit_opinion');
        $date = date('Y-m-d H:i:s');
        $update['audit_time'] = $date;
        $update['update_time'] = $date;
        $result = $this->stationeryApplyService->auditApply($update, $applyId);
        $result->output();
    }

    /**
     * 发放文具操作
     *
     * @param HttpRequest $request
     * @return void
     */
    public function doGrant(HttpRequest $request) {
        if (!$this->chkPermission('stationery_apply_grant')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->stationeryApplyService->findById($applyId);
        if (!$applyInfo) {
            JsonResult::fail('记录不存在，请刷新后重试');
        }
        if ($applyInfo['status'] != StationeryApplyService::UNCLAIMED) {
            JsonResult::fail('该申请目前状态不支持发放');
        }
        $update['grant_remark'] = $request->getStrParam('grant_remark');
        $date = date('Y-m-d H:i:s');
        $update['grant_time'] = $date;
        $update['update_time'] = $date;
        $itemArr = $request->getParameter('item');
        $result = $this->stationeryApplyService->grant($update, $itemArr, $applyId);
        $result->output();
    }
}