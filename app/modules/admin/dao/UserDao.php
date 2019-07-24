<?php


namespace app\admin\dao;



class UserDao extends BaseDao {
    public function __construct() {
        parent::__construct('user');
        $this->primaryKey = 'id';
    }
}