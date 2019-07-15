<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class VacationDao extends MysqlModel {
    public function __construct() {
        parent::__construct('vacation');
        $this->primaryKey = 'id';
    }
}