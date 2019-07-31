<?php

/*
 * 文具申请子项Dao
 * @Author: WangJinBo <wangjb@ovc123.com>
 * @Date: 2019-07-25 18:01:27 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:02:18
 */

namespace app\admin\dao;

use herosphp\filter\Filter;

class StationeryApplyItemDao extends BaseDao {

    public static $filter = array(
        'stationery_id' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '申请文具id不能为空')),
        'stationery_name' => array(Filter::DFILTER_STRING, array(1, 50), Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '申请文具名称不能为空',  'length' => '申请文具名称长度必须在1~50之内')),
        'stationery_unit' => array(Filter::DFILTER_STRING, array(1, 10), Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '申请文具单位不能为空',  'length' => '申请文具单位长度必须在1~10之内')),
        'apply_num' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '申请数量不能为空')),
        'grant_num' => array(Filter::DFILTER_NUMERIC, null, Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '发放数量不能为空'))
    );

    public function __construct() {
        parent::__construct('stationery_apply_item');
        $this->primaryKey = 'id';
    }
}