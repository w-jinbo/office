<?php

/*
 * 假期申请服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:47:22 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-05 15:39:56
 */

namespace app\admin\service;

use herosphp\utils\JsonResult;
use app\admin\dao\VacationApplyDao;
use app\admin\service\UserService;

class VacationApplyService extends BaseService {
    
    const AM = 1;
    const PM = 2;
    const APPLYING = 1;
    const APPLY_AGREE = 2;
    const APPLY_REJECT = 3;
    const APPLY_CANCEL = 4;
    
    protected $modelClassName = VacationApplyDao::class;

    /**
     * 申请状态数组
     *
     * @var array
     */
    protected static $statusArr = array(
        self::APPLYING => '申请中',
        self::APPLY_AGREE => '同意申请',
        self::APPLY_REJECT => '拒绝申请',
        self::APPLY_CANCEL => '取消申请',
    );

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
            
        $user = self::getUser();
        $userId = $user->getId();
        if ($type == 1) {
            //我的申请列表，只加载用户自己的申请记录
            $query->where('user_id', $userId);
        } else {
            //审核列表，自己不能审核自己的申请
            $query->where('user_id', '!=', $userId);
        }
        
        $query->where(function($query) use($query, $searchDate) {
            $query->where('apply_begin_date', 'between', $searchDate)
                ->whereOr('apply_end_date', 'between', $searchDate);
        });
        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($keyword)) {
            $query->where(function($query) use($query, $keyword) {
                $query->where('vacation_name', 'like', '%' . $keyword . '%')
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
            ->fields('a.id, a.vacation_name, a.apply_reason, a.apply_begin_date, 
                a.apply_begin_period, a.apply_end_date, a.apply_end_period, a.status, 
                a.create_time, b.username, b.realname')
            ->page($page, $pageSize)
            ->order('a.id desc')
            ->find();
        foreach ($data as $k => $v) {
            $beginFormat = self::formatDate($v['apply_begin_date'], $v['apply_begin_period']);
            $endFormat = self::formatDate($v['apply_end_date'], $v['apply_end_period'], 'end');
            $data[$k]['apply_date'] = $beginFormat . ' ~ ' . $endFormat;
            $data[$k]['status_text'] = self::$statusArr[$v['status']];
        }
        $return['total'] = $total;
        $return['list'] = $data;
        return $return;
    }

    /**
     * 新增申请
     *
     * @param int $vacationId 假期类型id
     * @param string $vacationName 假期类型名称
     * @param string $beginDate 请假开始日期
     * @param int $beginPeriod 请假开始时间段，1：上午，2：下午
     * @param string $endDate 请假结束日期
     * @param int $endPeriod 请假结束时间段，1：上午，2：下午
     * @param string $reason 请假原因
     * @return JsonResult
     */
    public function addApply(int $vacationId, string $vacationName, string $beginDate, int $beginPeriod, 
        string $endDate, int $endPeriod, string $reason) {
        $result = array(
            'success' => false,
            'message' => '',
        );
        $user = self::getUser();
        $userId = $user->getId();

        $error = $this->chkApplyDate($userId, $beginDate, $endDate, $beginPeriod, $endPeriod);
        if(!($error === true)){
            $result['message'] = $error;
            return $result;
        }
        $date = date('Y-m-d H:i:s');
        $data = array(
            'user_id' => $userId,
            'vacation_id' => $vacationId,
            'vacation_name' => $vacationName,
            'apply_begin_date' => $beginDate,
            'apply_begin_period' => $beginPeriod,
            'apply_end_date' => $endDate,
            'apply_end_period' => $endPeriod,
            'apply_reason' => $reason,
            'status' => self::APPLYING,
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
        $result['apply_id'] = $res;
        return $result;
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
            ->fields('a.id, a.vacation_name, a.apply_reason, a.apply_begin_date, 
                a.apply_begin_period, a.apply_end_date, a.apply_end_period, a.status, 
                a.audit_user_id, a.audit_user_realname, a.audit_opinion, a.audit_time, 
                a.create_time, b.username, b.realname, b.tel, b.department')
            ->where('a.id', $applyId)
            ->findOne();
        if ($applyInfo) {
            $beginFormat = self::formatDate($applyInfo['apply_begin_date'], $applyInfo['apply_begin_period']);
            $endFormat = self::formatDate($applyInfo['apply_end_date'], $applyInfo['apply_end_period'], 'end');
            $applyInfo['apply_date'] = $beginFormat . ' ~ ' . $endFormat;
            $applyInfo['status_text'] = self::$statusArr[$applyInfo['status']];
        }
        return $applyInfo;
    }

    /**
     * 申请审批
     *
     * @param int $applyId 申请记录id
     * @param int $status 状态
     * @param string $opinion 审批意见
     * @return bool|int $result
     */
    public function auditApply(int $applyId, int $status, string $opinion) {
        $admin = self::getUser();
        $date = date('Y-m-d H:i:s');
        $data = array(
            'status' => $status,
            'audit_user_id' => $admin->getId(),
            'audit_user_realname' => $admin->getRealname(),
            'audit_opinion' => $opinion,
            'audit_time' => $date,
            'update_time' => $date,
        );
        $result = $this->modelDao->update($data, $applyId);
        return $result;
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
     * 格式化申请日期，方便后续进行的时间比较
     *
     * @param string $date
     * @param int $period
     * @param string $type
     * @return string
     */
    public static function formatDate(string $date, int $period, $type = 'begin') {
        $format = array(
            'begin' => array(self::AM => '08:30', self::PM => '13:30'),
            'end' => array(self::AM => '12:00', self::PM => '18:00')
        );
        // $dateTemp = date('Y-m-d H:i:s',strtotime($date . ' ' . $format[$type][$period]));
        // return $dateTemp ;
        return $date . ' ' . $format[$type][$period];
    }

    /**
     * 判断用户新申请是否与已有申请的时间重叠
     *
     * @param integer $userId 申请用户id
     * @param string $beginDate 申请开始日期
     * @param string $endDate 申请结束日期
     * @param integer $beginPeriod 申请开始时间段
     * @param integer $endPeriod 申请结束时间段
     * @return bool|string
     */
    private function chkApplyDate(int $userId, string $beginDate, string $endDate, int $beginPeriod, int $endPeriod) {
        /*
         * 申请时间会有四种情况
         * 1、新申请与已有的申请各不重叠
         * 2、新申请的开始时间或结束时间与已有申请的时间范围重叠，临界值新申请的开始时间是已有申请的结束时间或新申请的结束时间是已有申请的开始时间
         * 3、新申请的时间范围包含已有申请的时间范围
         * 4、新申请的时间范围在已有申请的时间范围之内 
         * 
         * 情况1可以通过数据库查询，加上条件 apply_begin_date between $beginDate 
         *      and $endDate or apply_end_date between $beginDate and $endDate进行排除
         * 以上查询结果是包括 情况2 和 情况3，针对这两种情况进行判断
         * 情况4需要通过数据库查询，加上条件 apply_begin_date >= $beginDate and apply_end_date <= $endDate 进行排除，
         *      存在记录，则该申请与已有申请重叠
         */
        
        $query = $this->modelDao;
        $query->where('user_id', $userId)->where('status', 'in', [self::APPLYING, self::APPLY_AGREE]);
        //查询前三种情况
        $error = '新申请的时间段内已存在其他申请';
        $firstQuery = clone $query;
        $list = $firstQuery
                ->fields('apply_begin_date,apply_begin_period,apply_end_date,apply_end_period,status')
                ->where(function($firstQuery) use($firstQuery, $beginDate, $endDate) {
                    $firstQuery->where('apply_begin_date', 'between', [$beginDate, $endDate])
                        ->whereOr('apply_end_date', 'between', [$beginDate, $endDate]);
                })
                ->find();

        if (!empty($list)) {
            //针对情况2,3进行遍历判断
            $beginFormat = self::formatDate($beginDate, $beginPeriod);
            $endFormat = self::formatDate($endDate, $endPeriod, 'end');
            foreach ($list as $k => $v) {
                $vBeginFormat = self::formatDate($v['apply_begin_date'],$v['apply_begin_period']);
                $vEndFormat = self::formatDate($v['apply_end_date'],$v['apply_end_period'],'end');
                if ($endFormat < $vBeginFormat) {
                    //新申请结束时间在已有申请开始时间之前，符合，继续遍历
                    continue;
                }
                if ($beginFormat > $vEndFormat) {
                    //新申请开始时间在已有申请结束时间之后，符合，继续遍历
                    continue;
                }
                //其他情况均重叠，返回错误信息
                return $error;
            } 
        }

        //查询情况4
        $secondQuery = clone $query;
        $count = $secondQuery->where('apply_begin_date', '<', $beginDate)->where('apply_end_date', '>', $endDate)->count();
        if ($count > 0) {
            //存在记录，时间冲突
            return $error;
        }
        return true;
    }
}