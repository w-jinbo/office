<?php

/*
 * 角色管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:41:59 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-29 17:47:05
 */

namespace app\admin\service;


use herosphp\filter\Filter;
use herosphp\utils\JsonResult;
use herosphp\http\HttpRequest;
use app\demo\dao\RoleDao;

class RoleService extends BaseService {
    protected $modelClassName = RoleDao::class;

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
     * 新增角色
     *
     * @param string $name 角色名
     * @param integer $isValid 是否有效
     * @param string $summary 描述
     * @param string $permissions 权限集合
     * @return int
     */
    public function addRole(string $name, int $isValid, string $summary, string $permissions) {
        $date = date('Y-m-d H:i:s');
        $data = array(
            'name' => $name,
            'is_valid' => $isValid,
            'summary' => $summary,
            'permissions' => $permissions,
            'create_time' => $date,
            'update_time' => $date
        );
        $result = $this->modelDao->add($data);
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
}