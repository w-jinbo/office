<?php

/*
 * 文具申请控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-26 08:42:53 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-05 15:43:35
 */

namespace app\admin\action;

use herosphp\core\Loader;
use app\admin\service\StationeryService;
use app\admin\service\StationeryApplyService;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;
use app\admin\dao\StationeryApplyDao;
use app\admin\dao\StationeryApplyItemDao;
use app\admin\service\SystemTipService;

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
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $status = $request->getIntParam('status');
        $searchDate = $request->getParameter('searchDate', 'trim|urldecode');
        $type = $request->getIntParam('type');
        $searchDateArr = explode(' - ', $searchDate);
        $data = $this->stationeryApplyService->getListData($keyword, $status, 
            $searchDateArr, $type, $page, $pageSize);

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
        $this->chkPermissionWeb('stationery_apply_audit');
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->stationeryApplyService->getApplyInfo($applyId);
        if (empty($applyInfo)) {
            $this->error('没有找到对应的记录');
        }
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
        $this->chkPermissionWeb('stationery_apply_grant');
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->stationeryApplyService->getApplyInfo($applyId);
        if (empty($applyInfo)) {
            $this->error('没有找到对应的记录');
        }
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
        $data = $this->getParams($request);
        if (empty($data['item'])) {
            JsonResult::fail('请选择要申请的项目');
        }

        $admin = $this->admin;
        $result = $this->stationeryApplyService->addApply($admin->getId(), $data['apply_reason'], $data['item']);
        if (!$result) {
            JsonResult::fail('申请失败');
        }
        $this->addSystemTip(SystemTipService::STATIONERY_APPLY, $result);
        JsonResult::success('申请成功');
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
        $data = $this->getParams($request);
        $admin = $this->admin;
        $result = $this->stationeryApplyService->auditApply($applyId, $admin->getId(), 
            $admin['realname'], $data['status'], $data['audit_opinion']);
        if ($result <= 0) {
            JsonResult::fail('审批失败');
        }
        $this->addSystemTip(SystemTipService::STATIONERY_RESULT, $applyId, $applyInfo['user_id']);
        JsonResult::success('审批成功');
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
        $data = $this->getParams($request);
        $result = $this->stationeryApplyService->grant($applyId, $data['item'], $data['grant_remark']);
        if (!$result) {
            JsonResult::fail('发放失败');
        }
        JsonResult::success('发放成功');
    }

    /**
     * 获取表单数据并校验
     *
     * @param HttpRequest $request
     * @return array|string
     */
    private function getParams(HttpRequest $request) {
        $reason = $request->getParameter('apply_reason', 'trim|urldecode');
        $item = $request->getParameter('item');
        $status = $request->getIntParam('status');
        $opinion = $request->getParameter('audit_opinion', 'trim|urldecode');
        $remark = $request->getParameter('grant_remark', 'trim|urldecode');

        $params = array();
        !empty($reason) ? $params['apply_reason'] = $reason : '';
        !empty($status) ? $params['status'] = $status : '';
        !empty($opinion) ? $params['audit_opinion'] = $opinion : '';
        !empty($remark) ? $params['grant_remark'] = $remark : '';
        if (!empty($params)) {
            $data = $this->dataFilter(StationeryApplyDao::$filter, $params);
            if (!is_array($data)) {
                JsonResult::fail($data);
            }
        }

        if (!empty($item)) {
            $itemData = array();
            $filter = StationeryApplyItemDao::$filter;
            foreach ($item as $k => $v) {
                $param = array();
                !empty($v['id']) ? $param['stationery_id'] = $v['id'] : '';
                !empty($v['apply_item_id']) ? $param['apply_item_id'] = $v['apply_item_id'] : '';
                !empty($v['name']) ? $param['stationery_name'] = $v['name'] : '';
                !empty($v['unit']) ? $param['stationery_unit'] = $v['unit'] : '';
                !empty($v['num']) ? $param['apply_num'] = $v['num'] : '';
                !empty($v['grant_num']) ? $param['grant_num'] = $v['grant_num'] : '';
                $temp = $this->dataFilter($filter, $param);
                if (!is_array($temp)) {
                    JsonResult::fail($temp);
                }
                $itemData[$k] = $temp;
            }
            $data['item'] = $itemData;
        }

        return $data;
    }
}