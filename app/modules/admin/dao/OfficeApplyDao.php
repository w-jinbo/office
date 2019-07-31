<?php

/*
 * 办公室申请Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:58:44 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:59:16
 */

namespace app\admin\dao;

use herosphp\filter\Filter;

class OfficeApplyDao extends BaseDao {

    public static $filter = array(
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

    public function __construct() {
        parent::__construct('office_apply');
        $this->primaryKey = 'id';
    }
}