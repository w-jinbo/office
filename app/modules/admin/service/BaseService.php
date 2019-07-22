<?php

namespace app\admin\service;

use herosphp\model\CommonService;
use herosphp\utils\JsonResult;

class BaseService extends CommonService {

    /**
     * 新增一条数据
     *
     * @param array $params
     * @return JsonResult
     */
    public function addRow(array $params) {
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
     * 修改一条数据的信息
     *
     * @param array $params
     * @return JsonResult
     */
    public function updateRow(array $params) {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            $result->setMessage($data);
            return $result;
        }

        $vacationId = $data['id'];
        unset($data['id']);
        $data['is_valid'] = isset($params['is_valid']) ? 1 : 0;
        $data['update_time'] = date('Y-m-d H:i:s');
        $res = $this->modelDao->update($data,$vacationId);
        if ($res <= 0) {
            $result->setMessage('修改失败，请稍后重试');
            return $result;
        }
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('修改成功');
        return $result;
    }

    /**
     * 删除多条数据
     *
     * @param string $ids
     * @return JsonResult
     */
    public function delRows(string $ids) {
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