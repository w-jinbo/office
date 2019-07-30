<?php

/*
 * 用户管理Dao
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 18:02:45 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 18:03:08
 */

namespace app\admin\dao;

use herosphp\filter\Filter;

class UserDao extends BaseDao {

    public static $filter = array(
        'username' => array(Filter::DFILTER_EMAIL, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '用户名不能为空', 'length' => '用户名长度必须在6~20之间', 'type' => '请输入正确的电子邮箱')),
        'password' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '密码不能为空')),
        'role_ids' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '角色集合不能为空')),
        'realname' => array(Filter::DFILTER_STRING, array(2, 20), Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '用户姓名不能为空', 'length' => '用户姓名长度必须在2~20之内')),
        'tel' => array(Filter::DFILTER_MOBILE, null, null, array('require' => '手机号码不能为空', 'type' => '请输入正确的手机号码')),
        'department' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
            array('require' => '部门名称不能为空', 'length' => '部门名称长度必须在2~20之内'))
    );

    public function __construct() {
        parent::__construct('user');
        $this->primaryKey = 'id';
    }
}