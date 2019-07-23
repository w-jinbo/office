<?php

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

    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $type = $request->getStrParam('type');
        $status = $request->getStrParam('status');
        $this->assign('statusList', $this->vacationApplyService->getStatusArr());
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('type', $type);
        $this->assign('dataUrl', url('/admin/vacationApply/getListData?type=' . $type . '&keyword=' . urlencode($keyword) . '&status=' . $status));
        $this->setView('vacation_apply/index');
    }

    public function getListData(HttpRequest $request) {
        $data = $this->vacationApplyService->getListData($request);

        $result = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $result->setData($data['list']);
        $result->setCount($data['total']);
        $result->setPage($data['page']);
        $result->setPagesize($data['pageSize']);
        $result->output();
    } 

    public function add() {
        $vacationList = $this->vacationService->vacationList();
        $this->assign('vacationList', $vacationList);
        $this->setView('vacation_apply/add');
    }

    public function view() {}

    /**
     * Undocumented function
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
}