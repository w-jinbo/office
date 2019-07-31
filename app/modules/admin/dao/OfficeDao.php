<?php

/*
 * 办公室管理Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:59:20 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:59:50
 */

namespace app\admin\dao;

use herosphp\filter\Filter;

class OfficeDao extends BaseDao {

    public static $filter = array(
        'name' => array(Filter::DFILTER_STRING, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '办公室名称不能为空', 'length' => '办公室名称长度必须在1~20之间')),
        'address' => array(Filter::DFILTER_STRING, array(1, 255), Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, 
            array('require' => '办公室地址不能为空')),
        'summary' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, null),
    );

    public function __construct() {
        parent::__construct('office');
        $this->primaryKey = 'id';
    }
}