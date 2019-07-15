<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-29
 * Time: 下午4:06
 */

namespace app\demo\action;


use app\demo\service\RoomSevice;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;

class RoomAction extends ControllerRepeat
{
    private $roomSevice;

    private $menu;

    public function __construct()
    {
        parent::__construct();
        $this->isLogin();
        $this->roomSevice = Loader::service(RoomSevice::class);
        $this->menu  = $this->roleString($_SESSION['roleAuth']);
    }

    /**
     * 办公室申请入口
    */
    public function apply(HttpRequest $request)
    {
        $uri = $request->getRequestUri();
        $this->isAuth($_SESSION['roleAuth'],$uri);
        $data['menu'] = $this->menu;
        $this->assign('data',$data);
        $this->setView("room/room");
    }

    /**
     * 办公室申请操作
    */
    public function roomApply(HttpRequest $request)
    {
        $data['menu'] = $this->menu;
        $get = $request->getParameters();
        $data['tip']  = $this->roomSevice->first($get);
        $this->assign('data',$data);
        $this->setView('room/room');
    }


    /**
     * 用户查看个人已申请的假期
     */
    public function rooms(HttpRequest $request)
    {
        $page = $request->getParameter('page');
        $pageSize = 10;
        $count = $this->roomSevice->allCountID();
        $data['menu'] = $this->menu;
        $data['room'] = $this->roomSevice->selectRooms($page,$pageSize);
        $data['page'] = $this->nowPage($count,$pageSize,$page);
        $this->assign('data',$data);
        $this->setView('room/rooms');
    }

    /**
    * 用户取消办公室申请
     */
    public function remove(HttpRequest $request)
    {
        $data['menu'] = $this->menu;
        $id = $request->getParameter('id');
        $data['tip'] = $this->roomSevice->roomsRemove($id);
        $this->assign('data',$data);
        $this->setView('room/rooms');
    }

    /**
     * 物品审核入口,普通管理员
    */
    public function trial(HttpRequest $request)
    {
        $uri = $request->getRequestUri();
        $this->isAuth($_SESSION['roleAuth'],$uri);
        $page = $request->getParameter('page');
        $pageSize = 10;
        $count = $this->roomSevice->allCount();
        $data['menu'] = $this->menu;
        $data['all'] = $this->roomSevice->roomAll($page,$pageSize);
        $data['page'] = $this->nowPage($count,$pageSize,$page);
        $this->assign('data',$data);
        $this->setView('room/roomTrial');
    }
    public function trialOne(HttpRequest $request)
    {
        $data['menu'] = $this->menu;
        $gets = $request->getParameters();
        $data['room'] = $this->roomSevice->roomOne($gets['id']);
        $data['user'] = $this->roomSevice->userOne($gets['user']);
        $this->assign('data',$data);
        $this->setView('room/roomTrialOne');
    }

    /***
     * 物品审核操作
     * */
    public function judge(HttpRequest $request)
    {
        $uri = $request->getRequestUri();
        $this->isAuth($_SESSION['roleAuth'],$uri);
        $gets = $request->getParameters();
        $pageSize = 10;
        $count = $this->roomSevice->allCount();
        $data['menu'] = $this->menu;
        $data['all'] = $this->roomSevice->roomAll($gets['page'],$pageSize);
        $data['page'] = $this->nowPage($count,$pageSize,$gets['page']);
        $data['tip'] =$this->roomSevice->isState($gets);
        $this->assign('data',$data);
        $this->setView('room/roomTrial');
    }

}