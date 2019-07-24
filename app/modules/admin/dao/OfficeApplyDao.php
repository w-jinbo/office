<?php


namespace app\admin\dao;



class OfficeApplyDao extends BaseDao {
    public function __construct() {
        parent::__construct('office_apply');
        $this->primaryKey = 'id';
    }
}