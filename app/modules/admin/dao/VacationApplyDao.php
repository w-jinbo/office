<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class VacationApplyDao extends MysqlModel {
    public function __construct() {
        parent::__construct('vacation_apply');
        $this->primaryKey = 'id';
    }
}