<?php

/*
 * 办公室申请管理控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:36:26 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-01 16:45:07
 */

namespace app\admin\action;

use herosphp\core\Loader;
use app\admin\service\OfficeService;
use app\admin\service\OfficeApplyService;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;
use app\admin\dao\OfficeApplyDao;
use app\admin\service\SystemTipService;

class OfficeApplyAction extends BaseAction {

    protected $officeService ;
    protected $officeApplyService ;

    public function __construct() {
        parent::__construct();
        $this->officeService = Loader::service(OfficeService::class);
        $this->officeApplyService = Loader::service(OfficeApplyService::class);
    }

    /**
     * 办公室申请列表页
     *
     * @param HttpRequest $request
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
        $this->assign('statusList', OfficeApplyService::getStatusArr());
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('type', $type);
        $this->assign('searchDate', $searchDate);
        $this->assign('dataUrl', '/admin/officeApply/getListData?type=' . $type 
            . '&searchDate=' . urlencode($searchDate) . '&keyword=' . urlencode($keyword) . '&status=' . $status);
        $this->setView('office_apply/index');
    }

    /**
     * 列表页数据获取
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
        $data = $this->officeApplyService->getListData($keyword, $status, 
            $searchDateArr, $type, $page, $pageSize);

        $result = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $result->setData($data['list']);
        $result->setCount($data['total']);
        $result->setPage($data['page']);
        $result->setPagesize($data['pageSize']);
        $result->output();
    }

    /**
     * 添加办公室申请页面
     */
    public function add() {
        $officeList = $this->officeService->officeList();
        $this->assign('officeList', $officeList);
        $this->setView('office_apply/add');
    }

    /**
     * 办公室预约情况
     *
     * @param HttpRequest $request
     */
    public function officeBook(HttpRequest $request) {
        $officeId = $request->getIntParam('id');
        $searchDate = $request->getStrParam('searchDate');

        //设置默认时间
        $beginDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1month'));
        if (!empty($searchDate)) {
            $dateArr = explode(' - ', $searchDate);
            $beginDate = $dateArr[0];
            $endDate = $dateArr[1];
        }
        $list = $this->officeApplyService->getOfficeBookEdById($officeId, $beginDate, $endDate);
        $this->assign('bookList', $list);
        $this->assign('searchDate', $beginDate. ' - ' . $endDate);
        $this->assign('id', $officeId);
        $this->setView('office_apply/office_book');
    }

    /**
     * 根据时间查询可以预约的办公室
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function searchOfficeByTime(HttpRequest $request) {
        $result = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $data = $this->officeService->officeList();
        $params = $request->getParameters();

        //判断用户申请时间是否合法
        $applyDate = $params['apply_date'];
        $beginTime = $params['begin_time'];
        $endTime = $params['end_time'];
        $this->chkApplyDate($applyDate, $beginTime, $endTime);

        //根据时间查询已被预约的办公室id集合
        $officeIds = $this->officeApplyService->searchBookEdOffice($applyDate, $beginTime, $endTime);
        if (!empty($officeIds)) {
            $data = $this->officeService
                ->fields('id, name')
                ->where('id', 'nin', $officeIds)
                ->where('is_valid', 1)
                ->find();
        }
        $result->setData($data);
        $result->output();
    }

    /**
     * 申请详情页
     *
     * @param HttpRequest $request
     */
    public function detail(HttpRequest $request) {
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->officeApplyService->getApplyInfo($applyId);
        if (empty($applyInfo)) {
            $this->error('没有找到对应的记录');
        }
        $this->assign('applyInfo', $applyInfo);
        $this->setView('office_apply/detail');
    }

    /**
     * 申请拒绝、关闭页面
     *
     * @param HttpRequest $request
     */
    public function audit(HttpRequest $request) {
        $this->chkPermissionWeb('office_apply_audit');
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->officeApplyService->getApplyInfo($applyId);
        if (empty($applyInfo)) {
            $this->error('没有找到对应的记录');
        }
        $this->assign('applyInfo', $applyInfo);
        $this->assign('auditFlag', true);
        $this->setView('office_apply/detail');
    }

