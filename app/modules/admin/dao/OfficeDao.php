<?php


namespace app\admin\dao;



class OfficeDao extends BaseDao {
    public function __construct() {
        parent::__construct('office');
        $this->primaryKey = 'id';
    }
}