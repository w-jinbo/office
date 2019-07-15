<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-30
 * Time: 下午2:41
 */

namespace app\demo\dao;


use herosphp\model\MysqlModel;

class GoodsDao extends MysqlModel
{
    public function __construct()
    {
        parent::__construct('goods');

        $this->primaryKey = 'id';

    }
}