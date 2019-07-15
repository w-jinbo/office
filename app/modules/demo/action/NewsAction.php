<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-30
 * Time: 下午4:03
 */

namespace app\demo\action;


use app\demo\service\NewsService;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;

class NewsAction extends ControllerRepeat
{

    private $newsService;

    private $menu;

    public function __construct()
    {
        parent::__construct();
        $this->isLogin();
        $this->newsService = Loader::service(NewsService::class);
        $this->menu  = $this->roleString($_SESSION['roleAuth']);
    }

    /**
     * 内部公示入口
    */
    public function apply(HttpRequest $request)
    {
        $uri = $request->getRequestUri();

        $this->isAuth($_SESSION['roleAuth'],$uri);
        $page = $request->getParameter('page');
        $pageSize = 10;
        $count = $this->newsService->allCount();
        $data['menu'] = $this->menu;
        $data['news'] = $this->newsService->allNews($page,$pageSize);
        $data['page'] = $this->nowPage($count,$pageSize,$page);
        $this->assign('data',$data);
        $this->setView('new/news');
    }

    public function look(HttpRequest $request)
    {
        $data['menu'] = $this->menu;
        $id = $request->getParameter('id');
        $data['news'] = $this->newsService->selectOne($id);
        $this->assign('data',$data);
        $this->setView('new/newOne');
    }

    /**
     * 发布内部公示
     */
    public function writeNew(HttpRequest $request)
    {
        $uri = $request->getRequestUri();
        $this->isAuth($_SESSION['roleAuth'],$uri);
        $data['menu'] = $this->menu;
        $this->assign('data',$data);
        $this->setView('new/writeNew');
    }

    public function insert(HttpRequest $request)
    {
        $data['menu'] = $this->menu;
        $gets = $request->getParameters();
        $data['tip'] = $this->newsService->filterNew($gets);
        $this->assign('data',$data);
        $this->setView('new/writeNew');
    }




}