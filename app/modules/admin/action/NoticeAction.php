<?php

/*
 * 系统公告控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-08-01 15:29:21 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-06 09:53:36
 */

 namespace app\admin\action;

use herosphp\core\Loader;
use app\admin\service\NoticeService;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;
use app\admin\dao\NoticeDao;

class NoticeAction extends BaseAction {

    protected $noticeService ;

    public function __construct() {
        parent::__construct();
        $this->noticeService = Loader::service(NoticeService::class);
    }

    /**
     * 管理列表页
     *
     * @param HttpRequest $request
     * @return void
     */
    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);
        $this->assign('dataUrl', url('/admin/notice/findAll?keyword=' . urlencode($keyword)));
        $this->setView('notice/index');
    }

    /**
     * 普通列表页
     *
     * @param HttpRequest $request
     * @return void
     */
    public function list(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);
        $this->assign('dataUrl', url('/admin/notice/findAll?valid=1&keyword=' . urlencode($keyword)));
        $this->setView('notice/list');
    }

    /**
     * 获取列表数据
     *
     * @param HttpRequest $request
     * @return void
     */
    public function findAll(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $isValid = $request->getParameter('valid');
        $data = $this->noticeService->getListData($keyword, $page, $pageSize, $isValid);

        $result = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $result->setData($data['list']);
        $result->setCount($data['total']);
        $result->setPage($data['page']);
        $result->setPagesize($data['pageSize']);
        $result->output();
    }

    /**
     * 新增公告页面
     *
     * @return void
     */
    public function add() {
        $this->setView('notice/add');
    }

    /**
     * 修改公告信息页面
     *
     * @param HttpRequest $request
     * @return void
     */
    public function edit(HttpRequest $request) {
        $id = $request->getIntParam('id');
        $notice = $this->noticeService->findById($id);
        if (empty($notice)) {
            $this->error('没有找到对应的记录');
        }
        $this->assign('notice', $notice);
        $this->setView('notice/edit');
    }

    /**
     * 公告详情
     *
     * @param HttpRequest $request
     * @return void
     */
    public function detail(HttpRequest $request) {
        $id = $request->getIntParam('id');
        $notice = $this->noticeService->findById($id);
        if (empty($notice)) {
            $this->error('没有找到对应的记录');
        }
        $this->assign('notice', $notice);
        $this->setView('notice/detail');
    }

    /**
     * 新增操作
     *
     * @param HttpRequest $request
     * @return void
     */
    public function doAdd(HttpRequest $request) {
        $data = $this->getParams($request);
        $result = $this->noticeService->addNotice($data['title'], 
            $data['summary'], $data['content'], $data['is_valid']);
        if ($result <= 0) {
            JsonResult::fail('添加失败');
        }
        JsonResult::success('添加成功');
    }

    /**
     * 修改公告信息操作
     *
     * @param HttpRequest $request
     */
    public function doEdit(HttpRequest $request) {
        $noticeId = $request->getIntParam('id');
        $data = $this->getParams($request);
        $result = $this->noticeService->updateNotice($noticeId, $data['title'], 
        $data['summary'], $data['content'], $data['is_valid']);
        if ($result <= 0) {
            JsonResult::fail('修改失败');
        }
        JsonResult::success('修改成功');
    }

    /**
     * 删除公告操作
     *
     * @param HttpRequest $request
     */
    public function doDel(HttpRequest $request){
        $ids = $request->getStrParam('ids');
        parent::doDel($this->noticeService, $ids);
    }

    /**
     * 修改公告状态接口
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doChangeValid(HttpRequest $request) {
        $params = $request->getParameters();
        $res = parent::changeValid($params['id'], $params['valid'], $this->noticeService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }

    /**
     * 获取表单提交的参数
     *
     * @param HttpRequest $request
     * @return array $params
     */
    private function getParams(HttpRequest $request) {
        $title = $request->getParameter('title', 'trim|urldecode');
        $summary = $request->getParameter('summary', 'trim|urldecode');
        $content = $request->getParameter('content', 'trim|urldecode');
        $isValid = $request->getIntParam('is_valid');
        
        $params = array(
            'title' => $title,
            'is_valid' => $isValid,
            'content' => $content
        );
        if (!empty($summary)) {
            $params['summary'] = $summary;
        }

        //过滤数据
        $data = $this->dataFilter(NoticeDao::$filter, $params);

        if (!is_array($data)) {
            JsonResult::fail($data);
        }

        //存储描述的键值对不存在，用户没有填写描述或清空，需要将数据内容清空
        if (!isset($data['summary'])) {
            $data['summary'] = '';
        }
        return $data;
    }
}