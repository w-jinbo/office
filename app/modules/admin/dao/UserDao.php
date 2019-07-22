<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class UserDao extends MysqlModel {
    public function __construct() {
        parent::__construct('user');
        $this->primaryKey = 'id';
    }

    public function __clone() {
        // 强制复制一份this->sqlBuilder， 否则仍然指向同一个对象
        $this->sqlBuilder = clone $this->sqlBuilder;
    }
}