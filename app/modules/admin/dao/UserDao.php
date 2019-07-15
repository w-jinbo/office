<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class UserDao extends MysqlModel {
    public function __construct() {
        parent::__construct('user');
        $this->primaryKey = 'id';
    }
}