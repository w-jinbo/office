<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-29
 * Time: 下午4:07
 */

namespace app\demo\dao;


use herosphp\model\MysqlModel;

class RoomDao extends MysqlModel
{
    public function __construct()
    {
        parent::__construct('room');

        $this->primaryKey = 'id';

    }
}