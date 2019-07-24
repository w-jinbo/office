<?php


namespace app\admin\dao;



class NoticeDao extends BaseDao {
    public function __construct() {
        parent::__construct('notice');
        $this->primaryKey = 'id';
    }
}