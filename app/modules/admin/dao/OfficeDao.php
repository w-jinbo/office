<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class OfficeDao extends MysqlModel {
    public function __construct() {
        parent::__construct('office');
        $this->primaryKey = 'id';
    }
}