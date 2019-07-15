<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-29
 * Time: 上午11:27
 */

namespace app\demo\dao;

use herosphp\model\MysqlModel;

class VacationDao extends MysqlModel
{
    public function __construct()
    {
        parent::__construct('vacation');

        $this->primaryKey = 'id';

    }
}