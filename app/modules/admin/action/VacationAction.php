<?php

/*
 * 假期管理控制器
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:38:41 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-01 16:41:48
 */

namespace app\admin\action;

use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;
use app\admin\service\VacationService;
use app\admin\dao\VacationDao;

class VacationAction extends BaseAction {
    protected $vacationService ;
    public function __construct() {
        parent::__construct();
        $this->vacationService = Loader::service(VacationService::class);
    }

    /**
     * 假期类型列表页
     *
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {
        $this->chkPermissionWeb('vacation_list');
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $this->assign('keyword', $keyword);
        $this->assign('dataUrl', url('/admin/vacation/getListData?keyword=' . urlencode($keyword)));
        $this->setView('vacation/index');
    }

    /**
     * 获取列表页数据接口
     *
     * @param HttpRequest $request
     */
    public function getListData(HttpRequest $request) {
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $data = $this->vacationService->getListData($keyword, $page, $pageSize);

        $request = new JsonResult(JsonResult::CODE_SUCCESS, '获取数据成功');
        $request->setData($data['list']);
        $request->setCount($data['total']);
        $request->setPage($data['page']);
        $request->setPagesize($data['pageSize']);
        $request->output();
    }

    /**
     * 新增假期类型页面
     */
    public function add() {
        $this->chkPermissionWeb('vacation_list_add');
        $this->setView('vacation/add');
    }

    /**
     * 修改假期类型信息页面
     *
     * @param HttpRequest $request
     */
    public function edit(HttpRequest $request) {
        $this->chkPermissionWeb('vacation_list_edit');
        $vacationId = $request->getStrParam('id');
        $vacation = $this->vacationService->findById($vacationId);
        if (empty($vacation)) {
            $this->error('没有找到对应的记录');
        } 
        $this->assign('vacation', $vacation);
        $this->setView('vacation/edit');
    }

    /**
     * 新增假期操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doAdd(HttpRequest $request) {
        if (!$this->chkPermission('vacation_list_add')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $data = self::getParams($request);
        
        $result = $this->vacationService->addVacation($data['name'], $data['summary'], $data['is_valid']);
        if ($result <= 0) {
            JsonResult::fail('添加失败');
        }
        JsonResult::success('添加成功');
    }

    /**
     * 修改假期信息操作
     *
     * @param HttpRequest $request
     * @return Json
     */
    public function doEdit(HttpRequest $request) {
        if (!$this->chkPermission('vacation_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $vacationId = $request->getIntParam('id');
        $data = self::getParams($request);
    
        $result = $this->vacationService->updateVacation($vacationId, $data['name'], $data['summary'], $data['is_valid']);
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
        if (!$this->chkPermission('vacation_list_del')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $ids = $request->getStrParam('ids');
        parent::doDel($this->vacationService, $ids);
    }

    /**
     * 修改记录状态
     *
     * @param HttpRequest $request
     * @return JsonResult
     */
    public function doChangeValid(HttpRequest $request) {
        if (!$this->chkPermission('vacation_list_edit')) {
            JsonResult::fail('您没有权限进行此操作');
        }
        $params = $request->getParameters();
        $res = parent::changeValid($params['id'], $params['valid'], $this->vacationService);
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
        $summary = $request->getParameter('summary', 'trim|urldecode');
        $isValid = $request->getIntParam('is_valid');
        
        $params = array(
            'name' => $name,
            'is_valid' => $isValid,
        );
        //用户有填写描述，将内容赋值到待校验数组中
        if (!empty($summary)) {
            $params['summary'] = $summary;
        }

        //过滤数据
        $data = $this->dataFilter(VacationDao::$filter, $params);

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