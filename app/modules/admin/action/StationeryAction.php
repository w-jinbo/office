<?php

/*
 * 文具管理控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 16:44:18 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-06 10:35:41
 */

namespace app\admin\action;

use herosphp\core\Loader;
use app\admin\service\StationeryService;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;
use app\admin\dao\StationeryDao;

class StationeryAction extends BaseAction {
    protected $stationeryService ;
    public function __construct() {
        parent::__construct();
        $this->stationeryService = Loader::service(StationeryService::class);
    }

    /**
     * 文具列表页
     *
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);
        $this->assign('dataUrl', url('/admin/stationery/findAll?keyword=' . urlencode($keyword)));
        $this->setView('stationery/index');
    }

    /**
     * 获取文具列表数据
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function findAll(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $data = $this->stationeryService->getListData($keyword, $page, $pageSize);

        $request = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $request->setData($data['list']);
        $request->setCount($data['total']);
        $request->setPage($data['page']);
        $request->setPagesize($data['pageSize']);
        $request->output();
    }

    /**
     * 新增文具页面
     */
    public function add() {
        $this->setView('stationery/add');
    }

    /**
     * 修改文具信息页面
     *
     * @param HttpRequest $request
     */
    public function edit(HttpRequest $request) {
        $stationeryId = $request->getStrParam('id');
        $stationery = $this->stationeryService->findById($stationeryId);
        if (empty($stationery)) {
            $this->error('没有找到对应的记录');
        }
        $this->assign('stationery', $stationery);
        $this->setView('stationery/edit');
    }

    /**
     * 新增文具操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doAdd(HttpRequest $request) {
        $data = $this->getParams($request);

        $result = $this->stationeryService->addStationery($data['name'], 
            $data['unit'], $data['summary'], $data['is_valid']);
        if ($result <= 0) {
            JsonResult::fail('添加失败');
        }
        JsonResult::success('添加成功');
    }

    /**
     * 修改文具信息操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doEdit(HttpRequest $request) {
        $stationeryId = $request->getIntParam('id');
        $data = $this->getParams($request);
        $result = $this->stationeryService->updateStationery($stationeryId, 
            $data['name'], $data['unit'], $data['summary'], $data['is_valid']);
            if ($result <= 0) {
                JsonResult::fail('修改失败');
            }
            JsonResult::success('修改成功');
    }

    /**
     * 删除操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doDel(HttpRequest $request) {
        $ids = $request->getStrParam('ids');
        parent::doDel($this->officeService, $ids);
    }

    /**
     * 修改记录状态
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doChangeValid(HttpRequest $request) {
        $params = $request->getParameters();
        $res = parent::changeValid($params['id'], $params['valid'], $this->stationeryService);
        if ($res <= 0) {
            JsonResult::fail('修改状态失败，请稍后重试');
        } else {
            JsonResult::success('修改状态成功');
        }
    }

    /**
     * 获取表单提交的参数并校验
     *
     * @param HttpRequest $request
     * @return array $params
     */
    private function getParams(HttpRequest $request) {
        $name = $request->getParameter('name', 'trim|urldecode');
        $unit = $request->getStrParam('unit');
        $summary = $request->getParameter('summary', 'trim|urldecode');
        $isValid = $request->getIntParam('is_valid');
        
        $params = array(
            'name' => $name,
            'unit' => $unit,
            'is_valid' => $isValid,
        );
        //用户有填写描述，将内容赋值到待校验数组中
        if (!empty($summary)) {
            $params['summary'] = $summary;
        }

        //过滤数据
        $data = $this->dataFilter(StationeryDao::$filter, $params);

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
