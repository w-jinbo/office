<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-31
 * Time: 上午10:52
 */

namespace app\demo\dao;


use herosphp\model\MysqlModel;

class RoleDao extends MysqlModel
{
    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('role');

        //设置表数据表主键，默认为id
        $this->primaryKey = 'id';
    }
}