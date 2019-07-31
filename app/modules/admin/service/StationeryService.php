<?php

/*
 * 文具管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 16:47:54 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-26 15:17:54
 */

namespace app\admin\service;
;
use app\admin\dao\StationeryDao;

class StationeryService extends BaseService{

    protected $modelClassName = StationeryDao::class;

    /**
     * 获取有效的文具数组
     *
     * @return array|bool
     */
    public function stationeryList() {
        $list = $this->modelDao->fields('id, name, unit')->where('is_valid', '1')->find();
        return $list;
    }

    /**
     * 添加文具
     *
     * @param string $name 文具名称
     * @param string $unit 文具单位
     * @param string $summary 描述
     * @param integer $isValid 是否有效
     * @return bool|int
     */
    public function addStationery(string $name, string $unit, string $summary, int $isValid) {
        $date = date('Y-m-d H:i:s');
        $data = array(
            'name' => $name,
            'unit' => $unit,
            'summary' => $summary,
            'is_valid' => $isValid,
            'create_time' => $date,
            'update_time' => $date,
        );
        $result = $this->modelDao->add($data);
        return $result;
    }

    /**
     * 修改文具信息
     *
     * @param integer $id 记录id
     * @param string $name 文具名称
     * @param string $unit 文具单位
     * @param string $summary 描述
     * @param integer $isValid 是否有效
     * @return bool|int
     */
    public function updateStationery(int $id, string $name, string $unit, string $summary, int $isValid) {
        $data = array(
            'name' => $name,
            'unit' => $unit,
            'summary' => $summary,
            'is_valid' => $isValid,
            'update_time' => date('Y-m-d H:i:s'),
        );
        $result = $this->modelDao->update($data, $id);
        return $result;
    }
}
