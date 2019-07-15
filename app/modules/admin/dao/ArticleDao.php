<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class ArticleDao extends MysqlModel {
    public function __construct() {
        parent::__construct('article');
        $this->primaryKey = 'id';
    }
}