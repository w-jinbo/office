<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class OfficeApplyDao extends MysqlModel {
    public function __construct() {
        parent::__construct('office_apply');
        $this->primaryKey = 'id';
    }
}