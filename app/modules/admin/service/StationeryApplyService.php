<?php

/*
 * 文具申请服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-26 08:44:25 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-26 17:43:01
 */

namespace app\admin\service;

use herosphp\utils\JsonResult;
use herosphp\session\Session;
use herosphp\filter\Filter;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;

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

    protected $modelClassName = 'app\admin\dao\StationeryApplyDao';

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
     * @param HttpRequest $request
     * @return void
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
        $searchDateArr[1] = $searchDateArr[1] . ' 23:59:59';
        $query->where('a.create_time', 'between', $searchDateArr);
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
     * @param array $params
     * @return void
     */
    public function addApply(array $params) {
        $query = $this->modelDao;
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $userId = Session::get('user_id');
        $params['user_id'] = $userId;
        //申请的文具子项
        $itemArr = $params['item'];
        unset($params['item']);
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            $result->setMessage($data);
            return $result;
        }
        $date = date('Y-m-d H:i:s');
        $data['create_time'] = $date;
        $data['update_time'] = $date;
        $data['status'] = self::APPLIED;
        $query->beginTransaction();
        $res = $query->add($data);
        if ($res <= 0) {
            $query->rollback();
            $result->setMessage('申请失败，请稍后重试');
            return $result;
        }
        //添加申请成功，将申请物品项添加到关联表中
        $stationeryItemService = Loader::service(StationeryApplyItemService::class);
        foreach ($itemArr as $key => $item) {
            $temp = array();
            $temp['stationery_apply_id'] = $res;
            $temp['stationery_id'] = $item['id'];
            $temp['stationery_name'] = $item['name'];
            $temp['stationery_unit'] = $item['unit'];
            $temp['apply_num'] = $item['num'];
            $stationeryItemFlag = $stationeryItemService->addRow($temp);
            if (!$stationeryItemFlag) {
                $query->rollback();
                $result->setMessage('申请失败，请稍后重试');
                return $result;
            }
        }
        $query->commit();
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('申请成功');
        return $result;
    }

    /**
     * 申请审批
     *
     * @param array $params
     * @param integer $applyId
     * @return JsonResult
     */
    public function auditApply(array $params, int $applyId) {
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
     * 发放文具
     *
     * @param array $params
     * @param array $itemArr
     * @param integer $applyId
     * @return JsonResult
     */
    public function grant(array $params, array $itemArr, int $applyId) {
        $query = $this->modelDao;
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        if (empty($params['grant_remark'])) {
            unset($params['grant_remark']);
        }
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            $result->setMessage($data);
            return $result;
        }
        $data['status'] = self::RECEIVED;
        $query->beginTransaction();
        $res = $query->update($data, $applyId);
        if(!$res){
            //数据更新失败
            $query->rollback();
            $result->setMessage('发放失败，请稍后重试');
            return $result;
        }

        //更新发放数量
        $stationeryItemService = Loader::service(StationeryApplyItemService::class);
        foreach ($itemArr as $key => $item) {
            $temp = array();
            $temp['grant_num'] = $item;
            $stationeryItemFlag = $stationeryItemService->updateRow($temp, $key);
            if (!$stationeryItemFlag) {
                $query->rollback();
                $result->setMessage('发放失败，请稍后重试');
                return $result;
            }
        }
        $query->commit();
        //数据更新成功
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('发放成功');
        return $result;
        
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

    /**
     * 数据过滤
     *
     * @param array $params
     * @return array|string
     */
    private function dataFilter(array $params) {
        $filterMap = array(
            'user_id' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '申请人不能为空')),
            'apply_reason' => array(Filter::DFILTER_STRING, array(1, 255), Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES,
                array('require' => '申请原因不能为空',  'length' => '申请原因长度必须在1~255之内')),
            'audit_user_id' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '审核人id不能为空')),
            'audit_user_realname' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '审核人姓名不能为空')),
            'audit_opinion' => array(Filter::DFILTER_STRING, array(1, 255), Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES,
                array('require' => '审核意见不能为空',  'length' => '审核意见长度必须在1~255之内')),
            'grant_remark' => array(Filter::DFILTER_STRING, array(1, 255), Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES,
                array('require' => '发放备注不能为空',  'length' => '发放备注长度必须在1~255之内')),
        );
        $data = $params;
        $data = Filter::loadFromModel($data, $filterMap, $error);
        return !$data ? $error : $data;
    }
}
