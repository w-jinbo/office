<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class NoticeDao extends MysqlModel {
    public function __construct() {
        parent::__construct('notice');
        $this->primaryKey = 'id';
    }
}