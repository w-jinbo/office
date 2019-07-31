<?php

namespace app\admin\dao;

class SystemTipDao extends BaseDao {

    public function __construct() {
        parent::__construct('system_tip');
        $this->primaryKey = 'id';
    }
}