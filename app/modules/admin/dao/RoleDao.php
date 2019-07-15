<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class RoleDao extends MysqlModel {
    public function __construct() {
        parent::__construct('role');
        $this->primaryKey = 'id';
    }
}