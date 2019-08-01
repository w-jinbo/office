<?php

/*
 * 主页面控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:33:35 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-01 15:49:39
 */

namespace app\admin\action;

use herosphp\http\HttpRequest;
use app\admin\dao\UserDao;
use herosphp\utils\JsonResult;
use app\admin\service\NoticeService;
use app\admin\service\SystemTipService;

class MainAction extends BaseAction {
    public function __construct() {
        parent::__construct();
        $this->assign('admin', $this->admin);
        $GLOBALS['admin'] = $this->admin;
    }

    /**
     * 主页
     */
    public function index() {
        $this->setView('main/index');
    }

    /**
     * 首页
     *
     * @return void
     */
    public function main() {
        //获取系统公告
        $noticeModel = new NoticeService();
        $noticeList = $noticeModel->getListData('', 1, 5, 1);
        $this->assign('noticeList', $noticeList);

        //获取系统通知
        $vacationAudit = $this->chkPermission('vacation_apply_audit');
        $stationeryAudit = $this->chkPermission('stationery_apply_audit');
        $systemTipModel = new SystemTipService();
        $systemTipList = $systemTipModel->getListData($this->admin['id'], 1, 5, 0, $vacationAudit, $stationeryAudit);
        $this->assign('systemTipList', $systemTipList);
        $this->setView('main/main');
    }

    /**
     *　左侧菜单栏
     */
    public function left() {
        $this->setView('main/left');
    }

    /**
     *　修改用户信息页面
     */
    public function setUserInfo() {
        $this->setView('main/set_user_info');
    }

    /**
     *　修改用户密码页面
     */
    public function setPwd() {
        $this->setView('main/set_pwd');
    }

    /**
     *　退出登录
     */
    public function quit() {
        $this->userService->quit();
        location('/admin/login/index');
        die();
    }

    /**
     * 修改用户信息操作
     *
     * @param HttpRequest $request
     * @return void
     */
    public function doSetUserInfo(HttpRequest $request) {
        $realName= $request->getStrParam('realname');
        $tel = $request->getStrParam('tel');
        $department = $request->getStrParam('department');

        $params = array(
            'realname' => $realName,
            'tel' => $tel,
            'department' => $department
        );
        $data = $this->dataFilter(UserDao::$filter, $params);
        
        $result = $this->userService->updateUser($data['realname'], $data['tel'], $data['department']);
        if ($result <= 0) {
            JsonResult::fail('修改失败');
        }
        JsonResult::success('修改成功');
    }

    /**
     * 修改用户密码操作
     *
     * @param HttpRequest $request
     * @return void
     */
    public function doSetPwd(HttpRequest $request) {
        $newPwd = $request->getStrParam('new_pwd');
        $oldPwd = $request->getStrParam('old_pwd');
        $user = $this->userService->getUser();
        if (!$user) {
            JsonResult::fail('没有找到用户信息');
        }

        //验证旧密码
        $chkOldPwd = md5(md5($oldPwd) . $user['salt']);
        if ($chkOldPwd != $user['password']) {
            JsonResult::fail('旧密码错误，验证失败');
        }

        $result = $this->userService->setPwd($newPwd, $user['id']);
        if ($result <= 0) {
            JsonResult::fail('修改失败');
        }
        $this->userService->quit();
        $jsonResult = new JsonResult(JsonResult::CODE_SUCCESS, '修改成功，请重新登录');
        $jsonResult->setData(['url'=>'/admin/login/index']);
        $jsonResult->output();
    }
}