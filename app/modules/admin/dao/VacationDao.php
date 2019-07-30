<?php

/*
 * 假期管理Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:03:48 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:04:13
 */

namespace app\admin\dao;

use herosphp\filter\Filter;

class VacationDao extends BaseDao {

    public static $filter = array(
        'name' => array(Filter::DFILTER_STRING, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '假期名称不能为空', 'length' => '假期名称长度必须在1~20之间')),
        'summary' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, null),
    );

    public function __construct() {
        parent::__construct('vacation');
        $this->primaryKey = 'id';
    }
}