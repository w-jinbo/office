<?php


namespace app\admin\dao;



class ArticleApplyItemDao extends BaseDao {
    public function __construct() {
        parent::__construct('article_apply_item');
        $this->primaryKey = 'id';
    }
}