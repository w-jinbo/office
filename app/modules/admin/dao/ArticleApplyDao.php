<?php


namespace app\admin\dao;



class ArticleApplyDao extends BaseDao {
    public function __construct() {
        parent::__construct('article_apply');
        $this->primaryKey = 'id';
    }
}