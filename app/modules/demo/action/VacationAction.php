<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-29
 * Time: 上午9:36
 */

namespace app\demo\action;

use app\demo\service\VacationService;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;

class VacationAction extends ControllerRepeat
{
    private $vacationService;

    private $menu;

    public function __construct()
    {
        parent::__construct();
        $this->isLogin();
        $this->vacationService = Loader::service(VacationService::class);
        $this->menu  = $this->roleString($_SESSION['roleAuth']);
    }

    /**
     * 假期申请入口
    */
    public function apply(HttpRequest $request)
    {
        $uri = $request->getRequestUri();
        $this->isAuth($_SESSION['roleAuth'],$uri);
        $data['menu'] = $this->menu;
        $this->assign('data',$data);
        $this->setView('vacation/vacation');
    }


    /**
     * 假期申请操作
    */
    public function putTo(HttpRequest $request)
    {
        $data['menu'] = $this->menu;
        $gets = $request->getParameters();
        $data['tip']  = $this->vacationService->filterVaction($gets);
        $this->assign('data',$data);
        $this->setView('vacation/vacation');

    }

    /**
     * 用户查看个人已申请的假期
    */
    public function vacations(HttpRequest $request)
    {
        $pageSize = 10;
        $page = $request->getParameter('page');
        $count = $this->vacationService->allCount();
        $data['menu'] = $this->menu;
        $data['user'] = $this->vacationService->selectOne($page,$pageSize);
        $data['page'] = $this->nowPage($count,$pageSize,$page);
        $this->assign('data',$data);
        $this->setView('vacation/vacations');

    }

    /**
     * 用户取消假期申请
     */
    public function remove(HttpRequest $request)
    {
        //判断权限
        $data['menu'] = $this->menu;
        $id = $request->getParameter('id');
        $data['tip'] = $this->vacationService->vacationRemove($id);
        $this->assign('data',$data);
        $this->setView('vacation/vacations');
    }


    /**
     * 假期申请审核入口
    */
    public function trial(HttpRequest $request)
    {
        $uri = $request->getRequestUri();
        $this->isAuth($_SESSION['roleAuth'],$uri);
        $pageSize = 10;
        $page = $request->getParameter('page');
        $count = $this->vacationService->allCount();
        $data['menu'] = $this->menu;
        $data['page'] = $this->nowPage($count,$pageSize,$page);
        $data['all'] = $this->vacationService->vacationAll($page,$pageSize);
        $this->assign('data',$data);
        $this->setView('vacation/vacationTrial');
    }

    public function trialOne(HttpRequest $request)
    {
        $data['menu'] = $this->menu;
        $id = $request->getParameter('id');
        $userID = $request->getParameter('user');
        $data['vacation'] = $this->vacationService->vacationOne($id);
        $data['user'] = $this->vacationService->userOne($userID);
        $this->assign('data',$data);
        $this->setView('vacation/vacationTrialOne');
    }

    /***
     * 假期审核
     * */
    public function judge(HttpRequest $request)
    {
        $gets = $request->getParameters();
        $pageSize = 10;
        $count = $this->vacationService->allCount();
        $data['menu'] = $this->menu;
        $data['page'] = $this->nowPage($count,$pageSize,$gets['page']);
        $data['tip'] =$this->vacationService->isFeedback($gets);
        $data['all'] = $this->vacationService->vacationAll($gets['page'],$pageSize);
        $this->assign('data',$data);
        $this->setView('vacation/vacationTrial');
    }


}