<?php


namespace app\admin\dao;



class VacationApplyDao extends BaseDao {
    public function __construct() {
        parent::__construct('vacation_apply');
        $this->primaryKey = 'id';
    }
}