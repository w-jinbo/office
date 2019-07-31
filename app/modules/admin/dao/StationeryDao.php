<?php

/*
 * 文具管理Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:02:20 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:02:41
 */

namespace app\admin\dao;

use herosphp\filter\Filter;

class StationeryDao extends BaseDao {

    public static $filter = array(
        'name' => array(Filter::DFILTER_STRING, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '文具名称不能为空', 'length' => '文具名称长度必须在1~20之间')),
        'unit' => array(Filter::DFILTER_STRING, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '文具单位不能为空', 'length' => '文具单位长度必须在1~20之间')),
        'summary' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, null),
    );

    public function __construct() {
        parent::__construct('stationery');
        $this->primaryKey = 'id';
    }
}