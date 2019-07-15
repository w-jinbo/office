<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-28
 * Time: 上午10:53
 */

namespace app\demo\dao;


use herosphp\model\MysqlModel;

class LoginErrorDao extends MysqlModel
{
    public function __construct(){

        //创建model对象并初始化数据表名称
         parent::__construct("loginError_log");

        //设置表数据表主键，默认为id
        $this->primaryKey = "id";

    }
}