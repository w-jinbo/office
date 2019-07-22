<?php

namespace app\admin\action;

use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use app\admin\service\VacationApplyService;
use app\admin\service\VacationService;

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
        $type = $request->getIntParam('type');
        $status = $request->getIntParam('status');
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('type', $type);
        $this->assign('dataUrl', url('/admin/vacation/getListData?type=' . $type . '&keyword=' . urlencode($keyword) . '&status=' . $status));
        $this->setView('vacation_apply/index');
    }

    public function add() {
        $vacationList = $this->vacationService->vacationList();
        $this->assign('vacationList', $vacationList);
        $this->setView('vacation_apply/add');
    }

    public function view() {}

    public function doAdd(HttpRequest $request) {
        print_r($request->getParameters());
    }
}