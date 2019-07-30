<?php

/*
 * 基础服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:40:07 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-29 17:32:26
 */

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

        $id = $data['id'];
        unset($data['id']);
        $data['is_valid'] = isset($params['is_valid']) ? 1 : 0;
        $data['update_time'] = date('Y-m-d H:i:s');
        $res = $this->modelDao->update($data,$id);
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
        $idsArr = explode(',', $ids);
        $result = $this->modelDao->where('id', 'in', $idsArr)->deletes();
        return $result;
    }
}