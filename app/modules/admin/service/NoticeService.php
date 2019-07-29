<?php

/*
 * 公告服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-29 08:40:28 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-29 16:32:04
 */

namespace app\admin\service;

use herosphp\http\HttpRequest;
use herosphp\filter\Filter;
use app\admin\dao\NoticeDao;

class NoticeService extends BaseService {

    protected $modelClassName = NoticeDao::class;

    public function getListData(HttpRequest $request) {
        $query = $this->modelDao;
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        if (!empty($keyword)) {
            $query->whereOr('title', 'like', '%' . $keyword . '%')
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
     * 数据过滤
     *
     * @param array $params
     * @return array|string
     */
    protected function dataFilter(array $params) {
        $filterMap = array(
            'title' => array(Filter::DFILTER_STRING, array(1, 100), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '公告标题不能为空', 'length' => '公告标题长度必须在1~100之间')),
            'summary' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, null),
            'title' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM | Filter::DFILTER_MAGIC_QUOTES, 
                array('require' => '公告正文不能为空')),
        );
        $data = $params;
        unset($data['ap_codes']);
        $data['permissions'] = implode(',', $params['ap_codes']);
        $data = Filter::loadFromModel($data, $filterMap, $error);
        return !$data ? $error : $data;
    }
}