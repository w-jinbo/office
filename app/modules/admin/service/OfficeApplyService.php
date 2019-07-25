<?php

/*
 * 办公室申请服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:40:45 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:41:18
 */

namespace app\admin\service;

use herosphp\session\Session;
use herosphp\utils\JsonResult;
use herosphp\filter\Filter;
use herosphp\http\HttpRequest;

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
    
    /**
     * 获取列表数据
     *
     * @param HttpRequest $request
     * @return array
     */
    public function getListData(HttpRequest $request) {
        $query = $this->modelDao;
        $query->alias('a')
            ->join('user b', MYSQL_JOIN_INNER)
            ->on('a.user_id = b.id');
            
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        $status = $request->getParameter('status', 'trim|urldecode');
        $searchDate = $request->getParameter('searchDate', 'trim|urldecode');
        $type = $request->getIntParam('type');
        $userId = session::get('user_id');
        if ($type == 1) {
            //我的申请列表，只加载用户自己的申请记录
            $query->where('user_id', $userId);
        } else {
            //审核列表，自己不能审核自己的申请
            $query->where('user_id', '!=', $userId);
        }
        $searchDateArr = explode(' - ', $searchDate);
        $query->where('apply_date', 'between', $searchDateArr);
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
     * @param array $params
     * @return JsonResult
     */
    public function addApply(array $params) {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $userId = Session::get('user_id');
        $params['user_id'] = $userId;
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            $result->setMessage($data);
            return $result;
        }
        $error = $this->chkApplyDate($params['office_id'], $params['apply_date'], 
            $params['apply_begin_time'], $params['apply_end_time']);
        if(!($error === true)){
            return new JsonResult(JsonResult::CODE_FAIL, $error);
        }
        $date = date('Y-m-d H:i:s');
        $data['create_time'] = $date;
        $data['update_time'] = $date;
        $data['status'] = self::APPLIED;
        $res = $this->modelDao->add($data);
        if ($res <= 0) {
            $result->setMessage('申请失败，请稍后重试');
            return $result;
        }
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('申请成功');
        return $result;
    }

    /**
     * 更新申请记录
     *
     * @param array $params
     * @param integer $applyId
     * @return JsonResult
     */
    public function updateApply(array $params, int $applyId) {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            $result->setMessage($data);
            return $result;
        }
        $res = $this->modelDao->update($data, $applyId);
        if(!$res){
            //数据更新失败
            $result->setMessage('审批失败，请稍后重试');
            return $result;
        }
        //数据更新成功
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('审批成功');
        return $result;
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
     * 数据过滤
     *
     * @param array $params
     * @return bool|array
     */
    protected function dataFilter(array $params) {
        $filterMap = array(
            'user_id' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '申请人不能为空')),
            'office_id' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '假期类型不能为空')),
            'office_name' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '假期类型名称不能为空')),
            'apply_reason' => array(Filter::DFILTER_STRING, array(1, 255), Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES,
                array('require' => '申请原因不能为空',  'length' => '申请原因长度必须在1~255之内')),
            'apply_date' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '申请日期不能为空')),
            'apply_begin_time' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '申请开始时间不能为空')),
            'apply_end_time' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '申请结束时间不能为空')),
            'audit_user_id' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '审核人id不能为空')),
            'audit_user_realname' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '审核人姓名不能为空')),
            'audit_opinion' => array(Filter::DFILTER_STRING, array(1, 255), Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES,
                array('require' => '审核意见不能为空',  'length' => '审核意见长度必须在1~255之内')),
        );
        $data = $params;
        $data = Filter::loadFromModel($data, $filterMap, $error);
        return !$data ? $error : $data;
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