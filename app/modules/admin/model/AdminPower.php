<?php


namespace app\admin\model;


class AdminPower {
    public static function getPowerArray($apCodes = '') {
        $powerArr = array(
            array('id'=>'user_manage', 'pId'=>'', 'name'=>'用户管理'),

            array('id'=>'user_list', 'pId'=>'user_manage', 'name'=>'用户列表'),
            array('id'=>'user_list_view', 'pId'=>'user_list', 'name'=>'查看'),
            array('id'=>'user_list_add', 'pId'=>'user_list', 'name'=>'添加'),
            array('id'=>'user_list_edit', 'pId'=>'user_list', 'name'=>'修改'),
            array('id'=>'user_list_del', 'pId'=>'user_list', 'name'=>'删除'),
            array('id'=>'user_list_reset_pwd', 'pId'=>'user_list', 'name'=>'重置密码'),

            array('id'=>'role_list', 'pId'=>'user_manage', 'name'=>'角色列表'),
            array('id'=>'role_list_view', 'pId'=>'role_list', 'name'=>'查看'),
            array('id'=>'role_list_add', 'pId'=>'role_list', 'name'=>'添加'),
            array('id'=>'role_list_edit', 'pId'=>'role_list', 'name'=>'修改'),
            array('id'=>'role_list_del', 'pId'=>'role_list', 'name'=>'删除'),


            array('id'=>'vacation_manage', 'pId'=>'', 'name'=>'假期调休管理'),

            array('id'=>'vacation_list', 'pId'=>'vacation_manage', 'name'=>'假期类型列表'),
            array('id'=>'vacation_list_view', 'pId'=>'vacation_list', 'name'=>'查看'),
            array('id'=>'vacation_list_add', 'pId'=>'vacation_list', 'name'=>'添加'),
            array('id'=>'vacation_list_edit', 'pId'=>'vacation_list', 'name'=>'修改'),
            array('id'=>'vacation_list_del', 'pId'=>'vacation_list', 'name'=>'删除'),

            array('id'=>'vacation_apply', 'pId'=>'vacation_manage', 'name'=>'假期申请'),
            array('id'=>'vacation_apply_view', 'pId'=>'vacation_apply', 'name'=>'查看'),
            array('id'=>'vacation_apply_add', 'pId'=>'vacation_apply', 'name'=>'添加'),
            array('id'=>'vacation_apply_cancel', 'pId'=>'vacation_apply', 'name'=>'取消'),
            array('id'=>'vacation_apply_audit', 'pId'=>'vacation_apply', 'name'=>'审批'),
        );

        if (!empty($apCodes)) {
            foreach ($powerArr as $k => $v) {
                if (strpos($apCodes, ',' . $v ['id'] . ',') !== false) {
                    $powerArr[$k]['checked'] = true;
                }
            }
        }
        return $powerArr;
    }
}