<?php

/*
 * 假期申请管理控制器
 * @Author: WangJinBo <wangjb@pvc123.xom> 
 * @Date: 2019-07-25 17:39:18 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-06 10:41:42
 */

namespace app\admin\action;

use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use app\admin\service\VacationApplyService;
use app\admin\service\VacationService;
use herosphp\utils\JsonResult;
use app\admin\dao\VacationApplyDao;
use app\admin\service\SystemTipService;

class VacationApplyAction extends BaseAction {

    protected $vacationApplyService ;
    protected $vacationService ;

    public function __construct() {
        parent::__construct();
        $this->vacationApplyService = Loader::service(VacationApplyService::class);
        $this->vacationService = Loader::service(VacationService::class);
    }

    /**
     * 假期申请列表页
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
        $this->assign('statusList', VacationApplyService::getStatusArr());
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('type', $type);
        $this->assign('searchDate', $searchDate);
        $this->assign('dataUrl', '/admin/vacationApply/findAll?type=' . $type 
            . '&searchDate=' . urlencode($searchDate) . '&keyword=' . urlencode($keyword) . '&status=' . $status);
        $this->setView('vacation_apply/index');
    }

    /**
     * 获取列表数据接口
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function findAll(HttpRequest $request) {
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $status = $request->getIntParam('status');
        $searchDate = $request->getParameter('searchDate', 'trim|urldecode');
        $type = $request->getIntParam('type');
        $searchDateArr = explode(' - ', $searchDate);
        $data = $this->vacationApplyService->getListData($keyword, $status, 
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
     */
    public function add() {
        $vacationList = $this->vacationService->vacationList();
        $this->assign('vacationList', $vacationList);
        $this->setView('vacation_apply/add');
    }

    /**
     * 申请详情页面
     *
     * @param HttpRequest $request
     */
    public function detail(HttpRequest $request) {
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->vacationApplyService->getApplyInfo($applyId);
        if (empty($applyInfo)) {
            $this->error('没有找到对应的记录');
        }
        $this->assign('applyInfo', $applyInfo);
        $this->setView('vacation_apply/detail');
    }

    /**
     * 审批页面
     *
     * @param HttpRequest $request
     */
    public function audit(HttpRequest $request) {
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->vacationApplyService->getApplyInfo($applyId);
        if (empty($applyInfo)) {
            $this->error('没有找到对应的记录');
        }
        $this->assign('applyInfo', $applyInfo);
        $this->assign('auditFlag', true);
        $this->setView('vacation_apply/detail');
    }

    /**
     * 增加申请
     *
     * @param HttpRequest $request
     * @return void
     */
    public function doAdd(HttpRequest $request) {
        $data = self::getParams($request);
        //判断用户申请时间是否合法
        $beginDate = $data['apply_begin_date'];
        $endDate = $data['apply_end_date'];
        $beginPeriod = $data['apply_begin_period'];
        $endPeriod = $data['apply_end_period'];

        if(!isDateValid($beginDate) || !isDateValid($endDate)) {
            JsonResult::fail('申请时间不合法，请重新选择');
        }

        if ($beginDate > $endDate) {
            JsonResult::fail('申请的开始时间不能大于结束时间');
        }
        if ($beginDate == $endDate && $beginPeriod > $endPeriod) {
            JsonResult::fail('申请的开始时间不能大于结束时间');
        }

        $result = $this->vacationApplyService->addApply($data['vacation_id'], $data['vacation_name'], 
            $data['apply_begin_date'], $data['apply_begin_period'], $data['apply_end_date'], 
            $data['apply_end_period'], $data['apply_reason']);
        if ($result['success'] == false) {
            JsonResult::fail($result['message']);
        }
        $this->addSystemTip(SystemTipService::VACATION_APPLY, $result['apply_id']);
        JsonResult::success('申请成功');
    }

    /**
     * 审批操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doAudit(HttpRequest $request) {
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->vacationApplyService->findById($applyId);
        if (!$applyInfo) {
            JsonResult::fail('记录不存在，请刷新后重试');
        }
        if ($applyInfo['status'] != VacationApplyService::APPLYING) {
            JsonResult::fail('该申请目前状态不支持审批');
        }
        $data = self::getParams($request);
        $result = $this->vacationApplyService->auditApply($applyId, $data['status'], $data['audit_opinion']);
        if ($result <= 0) {
            JsonResult::fail('审批失败');
        }
        $this->addSystemTip(SystemTipService::VACATION_RESULT, $applyId, $applyInfo['user_id']);
        JsonResult::success('审批成功');
    }

    /**
     * 取消申请操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doCancel(HttpRequest $request) {
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->vacationApplyService->findById($applyId);
        if (!$applyInfo) {
            JsonResult::fail('记录不存在，请刷新后重试');
        }
        if ($applyInfo['status'] != VacationApplyService::APPLYING) {
            JsonResult::fail('该申请目前状态不支持取消');
        }
        $update = array();
        $update['status'] = VacationApplyService::APPLY_CANCEL;
        $update['update_time'] = date('Y-m-d H:i:s');
        $res = $this->vacationApplyService->update($update, $applyId);
        if ($res > 0) {
            JsonResult::success('取消成功');
        }
        JsonResult::fail('系统开了小差，请稍后重试');
    }

    /**
     * 获取表单数据并校验
     *
     * @param HttpRequest $request
     * @return void
     */
    private function getParams(HttpRequest $request) {
        $vacationName = $request->getStrParam('vacation_name');
        $vacationId = $request->getIntParam('vacation_id');
        $applyBeginDate = $request->getStrParam('apply_begin_date');
        $applyBeginPeriod = $request->getIntParam('apply_begin_period');
        $applyEndDate = $request->getStrParam('apply_end_date');
        $applyEndPeriod = $request->getIntParam('apply_end_period');
        $applyReason = $request->getStrParam('apply_reason');
        $status = $request->getIntParam('status');
        $auditOpinion = $request->getStrParam('audit_opinion');

        $params = array();
        !empty($vacationName) ? $params['vacation_name'] = $vacationName : '';
        !empty($vacationId) ? $params['vacation_id'] = $vacationId : '';
        !empty($applyBeginDate) ? $params['apply_begin_date'] = $applyBeginDate : '';
        !empty($applyBeginPeriod) ? $params['apply_begin_period'] = $applyBeginPeriod : '';
        !empty($applyEndDate) ? $params['apply_end_date'] = $applyEndDate : '';
        !empty($applyEndPeriod) ? $params['apply_end_period'] = $applyEndPeriod : '';
        !empty($applyReason) ? $params['apply_reason'] = $applyReason : '';
        !empty($status) ? $params['status'] = $status : '';
        !empty($auditOpinion) ? $params['audit_opinion'] = $auditOpinion : '';

        $data = $this->dataFilter(VacationApplyDao::$filter, $params);
        if (!is_array($data)) {
            JsonResult::fail($data);
        }
        return $data;
    }
}