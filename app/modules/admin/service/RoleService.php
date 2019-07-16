<?php


namespace app\admin\service;


use herosphp\filter\Filter;
use herosphp\model\CommonService;
use herosphp\utils\JsonResult;

class RoleService extends CommonService {
    protected $modelClassName = 'app\admin\dao\RoleDao';

    public function roleList() {
        $list = $this->modelDao->fields('id,name')->where('is_valid', '1')->find();
        return $list;
    }

    public function addRole($params) {
        $result = new JsonResult(JsonResult::CODE_FAIL);
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

    public function updateRole($params) {
        $result = new JsonResult(JsonResult::CODE_FAIL);
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

    public function delRoles($ids) {
        $result = new JsonResult(JsonResult::CODE_FAIL);
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

    private function dataFilter($params) {
        $filterMap = array(
            'name' => array(Filter::DFILTER_STRING, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '角色名不能为空', 'length' => '用户名长度必须在1~20之间')),
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