<?php

/*
 * 文具申请服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-26 08:44:25 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-26 17:43:01
 */

namespace app\admin\service;

use herosphp\core\Loader;
use app\admin\dao\StationeryApplyDao;

class StationeryApplyService extends BaseService {

    const APPLIED = 1;
    const UNCLAIMED = 2;
    const RECEIVED = 3;
    const APPLY_REJECT = 4;

    /**
     * 申请状态数组
     *
     * @var array
     */
    protected static $statusArr = array(
        self::APPLIED => '已申请',
        self::UNCLAIMED => '待领取',
        self::RECEIVED => '已领取',
        self::APPLY_REJECT => '拒绝申请',
    );

    protected $modelClassName = StationeryApplyDao::class;

    /**
     * 获取申请状态数组
     *
     * @return array
     */
    public static function getStatusArr() {
        return self::$statusArr;
    }

    /**
     * 获取列表数据
     *
     * @param string $keyword 关键字
     * @param int $status 状态
     * @param array $searchDate 查询时间范围
     * @param int $type 类型，1：我的申请，2：审批列表
     * @param int $page 分页
     * @param int $pageSize 分页大小
     * @return array $data
     */
    public function getListData(string $keyword = '', int $status = 0, 
        array $searchDate, int $type, int $page, int $pageSize) {
        $query = $this->modelDao;
        $query->alias('a')
            ->join('user b', MYSQL_JOIN_INNER)
            ->on('a.user_id = b.id');
            
        $user = $this->getUser();
        $userId = $user['id'];
        if ($type == 1) {
            //我的申请列表，只加载用户自己的申请记录
            $query->where('user_id', $userId);
        } else {
            //审核列表，自己不能审核自己的申请
            $query->where('user_id', '!=', $userId);
        }
        $searchDate[1] = $searchDate[1] . ' 23:59:59';
        $query->where('a.create_time', 'between', $searchDate);
        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($keyword)) {
            $query->where(function($query) use($query, $keyword) {
                $query->where('apply_reason', 'like', '%' . $keyword . '%')
                    ->whereOr('b.realname', 'like', '%' . $keyword . '%');
            });
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

        $data = $query
            ->fields('a.id, a.apply_reason, a.status, a.create_time, b.username, b.realname')
            ->page($page, $pageSize)
            ->order('a.create_time desc')
            ->find();
        foreach ($data as $k => $v) {
            $data[$k]['status_text'] = self::$statusArr[$v['status']];
        }
        $return['total'] = $total;
        $return['list'] = $data;
        return $return;
    }

    /**
     * 新增申请
     *
     * @param integer $userId 申请人id
     * @param string $reason 申请原因
     * @param array $stationeryArr 申请物品数组，包含物品记录id，物品名称，物品单位，申请数量
     * @return bool
     */
    public function addApply(int $userId, string $reason, array $stationeryArr) {
        $query = $this->modelDao;
        $date = date('Y-m-d H:i:s');
        $data = array(
            'user_id' => $userId,
            'apply_reason' => $reason,
            'status' => self::APPLIED,
            'create_time' => $date,
            'update_time' => $date,
        );
        $query->beginTransaction();
        $res = $query->add($data);
        if ($res <= 0) {
            $query->rollback();
            return false;
        }
        //添加申请成功，将申请物品项添加到关联表中
        $stationeryItemService = Loader::service(StationeryApplyItemService::class);
        foreach ($stationeryArr as $key => $item) {
            $temp = $item;
            $temp['stationery_apply_id'] = $res;
            $stationeryItemFlag = $stationeryItemService->addRow($res, $item['stationery_id'], 
                $item['stationery_name'], $item['stationery_unit'], $item['apply_num']);
            if (!$stationeryItemFlag) {
                return false;
            }
        }
        $query->commit();
        return true;
    }

    /**
     * 审批申请
     *
     * @param integer $applyId 申请记录id
     * @param integer $adminId 审批账号id
     * @param string $adminName 审批人姓名
     * @param integer $status 审批状态
     * @param string $opinion 审批意见
     * @return bool|int
     */
    public function auditApply(int $applyId, int $adminId, string $adminName, int $status, string $opinion) {
        $date = date('Y-m-d H:i:s');
        $data = array(
            'audit_user_id' => $adminId,
            'audit_user_realname' => $adminName,
            'status' => $status,
            'audit_opinion' => $opinion,
            'audit_time' => $date,
            'update_time' => $date,
        );
        $result = $this->modelDao->update($data, $applyId);
        return $result;
    }

    /**
     * 发放文具
     *
     * @param integer $applyId 申请记录id
     * @param array $stationeryArr 物品数组，包含发放数量，关联记录id
     * @param string $remark 发放备注
     * @return bool
     */
    public function grant(int $applyId, array $stationeryArr, string $remark = null) {
        $query = $this->modelDao;
        $date = date('Y-m-d H:i:s');
        $data = array(
            'grant_remark' => empty($remark) ? '' : $remark,
            'status' => self::RECEIVED,
            'grant_time' => $date,
            'update_time' => $date,
        );
        $query->beginTransaction();
        $res = $query->update($data, $applyId);
        if(!$res){
            //数据更新失败
            $query->rollback();
            return false;
        }

        //更新发放数量
        $stationeryItemService = Loader::service(StationeryApplyItemService::class);
        foreach ($stationeryArr as $key => $item) {
            $stationeryItemFlag = $stationeryItemService->updateRow($item['grant_num'], $item['apply_item_id']);
            if (!$stationeryItemFlag) {
                $query->rollback();
                return false;
            }
        }
        $query->commit();
        //数据更新成功
        return true;
        
    }

    /**
     * 获取申请详情
     *
     * @param integer $applyId
     * @return array
     */
    public function getApplyInfo(int $applyId) {
        $query = $this->modelDao;
        $applyInfo = $query->alias('a')
            ->join('user b', MYSQL_JOIN_INNER)
            ->on('a.user_id = b.id')
            ->fields('a.id, a.apply_reason, a.status, a.audit_user_id, 
                a.audit_user_realname, a.audit_opinion, a.audit_time, a.grant_time, 
                a.create_time, b.username, b.realname, b.tel, b.department')
            ->where('a.id', $applyId)
            ->findOne();
        if ($applyInfo) {
            $applyInfo['status_text'] = self::$statusArr[$applyInfo['status']];
            //获取申请的文具项信息
            $itemQuery = Loader::service(StationeryApplyItemService::class);
            $applyInfo['item'] = $itemQuery
                ->fields('id, stationery_name, stationery_unit, apply_num, grant_num')
                ->where('stationery_apply_id', $applyId)
                ->find();
        }
        return $applyInfo;
    }
}
