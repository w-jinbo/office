<?php

/*
 * 文具申请子项目服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-26 14:50:25 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-26 17:44:31
 */

namespace app\admin\service;

use herosphp\filter\Filter;

class StationeryApplyItemService extends BaseService {

    protected $modelClassName = 'app\admin\dao\StationeryApplyItemDao';

    /**
     * 增加记录
     *
     * @param array $params
     * @return bool
     */
    public function addRow(array $params) {
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            return false;
        }

        $date = date('Y-m-d H:i:s');
        $data['create_time'] = $date;
        $data['update_time'] = $date;
        $res = $this->modelDao->add($data);
        if ($res <= 0) {
            return false;
        }
        return true;
    }

    /**
     * 更新记录
     *
     * @param array $params
     * @param int $id
     * @return bool
     */
    public function updateRow(array $params, int $id) {
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            return false;
        }

        $data['update_time'] = date('Y-m-d H:i:s');
        $res = $this->modelDao->update($data, $id);
        if ($res <= 0) {
            return false;
        }
        return true;
    }

    /**
     * 数据过滤
     *
     * @param array $params
     * @return array|string
     */
    private function dataFilter(array $params) {
        $filterMap = array(
            'stationery_apply_id' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '申请记录id不能为空')),
            'stationery_id' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '申请文具id不能为空')),
            'stationery_name' => array(Filter::DFILTER_STRING, array(1, 50), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '申请文具名称不能为空',  'length' => '申请文具名称长度必须在1~50之内')),
            'stationery_unit' => array(Filter::DFILTER_STRING, array(1, 10), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '申请文具单位不能为空',  'length' => '申请文具单位长度必须在1~10之内')),
            'apply_num' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '申请数量不能为空')),
            'grant_num' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '发放数量不能为空'))
        );
        $data = $params;
        $data = Filter::loadFromModel($data, $filterMap, $error);
        return !$data ? $error : $data;
    }
}