<?php

/*
 * 假期申请管理控制器
 * @Author: WangJinBo <wangjb@pvc123.xom> 
 * @Date: 2019-07-25 17:39:18 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:39:50
 */

namespace app\admin\action;

use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use app\admin\service\VacationApplyService;
use app\admin\service\VacationService;
use herosphp\utils\JsonResult;

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
        $this->assign('dataUrl', '/admin/vacationApply/getListData?type=' . $type 
            . '&searchDate=' . urlencode($searchDate) . '&keyword=' . urlencode($keyword) . '&status=' . $status);
        $this->setView('vacation_apply/index');
    }

    /**
     * 获取列表数据接口
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function getListData(HttpRequest $request) {
        $data = $this->vacationApplyService->getListData($request);

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
        if (!$this->chkPermission('vacation_apply_add')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        //判断用户申请时间是否合法
        $beginDate = $params['apply_begin_date'];
        $endDate = $params['apply_end_date'];
        $beginPeriod = $params['apply_begin_period'];
        $endPeriod = $params['apply_end_period'];

        if(!isDateValid($beginDate) || !isDateValid($endDate)) {
            JsonResult::fail('申请时间不合法，请重新选择');
        }

        if ($beginDate > $endDate) {
            JsonResult::fail('申请的开始时间不能大于结束时间');
        }
        if ($beginDate == $endDate && $beginPeriod > $endPeriod) {
            JsonResult::fail('申请的开始时间不能大于结束时间');
        }

        $result = $this->vacationApplyService->addApply($params);
        $result->output();
    }

    /**
     * 审批操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doAudit(HttpRequest $request) {
        if (!$this->chkPermission('vacation_apply_audit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->vacationApplyService->findById($applyId);
        if (!$applyInfo) {
            JsonResult::fail('记录不存在，请刷新后重试');
        }
        if ($applyInfo['status'] != VacationApplyService::APPLYING) {
            JsonResult::fail('该申请目前状态不支持取消');
        }
        $update = array();
        $update['status'] = $request->getIntParam('status');
        $update['audit_user_id'] = $this->admin['id'];
        $update['audit_user_realname'] = $this->admin['realname'];
        $update['audit_opinion'] = $request->getStrParam('audit_opinion');
        $date = date('Y-m-d H:i:s');
        $update['audit_time'] = $date;
        $update['update_time'] = $date;
        $result = $this->vacationApplyService->auditApply($update, $applyId);
        $result->output();
    }

    /**
     * 取消申请操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doCancel(HttpRequest $request) {
        if (!$this->chkPermission('vacation_apply_cancel')) {
            JsonResult::fail('您没有权限进行此操作');
        }
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
}