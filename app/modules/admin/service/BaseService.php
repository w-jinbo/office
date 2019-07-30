<?php

/*
 * 基础服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:40:07 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-29 17:32:26
 */

namespace app\admin\service;

use herosphp\model\CommonService;
use herosphp\utils\JsonResult;

class BaseService extends CommonService {

    /**
     * 获取列表数据
     *
     * @param sting $keyword 关键词
     * @param int $page 页码
     * @param int $pageSize 分页大小
     * @return array $return
     */
    public function getListData(string $keyword, int $page, int $pageSize) {
        $query = $this->modelDao;
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%')
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
     * 新增一条数据
     *
     * @param string $name
     * @param string $summary
     * @param int $isValid
     * @return int|bool
     */
    protected function addRow(string $name, string $summary, int $isValid) {
        $date = date('Y-m-d H:i:s');
        $data = array(
            'name' => $name,
            'summary' => $summary,
            'is_valid' => $isValid,
            'create_time' => $date,
            'update_time' => $date,
        );
        $result = $this->modelDao->add($data);
        return $result;
    }

    /**
     * 修改一条数据的信息
     *
     * @param int $id
     * @param string $name
     * @param string $summary
     * @param int $isValid
     * @return int|bool
     */
    protected function updateRow(int $id, string $name, string $summary, int $isValid) {
        $data = array(
            'name' => $name,
            'summary' => $summary,
            'is_valid' => $isValid,
            'update_time' => date('Y-m-d H:i:s'),
        );
        $result = $this->modelDao->update($data, $id);
        return $result;
    }

    /**
     * 删除多条数据
     *
     * @param string $ids
     * @return JsonResult
     */
    public function delRows(string $ids) {
        $idsArr = explode(',', $ids);
        $result = $this->modelDao->where('id', 'in', $idsArr)->deletes();
        return $result;
    }

    /**
     * 获取当前登录的用户信息
     *
     * @return void
     */
    protected function getUser() {
        $userService = new UserService();
        $user = $userService->getUser();
        return $user;
    }
}