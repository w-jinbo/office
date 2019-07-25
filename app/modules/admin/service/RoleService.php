<?php

/*
 * 角色管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:41:59 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:42:26
 */

namespace app\admin\service;


use herosphp\filter\Filter;
use herosphp\model\CommonService;
use herosphp\utils\JsonResult;
use herosphp\http\HttpRequest;

class RoleService extends CommonService {
    protected $modelClassName = 'app\admin\dao\RoleDao';

    /**
     * 获取角色选项列表
     *
     * @return array $list
     */
    public function roleList() {
        $list = $this->modelDao->fields('id, name')->where('is_valid', '1')->find();
        return $list;
    }

    /**
     * 获取角色列表数据
     *
     * @param HttpRequest $request
     * @return array $return
     */
    public function getListData(HttpRequest $request) {
        $query = $this->modelDao;
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        if (!empty($keyword)) {
            $query->whereOr('name', 'like', '%' . $keyword . '%')
                ->whereOr('summary', 'like', '%' . $keyword . '%');
        }

        $return = array(
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => 0,
            'list' => array()
        );

        //克隆查询对象，防止查询条件丢失
        $countQuery = clone $query;
        $total = $countQuery->count();
        if ($total <= 0) {
            return $return;
        }

        $data = $query->page($page, $pageSize)->order('id desc')->find();
        $return['total'] = $total;
        $return['list'] = $data;
        return $return;
    }

    /**
     * 增加角色
     *
     * @param array $params 表单数据
     * @return JsonResult $result
     */
    public function addRole(array $params) {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            $result->setMessage($data);
            return $result;
        }

        $date = date('Y-m-d H:i:s');
        //是否有效
        $data['is_valid'] = isset($params['is_valid']) ? 1 : 0;
        $data['create_time'] = $date;
        $data['update_time'] = $date;
        $res = $this->modelDao->add($data);
        if ($res <= 0) {
            $result->setMessage('添加失败，请稍后重试');
            return $result;
        }
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('添加成功');
        return $result;
    }

    /**
     * 更新角色记录信息
     *
     * @param array $params 表单数据
     * @return JsonResult $result
     */
    public function updateRole(array $params) {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            $result->setMessage($data);
            return $result;
        }

        $roleId = $data['id'];
        unset($data['id']);
        $data['is_valid'] = isset($params['is_valid']) ? 1 : 0;
        $data['update_time'] = date('Y-m-d H:i:s');
        $res = $this->modelDao->update($data,$roleId);
        if ($res <= 0) {
            $result->setMessage('修改失败，请稍后重试');
            return $result;
        }
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('修改成功');
        return $result;
    }

    /**
     * 删除角色
     *
     * @param string $ids 角色id字符串集合
     * @return JsonResult $result
     */
    public function delRoles(string $ids) {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $idsArr = explode(',', $ids);
        $res = $this->modelDao->where('id', 'in', $idsArr)->deletes();
        if ($res <= 0) {
            $result->setMessage('删除失败，请稍后重试');
            return $result;
        }
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('删除成功');
        return $result;
    }

    /**
     * 数据过滤
     *
     * @param array $params
     * @return array|string
     */
    private function dataFilter(array $params) {
        $filterMap = array(
            'name' => array(Filter::DFILTER_STRING, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '角色名不能为空', 'length' => '角色名称长度必须在1~20之间')),
            'permissions' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES,
                array('require' => '权限集合不能为空')),
            'summary' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, null),
        );
        $data = $params;
        unset($data['ap_codes']);
        $data['permissions'] = implode(',', $params['ap_codes']);
        $data = Filter::loadFromModel($data, $filterMap, $error);
        return !$data ? $error : $data;
    }
}