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

    public function addVacation(string $name, string $summary, int $isValid) {
        $result = parent::addRow($name, $summary, $isValid);
        return $result;
    }

    public function updateVacation(int $id, string $name, string $summary, int $isValid) {
        $result = parent::updateRow($id, $name, $summary, $isValid);
        return $result;
    }
}