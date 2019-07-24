<?php


namespace app\admin\dao;



class VacationDao extends BaseDao {
    public function __construct() {
        parent::__construct('vacation');
        $this->primaryKey = 'id';
    }
}