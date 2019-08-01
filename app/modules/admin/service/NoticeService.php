<?php

/*
 * 公告服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-29 08:40:28 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-08-01 15:36:46
 */

namespace app\admin\service;

use app\admin\dao\NoticeDao;

class NoticeService extends BaseService {

    protected $modelClassName = NoticeDao::class;

    /**
     * 获取列表数据
     *
     * @param string $keyword 关键词
     * @param integer $page 分页
     * @param integer $pageSize 分页大小
     * @param integer $isValid 是否有效
     * @return array $return 
     */
    public function getListData(string $keyword, int $page, int $pageSize, int $isValid = null) {
        $query = $this->modelDao;
        
        //防止关键词为空时，SQL语句错误
        $query->where('id', '>', 0);

        if (!empty($keyword)) {
            $query->where(function($query) use($query, $keyword) {
                $query->where('title', 'like', '%' . $keyword . '%')
                    ->whereOr('summary', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($isValid)) {
            $query->where('is_valid', $isValid);
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
     * 添加公告
     *
     * @param string $title 公告标题
     * @param string $summary 描述
     * @param string $content 正文内容
     * @param integer $isValid 是否有效
     * @return bool|int
     */
    public function addNotice(string $title, string $summary, string $content, int $isValid) {
        $date = date('Y-m-d H:i:s');
        $data = array(
            'title' => $title,
            'summary' => $summary,
            'content' => $content,
            'is_valid' => $isValid,
            'create_time' => $date,
            'update_time' => $date,
        );
        $result = $this->modelDao->add($data);
        return $result;
    }

    /**
     * 修改公告信息
     *
     * @param integer $id 记录id
     * @param string $title 公告标题
     * @param string $summary 描述
     * @param string $content 正文内容
     * @param integer $isValid 是否有效
     * @return bool|int
     */
    public function updateNotice(int $id, string $title, string $summary, string $content, int $isValid) {
        $data = array(
            'title' => $title,
            'summary' => $summary,
            'content' => $content,
            'is_valid' => $isValid,
            'update_time' => date('Y-m-d H:i:s'),
        );
        $result = $this->modelDao->update($data, $id);
        return $result;
    }
}