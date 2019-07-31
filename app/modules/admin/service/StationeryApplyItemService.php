<?php

/*
 * 文具申请子项目服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-26 14:50:25 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-26 17:44:31
 */

namespace app\admin\service;


class StationeryApplyItemService extends BaseService {

    protected $modelClassName = 'app\admin\dao\StationeryApplyItemDao';

    /**
     * 新增物品申请关联记录
     *
     * @param integer $applyId 物品申请记录id
     * @param integer $stationeryId 物品id
     * @param string $stationeryName 物品名称
     * @param string $stationeryUnit 物品单位
     * @param integer $applyNum 申请数量
     * @return bool
     */
    public function addRow(int $applyId, int $stationeryId, 
        string $stationeryName, string $stationeryUnit, int $applyNum) {
        $date = date('Y-m-d H:i:s');
        $data = array(
            'stationery_apply_id' => $applyId,
            'stationery_id' => $stationeryId,
            'stationery_name' => $stationeryName,
            'stationery_unit' => $stationeryUnit,
            'apply_num' => $applyNum,
            'create_time' => $date,
            'update_time' => $date,
        );
        $res = $this->modelDao->add($data);
        if ($res <= 0) {
            return false;
        }
        return true;
    }

    /**
     * 更新物品关联记录
     *
     * @param integer $grantNum 发放数量
     * @param integer $id 关联记录id
     * @return void
     */
    public function updateRow(int $grantNum, int $id) {
        $data = array(
            'grant_num' => $grantNum,
            'update_time' => date('Y-m-d H:i:s'),
        );
        $res = $this->modelDao->update($data, $id);
        if ($res <= 0) {
            return false;
        }
        return true;
    }
}