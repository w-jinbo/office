<?php

namespace app\admin\action;

use herosphp\core\Loader;
use app\admin\service\OfficeService;
use app\admin\service\OfficeApplyService;
use herosphp\http\HttpRequest;

class OfficeApplyAction extends BaseAction {

    protected $officeService ;
    protected $officeApplyService ;

    public function __construct() {
        parent::__construct();
        $this->officeService = Loader::service(OfficeService::class);
        $this->officeApplyService = Loader::service(OfficeApplyService::class);
    }

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

    public function add() {
        $officeList = $this->officeService->officeList();
        $this->assign('officeList', $officeList);
        $this->setView('office_apply/add');
    }
}