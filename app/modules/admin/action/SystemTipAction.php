<?php

/*
 * 消息通知控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-08-01 15:31:44 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-05 15:43:51
 */

 namespace app\admin\action;

use app\admin\service\SystemTipService;
use herosphp\http\HttpRequest;
use herosphp\core\Loader;
use herosphp\utils\JsonResult;

class SystemTipAction extends BaseAction {
    protected $systemTipService ;
    public function __construct() {
        parent::__construct();
        $this->systemTipService = Loader::service(SystemTipService::class);
    }

    /**
     * 消息通知列表
     *
     * @return void
     */
    public function index() {
        $this->assign('dataUrl', url('/admin/systemTip/getListData'));
        $this->setView('system_tip/index');
    }

    /**
     * 获取列表数据
     *
     * @param HttpRequest $request
     * @return void
     */
    public function getListData(HttpRequest $request) {
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $vacationAudit = $this->chkPermission('vacation_apply_audit');
        $stationeryAudit = $this->chkPermission('stationery_apply_audit');
        $data = $this->systemTipService->getListData($this->admin->getId(), $page, $pageSize, null, $vacationAudit, $stationeryAudit);

        $request = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $request->setData($data['list']);
        $request->setCount($data['total']);
        $request->setPage($data['page']);
        $request->setPagesize($data['pageSize']);
        $request->output();
    }

    /**
     * 详情
     *
     * @param HttpRequest $request
     * @return void
     */
    public function detail(HttpRequest $request) {
        $id = $request->getIntParam('id');
        $tipInfo = $this->systemTipService->getTipInfo($id);
        if (empty($tipInfo)) {
            $this->error('没有找到对应的记录');
        }
        //记录为未读状态，更改为已读
        if ($tipInfo['is_read'] == 0) {
            $this->systemTipService->updateTip($id);
        }
        //跳转到指定页面
        location($tipInfo['url']);
    }
}