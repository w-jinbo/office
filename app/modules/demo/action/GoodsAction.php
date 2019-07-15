<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-30
 * Time: 下午2:43
 */

namespace app\demo\action;


use app\demo\service\GoodsService;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;

class GoodsAction extends ControllerRepeat
{
    private $goodsSevice;

    private $menu;

    public function __construct()
    {
        parent::__construct();
        $this->isLogin();
        $this->goodsSevice = Loader::service(GoodsService::class);
        $this->menu  = $this->roleString($_SESSION['roleAuth']);
    }

    /**
     * 物品领取入口
    */
    public function apply(HttpRequest $request)
    {
        $uri = $request->getRequestUri();
        $this->isAuth($_SESSION['roleAuth'],$uri);
        $data['menu'] = $this->menu;
        $this->assign('data',$data);
        $this->setView('goods/goods');
    }

    public function goodsApply(HttpRequest $request)
    {
        $data['menu'] = $this->menu;
        $get = $request->getParameter('check');
        $data['tip'] = $this->goodsSevice->firstSelect($get);
        $this->assign('data',$data);
        $this->setView('goods/goods');
    }

    /**
     * 查看物品领用
     */
    public function goodsAll(HttpRequest $request)
    {
        $page = $request->getParameter('page');
        $pageSize = 10;
        $count = $this->goodsSevice->allCountID();
        $data['menu'] = $this->menu;
        $data['goods'] = $this->goodsSevice->goodsID($page,$pageSize);
        $data['page'] = $this->nowPage($count,$pageSize,$page);
        $this->assign('data',$data);
        $this->setView('goods/goodss');
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
        $count = $this->goodsSevice->allCount();
        $data['page'] = $this->nowPage($count,$pageSize,$page);
        $data['menu'] = $this->menu;
        $data['all'] = $this->goodsSevice->goodsAll($page,$pageSize);
        $this->assign('data',$data);
        $this->setView('goods/goodsTrial');
    }

    public function trialOne(HttpRequest $request)
    {
        $data['menu'] = $this->menu;
        $id = $request->getParameter('id');
        $userID = $request->getParameter('user');
        $data['goods'] = $this->goodsSevice->goodsOne($id);
        $data['user'] = $this->goodsSevice->userOne($userID);
        $this->assign('data',$data);
        $this->setView('goods/goodsTrialOne');
    }

    /***
     * 审核
     * */
    public function judge(HttpRequest $request)
    {
        $gets = $request->getParameters();
        $pageSize = 10;
        $count = $this->goodsSevice->allCount();
        $data['page'] = $this->nowPage($count,$pageSize,$gets['page']);
        $data['menu'] = $this->menu;
        $data['all'] = $this->goodsSevice->goodsAll($gets['page'],$pageSize);
        $data['tip'] =$this->goodsSevice->isState($gets);
        $this->assign('data',$data);
        $this->setView('goods/goodsTrial');
    }

}