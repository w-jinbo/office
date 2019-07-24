<?php


namespace app\admin\dao;



class RoleDao extends BaseDao {
    public function __construct() {
        parent::__construct('role');
        $this->primaryKey = 'id';
    }
}