<?php

namespace app\admin\traits;

use herosphp\filter\Filter;

trait DataFilterTraits {

    public function dataFilter(array $filterMap, array $params) {
        $data = Filter::loadFromModel($params, $filterMap, $error);
        return !$data ? $error : $data;
    }
}