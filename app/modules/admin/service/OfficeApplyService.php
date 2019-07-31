<?php

/*
 * 办公室申请服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:40:45 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:41:18
 */

namespace app\admin\service;

use app\admin\dao\OfficeApplyDao;

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

    protected $modelClassName = OfficeApplyDao::class;
    
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
        $query->where('apply_date', 'between', $searchDate);
        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($keyword)) {
            $query->where(function($query) use($query, $keyword) {
                $query->where('office_name', 'like', '%' . $keyword . '%')
                    ->whereOr('apply_reason', 'like', '%' . $keyword . '%')
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
            ->fields('a.id, a.office_name, a.apply_reason, a.apply_date, 
                a.apply_begin_time, a.apply_end_time, a.status, 
                a.create_time, b.username, b.realname')
            ->page($page, $pageSize)
            ->order('a.apply_date asc, a.apply_begin_time asc')
            ->find();
        foreach ($data as $k => $v) {
            $data[$k]['use_time'] = $v['apply_date'] . '  ' . $v['apply_begin_time'] . ' ~ ' . $v['apply_end_time'];
            $data[$k]['status_text'] = self::$statusArr[$v['status']];
        }
        $return['total'] = $total;
        $return['list'] = $data;
        return $return;
    }

    /**
     * 获取申请状态数组
     *
     * @return array
     */
    public static function getStatusArr() {
        return self::$statusArr;
    }

    /**
     * 新增申请
     *
     * @param integer $officeId 办公室记录id
     * @param string $officeName 办公室名称
     * @param string $date 预约日期
     * @param string $beginTime 预约开始时间
     * @param string $endTime 预约结束时间
     * @param string $reason 申请原因
     * @return array $result
     */
    public function addApply(int $officeId, string $officeName, string $date, 
        string $beginTime, string $endTime, string $reason) {
        $result = array(
            'success' => false,
            'message' => '',
        );
        $user = $this->getUser();
        $userId = $user['id'];

        $error = $this->chkApplyDate($officeId, $date, $beginTime, $endTime);
        if(!($error === true)){
            $result['message'] = $error;
            return result;
        }
        $date = date('Y-m-d H:i:s');
        $data = array(
            'user_id' => $userId,
            'office_id' => $officeId,
            'office_name' => $officeName,
            'apply_date' => $date,
            'apply_begin_time' => $beginTime,
            'apply_end_time' => $endTime,
            'apply_reason' => $reason,
            'status' => self::APPLIED,
            'create_time' => $date,
            'update_time' => $date,
        );
        $res = $this->modelDao->add($data);
        if ($res <= 0) {
            $result['message'] = '申请失败';
            return $result;
        }
        $result['success'] = true;
        $result['message'] = '申请成功';
        return $result;
    }

    /**
     * 更新申请记录
     *
     * @param integer $adminId 审批人账号记录id
     * @param string $adminName 审批人姓名
     * @param integer $status 状态
     * @param string $opinion 审批意见
     * @param integer $applyId 申请记录id
     * @return bool|int $result
     */
    public function updateApply(int $adminId, string $adminName, int $status, string $opinion, int $applyId) {
        $date = date('Y-m-d H:i:s');
        $data = array(
            'audit_user_id' => $adminId,
            'audit_user_realname' => $adminName,
            'status' => $status,
            'audit_opinion' => $opinion,
            'audit_time' => $date,
            'update_time' => $date 
        );
        $result = $this->modelDao->update($data, $applyId);
        return $result;
    }

    /**
     * 获取办公室的预约情况
     *
     * @param integer $officeId 办公室记录id
     * @param string $beginDate 开始时间
     * @param string $endDate 结束时间
     * @return array $list
     */
    public function getOfficeBookEdById(int $officeId, string $beginDate, string $endDate) {
        $query = $this->modelDao;
        $list = $query
            ->alias('a')
            ->join('user b', MYSQL_JOIN_INNER)
            ->on('a.user_id = b.id')
            ->fields('a.office_name, a.apply_date, a.apply_begin_time, a.apply_end_time, a.status, b.realname, b.tel')
            ->where('status', 'in', [self::APPLIED, self::IN_USE])
            ->where('office_id', $officeId)
            ->where('apply_date', 'between', [$beginDate, $endDate])
            ->order('apply_date asc, apply_begin_time asc')
            ->find();
        if (!empty($list)) {
            $statusArr = self::getStatusArr();
            foreach ($list as $k => $v) {
                $list[$k]['status_text'] = $statusArr[$v['status']];
                $list[$k]['use_time'] = $v['apply_date'] . '  ' .$v['apply_begin_time'] . ' ~ ' . $v['apply_end_time'];
            }
        }
        return $list;
    }

    /**
     * 查询指定时间已经预约的办公室id集合
     *
     * @param string $date
     * @param string $begin
     * @param string $end
     * @return array
     */
    public function searchBookEdOffice(string $date, string $begin, string $end) {
        $query = $this->modelDao;
        $query->where('status', 'in', [self::APPLIED, self::IN_USE])->where('apply_date', $date);
        $query->where(function($query) use($query, $begin, $end) {
            $query->where('apply_begin_time', 'between', [$begin, $end])
                ->whereOr('apply_end_time', 'between', [$begin, $end])
                ->whereOr(function($query) use($query, $begin, $end) {
                    $query->where('apply_begin_time', '<', $begin)->where('apply_end_time', '>', $end);
                });
        });
        $list = $query->fields('office_id')->find();
        $idsArr = array();
        foreach ($list as $k => $v) {
            array_push($idsArr, $v['office_id']);
        }
        return array_unique($idsArr);
    }

    /**
     * 获取申请详情
     *
     * @param integer $applyId
     * @return bool|array
     */
    public function getApplyInfo(int $applyId) {
        $query = $this->modelDao;
        $applyInfo = $query->alias('a')
            ->join('user b', MYSQL_JOIN_INNER)
            ->on('a.user_id = b.id')
            ->fields('a.id, a.office_name, a.apply_reason, a.apply_date, 
                a.apply_begin_time, a.apply_end_time, a.status, 
                a.audit_user_id, a.audit_user_realname, a.audit_opinion, a.audit_time, 
                a.create_time, b.username, b.realname, b.tel, b.department')
            ->where('a.id', $applyId)
            ->findOne();
        if ($applyInfo) {
            $applyInfo['use_time'] = $applyInfo['apply_date'] . ' ' . 
                $applyInfo['apply_begin_time'] . ' ~ ' . $applyInfo['apply_end_time'];
            $applyInfo['status_text'] = self::$statusArr[$applyInfo['status']];
        }
        return $applyInfo;
    }

    /**
     * 检查预约的办公室是否被占用
     *
     * @param integer $officeId
     * @param string $date
     * @param string $beginTime
     * @param string $endTime
     * @return string|bool
     */
    private function chkApplyDate(int $officeId, string $date, string $beginTime, string $endTime) {
        $query = $this->modelDao;
        $query->where('office_id', $officeId)->where('status', 'in', [self::APPLIED, self::IN_USE])->where('apply_date', $date);
        //查询前三种情况
        $error = '办公室在该时间段内已被预约';
        $firstQuery = clone $query;
        $list = $firstQuery
                ->fields('apply_date, apply_begin_time, apply_end_time, status')
                ->where(function($firstQuery) use($firstQuery, $beginTime, $endTime) {
                    $firstQuery->where('apply_begin_time', 'between', [$beginTime, $endTime])
                        ->whereOr('apply_end_time', 'between', [$beginTime, $endTime]);
                })
                ->find();

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                if ($endTime <= $v['apply_begin_time']) {
                    //新申请结束时间在已有申请开始时间之前，符合，继续遍历
                    continue;
                }
                if ($beginTime >= $v['apply_end_time']) {
                    //新申请开始时间在已有申请结束时间之后，符合，继续遍历
                    continue;
                }
                //其他情况均重叠，返回错误信息
                return $error;
            } 
        }

        $secondQuery = clone $query;
        $count = $secondQuery->where('apply_begin_time', '<', $beginTime)->where('apply_end_time', '>', $endTime)->count();
        if ($count > 0) {
            //存在记录，时间冲突
            return $error;
        }
        return true;
    }
}