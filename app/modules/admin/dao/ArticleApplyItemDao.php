<?php


namespace app\admin\dao;


use herosphp\model\MysqlModel;

class ArticleApplyItemDao extends MysqlModel {
    public function __construct() {
        parent::__construct('article_apply_item');
        $this->primaryKey = 'id';
    }
}