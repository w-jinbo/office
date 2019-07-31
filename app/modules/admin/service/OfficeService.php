<?php

/*
 * 办公室管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:41:23 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:41:52
 */

namespace app\admin\service;

use app\admin\dao\OfficeDao;

class OfficeService extends BaseService {

    protected $modelClassName = OfficeDao::class;

    /**
     * 获取有效的办公室数组
     *
     * @return array|bool
     */
    public function officeList() {
        $list = $this->modelDao->fields('id, name')->where('is_valid', '1')->find();
        return $list;
    }

    /**
     * 添加办公室
     *
     * @param string $name 办公室名称
     * @param string $address 办公室位置
     * @param string $summary 描述
     * @param integer $isValid 是否有效
     * @return bool|int
     */
    public function addOffice(string $name, string $address, string $summary, int $isValid) {
        $date = date('Y-m-d H:i:s');
        $data = array(
            'name' => $name,
            'address' => $address,
            'summary' => $summary,
            'is_valid' => $isValid,
            'create_time' => $date,
            'update_time' => $date,
        );
        $result = $this->modelDao->add($data);
        return $result;
    }

    /**
     * 修改办公室信息
     *
     * @param integer $id 记录id
     * @param string $name 办公室名称
     * @param string $address 办公室位置
     * @param string $summary 描述
     * @param integer $isValid 是否有效
     * @return bool|int
     */
    public function updateOffice(int $id, string $name, string $address, string $summary, int $isValid) {
        $data = array(
            'name' => $name,
            'address' => $address,
            'summary' => $summary,
            'is_valid' => $isValid,
            'update_time' => date('Y-m-d H:i:s'),
        );
        $result = $this->modelDao->update($data, $id);
        return $result;
    }
}