<?php

/*
 * 消息通知服务类
 * @Author: WangJinBo 
 * @Date: 2019-08-01 15:36:58 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-01 16:48:59
 */

namespace app\admin\service;

use app\admin\dao\SystemTipDao;

class SystemTipService extends BaseService {

    const VACATION_APPLY = 1;
    const STATIONERY_APPLY = 2;
    const VACATION_RESULT = 3;
    const OFFICE_RESULT = 4;
    const STATIONERY_RESULT = 5;

    const READ = 1;
    const UNREAD = 0;

    protected $modelClassName = SystemTipDao::class;

    protected static $content = array(
        self::VACATION_APPLY => array(
            'content' => '有新的假期调休申请',
            'url' => '/admin/vacationApply/audit'
        ),
        self::STATIONERY_APPLY => array(
            'content' => '有新的文具申请',
            'url' => '/admin/stationeryApply/audit'
        ),
        self::VACATION_RESULT => array(
            'content' => '有新的假期调休申请结果',
            'url' => '/admin/vacationApply/detail'
        ),
        self::OFFICE_RESULT => array(
            'content' => '有新的办公室申请结果',
            'url' => '/admin/officeApply/detail'
        ),
        self::STATIONERY_RESULT => array(
            'content' => '有新的文具申请结果',
            'url' => '/admin/stationeryApply/detail'
        )
    );

    protected static $statusArr = array(
        self::READ => '已读',
        self::UNREAD => '未读',
    );

    public function getContentArr() {
        return self::$content;
    }

    /**
     * 获取列表数据
     *
     * @param integer $userId 用户id
     * @param integer $page 分页
     * @param integer $pageSize 分页大小
     * @param integer $isRead 是否已读
     * @param boolean $vacationAudit 假期审批权限标识
     * @param boolean $stationeryAudit 文具审批权限标识
     * @return array $return
     */
    public function getListData(int $userId, int $page, int $pageSize, 
        int $isRead = null, bool $vacationAudit, bool $stationeryAudit){
        $query = $this->modelDao;
        
        //防止关键词为空时，SQL语句错误
        $query->where('id', '>', 0);

        $typeArr = array();
        if ($vacationAudit) {
            array_push($typeArr, self::VACATION_APPLY);
        }

        if ($stationeryAudit) {
            array_push($typeArr, self::STATIONERY_APPLY);
        }
        
        //获取自己的通知，当拥有审批权限时，获取新申请通知
        $query->where(function($query) use($query, $userId, $typeArr) {
            $query->where('user_id', $userId);
            if (!empty($typeArr)) {
                $query->whereOr('type', 'in', $typeArr);
            }
        });

        if (null !== $isRead) {
            $query->where('is_read', $isRead);
        }

        $return = array(
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => 0,
            'list' => array()
        );

        //克隆查询对象，防止查询条件丢失
        $countQuery = clone $query;
        $total = $countQuery->count();
        if ($total <= 0) {
            return $return;
        }

        $data = $query->page($page, $pageSize)->order('id desc')->find();
        $data = $this->dealData($data);
        $return['total'] = $total;
        $return['list'] = $data;
        return $return;
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

    /**
     * 获取提示详情
     *
     * @param integer $id
     * @return array
     */
    public function getTipInfo(int $id) {
        $tipInfo = $this->modelDao->findById($id);
        if (empty($tipInfo)) {
            return false;
        }
        $type = $tipInfo['type'];
        $logId = $tipInfo['log_id'];
        $logInfo = array();
        switch ($type) {
            case self::VACATION_APPLY:
            case self::VACATION_RESULT:
                $vacationModel = new VacationApplyService();
                $logInfo = $vacationModel->getApplyInfo($logId);
                break;
            case self::OFFICE_RESULT:
                $officeModel = new OfficeApplyService();
                $logInfo = $officeModel->getApplyInfo($logId);
                break;
            case self::STATIONERY_APPLY:
            case self::STATIONERY_RESULT:
                $stationeryModel = new StationeryApplyService();
                $logInfo = $stationeryModel->getApplyInfo($logId);
                break;
            default:
                break;
        }
        $tipInfo['log_info'] = $logInfo;
        $tipInfo['url'] = self::$content[$type]['url'] . '?id=' . $logId;
        if (in_array($type, [self::VACATION_APPLY, self::STATIONERY_APPLY])) {
            //申请，判断申请是否待审批
            if ($logInfo['status'] != 1) {
                //已经审批过，链接替换成详情
                $tipInfo['url'] = str_replace('audit', 'detail', $tipInfo['url']);
            }
        }
        return $tipInfo;
    }

    /**
     * 处理列表数据
     *
     * @param array $data
     * @return array
     */
    private function dealData(array $data) {
        $content = self::$content;
        foreach ($data as $k => $v) {
            $data[$k]['title'] = $content[$v['type']]['content'];
            $data[$k]['is_read_text'] = self::$statusArr[$v['is_read']]; 
        }
        return $data;
    }
}