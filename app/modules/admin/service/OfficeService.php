<?php

/*
 * 办公室管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:41:23 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:41:52
 */

namespace app\admin\service;

use herosphp\http\HttpRequest;
use herosphp\filter\Filter;

class OfficeService extends BaseService {

    protected $modelClassName = 'app\admin\dao\OfficeDao';

    public function getListData(HttpRequest $request) {
        $query = $this->modelDao;
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        if (!empty($keyword)) {
            $query->whereOr('name', 'like', '%' . $keyword . '%')
                ->whereOr('address', 'like', '%' . $keyword . '%')
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
     * 获取有效的办公室数组
     *
     * @return array|bool
     */
    public function officeList() {
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
                array('require' => '办公室名称不能为空', 'length' => '办公室名称长度必须在1~20之间')),
            'address' => array(Filter::DFILTER_STRING, array(1, 255), Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, 
                array('require' => '办公室地址不能为空')),
            'summary' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, null),
        );
        $data = $params;
        $data = Filter::loadFromModel($data, $filterMap, $error);
        return !$data ? $error : $data;
    }
}