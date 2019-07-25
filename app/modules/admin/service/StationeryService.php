<?php

/*
 * 文具管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 16:47:54 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:42:46
 */

namespace app\admin\service;

use herosphp\http\HttpRequest;
use herosphp\filter\Filter;

class StationeryService extends BaseService{

    protected $modelClassName = 'app\admin\dao\StationeryDao';

    /**
     * 获取列表数据
     *
     * @param HttpRequest $request
     * @return array $return
     */
    public function getListData(HttpRequest $request){
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

    /**
     * 获取有效的文具数组
     *
     * @return array|bool
     */
    public function stationeryList() {
        $list = $this->modelDao->fields('id, name')->where('is_valid', '1')->find();
        return $list;
    }

    /**
     * 数据过滤
     *
     * @param array $params
     * @return array|string
     */
    protected function dataFilter(array $params) {
        $filterMap = array(
            'name' => array(Filter::DFILTER_STRING, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '文具名称不能为空', 'length' => '文具名称长度必须在1~20之间')),
            'unit' => array(Filter::DFILTER_STRING, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '文具单位不能为空', 'length' => '文具单位长度必须在1~20之间')),
            'summary' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, null),
        );
        $data = $params;
        $data = Filter::loadFromModel($data, $filterMap, $error);
        return !$data ? $error : $data;
    }
}
