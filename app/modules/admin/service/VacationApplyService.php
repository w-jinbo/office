<?php

namespace app\admin\service;

use herosphp\http\HttpRequest;
use herosphp\filter\Filter;

class VacationApplyService extends BaseService {
    
    protected $modelClassName = 'app\admin\dao\VacationApplyDao';

    public function getListData(HttpRequest $request) {
        $query = $this->modelDao;
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        if (!empty($keyword)) {
            $query->whereOr('name', 'like', '%' . $keyword . '%')
                ->whereOr('summary', 'like', '%' . $keyword . '%');
        }

        $return = array(
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => 0,
            'list' => array()
        );

        //克隆查询对象，防止查询条件丢失
        $countQuery = clone $query;
        $total = $countQuery->count();
        if ($total <= 0) {
            return $return;
        }

        $data = $query->page($page, $pageSize)->order('id desc')->find();
        $return['total'] = $total;
        $return['list'] = $data;
        return $return;
    }

    protected function dataFilter(array $params) {
        $filterMap = array(
            'name' => array(Filter::DFILTER_STRING, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '假期名称不能为空', 'length' => '假期名称长度必须在1~20之间')),
            'summary' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, null),
        );
        $data = $params;
        $data = Filter::loadFromModel($data, $filterMap, $error);
        return !$data ? $error : $data;
    }
}