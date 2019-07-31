<?php

 /*
  * 假期管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:48:11 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:48:34
 */

namespace app\admin\service;

use app\admin\dao\VacationDao;

class VacationService extends BaseService {

    protected $modelClassName = VacationDao::class;

    /**
     * 获取有效的假期数组
     *
     * @return array|bool
     */
    public function vacationList() {
        $list = $this->modelDao->fields('id, name')->where('is_valid', '1')->find();
        return $list;
    }

    /**
     * 添加假期类型
     *
     * @param string $name 假期名称
     * @param string $summary 描述
     * @param integer $isValid 是否有效
     * @return bool|int
     */
    public function addVacation(string $name, string $summary, int $isValid) {
        $result = parent::addRow($name, $summary, $isValid);
        return $result;
    }

    /**
     * 修改假期类型信息
     *
     * @param integer $id 记录id
     * @param string $name 假期名称
     * @param string $summary 描述
     * @param integer $isValid 是否有效
     * @return bool|int
     */
    public function updateVacation(int $id, string $name, string $summary, int $isValid) {
        $result = parent::updateRow($id, $name, $summary, $isValid);
        return $result;
    }
}