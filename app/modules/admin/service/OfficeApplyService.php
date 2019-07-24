<?php

namespace app\admin\service;

class OfficeApplyService extends BaseService {

    const APPLIED = 1;
    const IN_USE = 2;
    const APPLY_OVERDUE = 3;
    const APPLY_REJECT = 4;
    const APPLY_CANCEL = 5;

    /**
     * 申请状态数组
     *
     * @var array
     */
    protected static $statusArr = array(
        self::APPLIED => '已申请',
        self::IN_USE => '使用中',
        self::APPLY_OVERDUE => '申请过期',
        self::APPLY_REJECT => '拒绝申请',
        self::APPLY_CANCEL => '关闭申请',
    );

    protected $modelClassName = 'app\admin\dao\OfficeApplyDao';
    

    public function getListData(HttpRequest $request) {
        
    }

    /**
     * 获取申请状态数组
     *
     * @return array
     */
    public static function getStatusArr() {
        return self::$statusArr;
    }
}