<?php

/*
 * 办公室申请管理控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:36:26 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:37:01
 */

namespace app\admin\action;

use herosphp\core\Loader;
use app\admin\service\OfficeService;
use app\admin\service\OfficeApplyService;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;

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
        $data = $this->officeApplyService->getListData($request);

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
        $query = $this->officeApplyService;
        $list = $query
            ->alias('a')
            ->join('user b', MYSQL_JOIN_INNER)
            ->on('a.user_id = b.id')
            ->fields('a.office_name, a.apply_date, a.apply_begin_time, a.apply_end_time, a.status, b.realname, b.tel')
            ->where('status', 'in', [OfficeApplyService::APPLIED, OfficeApplyService::IN_USE])
            ->where('office_id', $officeId)
            ->where('apply_date', 'between', [$beginDate, $endDate])
            ->order('apply_date asc, apply_begin_time asc')
            ->find();
        if (!empty($list)) {
            $statusArr = OfficeApplyService::getStatusArr();
            foreach ($list as $k => $v) {
                $list[$k]['status_text'] = $statusArr[$v['status']];
                $list[$k]['use_time'] = $v['apply_date'] . '  ' .$v['apply_begin_time'] . ' ~ ' . $v['apply_end_time'];
            }
        }
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
        $this->assign('applyInfo', $applyInfo);
        $this->setView('office_apply/detail');
    }

    /**
     * 申请拒绝、关闭页面
     *
     * @param HttpRequest $request
     */
    public function audit(HttpRequest $request) {
        $applyId = $request->getIntParam('id');
        $applyInfo = $this->officeApplyService->getApplyInfo($applyId);
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
        $params = $request->getParameters();

        //判断用户申请时间是否合法
        $applyDate = $params['apply_date'];
        $beginTime = $params['apply_begin_time'];
        $endTime = $params['apply_end_time'];
        $this->chkApplyDate($applyDate, $beginTime, $endTime);

        $result = $this->officeApplyService->addApply($params);
        $result->output();
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
            JsonResult::fail('该申请目前状态不支持取消');
        }
        $status = OfficeApplyService::APPLY_REJECT;
        if ($applyInfo['status'] == OfficeApplyService::IN_USE) {
            $status = OfficeApplyService::APPLY_CANCEL;
        }
        $update = array();
        $update['audit_user_id'] = $this->admin['id'];
        $update['audit_user_realname'] = $this->admin['realname'];
        $update['status'] = $status;
        $update['audit_opinion'] = $request->getStrParam('audit_opinion');
        $date = date('Y-m-d H:i:s');
        $update['audit_time'] = $date;
        $update['update_time'] = $date;
        $result = $this->officeApplyService->updateApply($update, $applyId);
        $result->output();
    }

    /**
     * 校验申请时间是否合法
     *
     * @param string $date
     * @param string $begin
     * @param string $end
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
}