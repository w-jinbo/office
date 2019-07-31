<?php

/*
 * 系统公共Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:00:08 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:00:30
 */

namespace app\admin\dao;

use herosphp\filter\Filter;

class NoticeDao extends BaseDao {

    public static $filter = array(
        'title' => array(Filter::DFILTER_STRING, array(1, 100), Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '公告标题不能为空', 'length' => '公告标题长度必须在1~100之间')),
        'summary' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, null),
        'title' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, 
            array('require' => '公告正文不能为空')),
    );

    public function __construct() {
        parent::__construct('notice');
        $this->primaryKey = 'id';
    }
}