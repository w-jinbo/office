<?php

namespace app\admin\service;

use app\admin\dao\SystemTipDao;

class SystemTipService extends BaseService {

    const VACATION_APPLY = 1;
    const STATIONERY_APPLY = 2;
    const VACATION_RESULT = 3;
    const OFFICE_RESULT = 4;
    const STATIONERY_RESULT = 5;

    protected $modelClassName = SystemTipDao::class;

    protected $content = array(
        VACATION_APPLY => array(
            'content' => '有新的假期调休申请',
            'url' => '/admin/vacationApply/audit'
        ),
        STATIONERY_APPLY => array(
            'content' => '有新的文具申请',
            'url' => '/admin/stationeryApply/audit'
        ),
        VACATION_RESULT => array(
            'content' => '有新的假期调休申请结果',
            'url' => '/admin/vacationApply/detail'
        ),
        OFFICE_RESULT => array(
            'content' => '有新的办公室申请结果',
            'url' => '/admin/officeApply/detail'
        ),
        STATIONERY_RESULT => array(
            'content' => '有新的文具申请结果',
            'url' => '/admin/stationeryApply/detail'
        )
    );

    public function getContentArr() {
        return self::$content;
    }


    /**
     * 新增系统提示
     *
     * @param integer $type 提示类型
     * @param integer $logId 提示的记录id
     * @param integer $userId 提示用户id
     * @return void
     */
    public function addTip(int $type, int $logId, int $userId = 0) {
        $data = array(
            'type' => $type,
            'log_id' => $logId,
            'user_id' => $userId,
            'is_read' => 0,
            'create_time' => date('Y-m-d H:i:s')
        );
        $this->modelDao->add($data);
    }

    /**
     * 已读系统提示
     *
     * @param integer $tipId 系统提示记录id
     * @return void
     */
    public function updateTip(int $tipId) {
        $data = array(
            'is_read' => 1,
            'update_time' => date('Y-m-d H:i:s')
        );
        $this->modelDao->update($data, $tipId);
    }
}