    /**
     * 新增申请操作
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doAdd(HttpRequest $request) {
        if (!$this->chkPermission('office_apply_add')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $data  = $this->getParams($request);

        //判断用户申请时间是否合法
        $applyDate = $data['apply_date'];
        $beginTime = $data['apply_begin_time'];
        $endTime = $data['apply_end_time'];
        $this->chkApplyDate($applyDate, $beginTime, $endTime);

        $result = $this->officeApplyService->addApply($data['office_id'], $data['office_name'], 
            $data['apply_date'], $data['apply_begin_time'], $data['apply_end_time'], $data['apply_reason']);
        if ($result['success'] == false) {
            JsonResult::fail($result['message']);
        }
        JsonResult::success('申请成功');
    }

    /**
     * 更新申请操作
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doUpdate(HttpRequest $request) {
        if (!$this->chkPermission('office_apply_audit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->officeApplyService->findById($applyId);
        if (!$applyInfo) {
            JsonResult::fail('记录不存在，请刷新后重试');
        }
        if ($applyInfo['status'] == OfficeApplyService::APPLY_CANCEL || 
            $applyInfo['status'] == OfficeApplyService::APPLY_OVERDUE || 
            $applyInfo['status'] == OfficeApplyService::APPLY_REJECT) {
            JsonResult::fail('该申请目前状态不支持变更');
        }
        $status = OfficeApplyService::APPLY_REJECT;
        if ($applyInfo['status'] == OfficeApplyService::IN_USE) {
            $status = OfficeApplyService::APPLY_CANCEL;
        }
        $admin = $this->admin;
        $opinion = $request->getStrParam('audit_opinion');
        $result = $this->officeApplyService->updateApply($admin['id'], $admin['realname'], $status, $opinion, $applyId);
        if ($result <=0 ) {
            JsonResult::fail('审批失败');
        }
        $this->addSystemTip(SystemTipService::OFFICE_RESULT, $applyId, $applyInfo['user_id']);
        JsonResult::success('审批成功');
    }

    /**
     * 校验申请时间是否合法
     *
     * @param string $date 日期
     * @param string $begin 开始时间
     * @param string $end 结束时间
     * @return void
     */
    private function chkApplyDate(string $date, string $begin, string $end) {
        if(!isDateValid($date)) {
            JsonResult::fail('申请日期不合法，请重新选择');
        }

        $today = date('Y-m-d');
        if ($date < $today) {
            JsonResult::fail('不能申请已过去的日期，请重新选择');
        }

        $now = date('H:i:s');
        if ($today == $date && ($begin < $now || $end < $now)) {
            JsonResult::fail('申请时间不合法，请重新选择');
        }

        if ($begin > $end) {
            JsonResult::fail('申请的开始时间不能大于结束时间');
        }
    }

    /**
     * 获取表单数据并校验
     *
     * @param HttpRequest $request
     * @return array|string
     */
    private function getParams(HttpRequest $request) {
        $officeName = $request->getStrParam('office_name');
        $officeId = $request->getIntParam('office_id');
        $applyDate = $request->getStrParam('apply_date');
        $applyBeginTime = $request->getStrParam('apply_begin_time');
        $applyEndTime = $request->getStrParam('apply_end_time');
        $applyReason = $request->getStrParam('apply_reason');
        $auditOpinion = $request->getStrParam('audit_opinion');

        $params = array();
        !empty($officeName) ? $params['office_name'] = $officeName : '';
        !empty($officeId) ? $params['office_id'] = $officeId : '';
        !empty($applyDate) ? $params['apply_date'] = $applyDate : '';
        !empty($applyBeginTime) ? $params['apply_begin_time'] = $applyBeginTime : '';
        !empty($applyEndTime) ? $params['apply_end_time'] = $applyEndTime : '';
        !empty($applyReason) ? $params['apply_reason'] = $applyReason : '';
        !empty($auditOpinion) ? $params['audit_opinion'] = $auditOpinion : '';
        
        $data = $this->dataFilter(OfficeApplyDao::$filter, $params);
        if (!is_array($data)) {
            JsonResult::fail($data);
        }
        return $data;
    }
}