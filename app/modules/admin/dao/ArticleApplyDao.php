<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class ArticleApplyDao extends MysqlModel {
    public function __construct() {
        parent::__construct('article_apply');
        $this->primaryKey = 'id';
    }
}