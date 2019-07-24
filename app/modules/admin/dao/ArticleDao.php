<?php


namespace app\admin\dao;



class ArticleDao extends BaseDao {
    public function __construct() {
        parent::__construct('article');
        $this->primaryKey = 'id';
    }
}