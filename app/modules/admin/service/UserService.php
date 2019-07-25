<?php

/*
 * 用户管理服务类
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-25 17:42:54 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-25 17:43:19
 */

namespace app\admin\service;


use herosphp\filter\Filter;
use herosphp\model\CommonService;
use herosphp\session\Session;
use herosphp\utils\JsonResult;
use herosphp\http\HttpRequest;

class UserService extends CommonService {

    protected $modelClassName = 'app\admin\dao\UserDao';

    /**
     * 用户登录
     *
     * @param string $userName 用户名
     * @param string $passWord 密码
     * @return JsonResult $result
     */
    public function login(string $userName, string $passWord) {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        //查询账号是否存在
        $user = $this->isUser($userName);
        if (!$user) {
            $result->setMessage('没有该用户的账号信息');
            return $result;
        }

        if ($user['is_valid'] === 0){
            $result->setMessage('该账号已被禁用');
            return $result;
        }

        $md5Pwd = md5(md5($passWord) . $user['salt']);
        if ($md5Pwd != $user['password']) {
            $result->setMessage('账号或密码错误');
            return $result;
        }

        //通过密码验证，设置session
        Session::set('user_id', $user['id']);
        Session::set('username', $userName);

        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('登录成功');
        $result->setData(['url'=>'/admin/Main/index']);//跳转页面
        return $result;
    }

    /**
     * 获取用户列表数据
     *
     * @param HttpRequest $request 请求数组，包含分页，分页大小，条件
     * @return array $return
     */
    public function getListData(HttpRequest $request) {
        $query = $this->modelDao;
        $page = $request->getIntParam('page');
        $pageSize = $request->getIntParam('limit');
        $keyword = $request->getParameter('keyword', 'trim|urldecode');
        if (!empty($keyword)) {
            $query->whereOr('username', 'like', '%' . $keyword . '%')
                ->whereOr('realname', 'like', '%' . $keyword . '%')
                ->whereOr('department', 'like', '%' . $keyword . '%');
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
     * 增加用户操作
     * 
     * @param array $params 表单数据
     * @return JsonResult $result
     */
    public function addUser(array $params) {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            $result->setMessage($data);
            return $result;
        }
        $user = $this->isUser($data['username']);
        if ($user) {
            $result->setMessage('该电子邮箱已被注册');
            return $result;
        }

        $data['salt'] = rand(1000, 9999);
        $data['password'] = md5(md5($data['password']) . $data['salt']);
        $date = date('Y-m-d H:i:s');
        //是否有效
        $data['is_valid'] = isset($params['is_valid']) ? 1 : 0;
        $data['create_time'] = $date;
        $data['update_time'] = $date;
        $res = $this->modelDao->add($data);
        if ($res <= 0) {
            $result->setMessage('添加失败，请稍后重试');
            return $result;
        }
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('添加成功');
        return $result;
    }

    /**
     * 更新用户信息
     * @param array $params 表单数据
     * @param string $userId 更新用户记录id
     * @return JsonResult $result
     */
    public function updateUser(array $params, string $userId = '') {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            $result->setMessage($data);
            return $result;
        }
        if (empty($userId)) {
            $userId = Session::get('user_id');
        }
        //数据验证通过，更新用户数据
        $data['is_valid'] = isset($params['is_valid']) ? 1 : 0;
        $data['update_time'] = date('Y-m-d H:i:s');
        $res = $this->modelDao->update($data, $userId);
        if(!$res){
            //数据更新失败
            $result->setMessage('修改失败，请稍后重试');
            return $result;
        }
        //数据更新成功
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('修改成功');
        return $result;
    }

    /**
     * 修改密码操作
     * 
     * @param string $newPwd 新密码
     * @param string $oldPwd 旧密码
     * @return JsonResult $result
     */
    public function setPwd(string $newPwd, string $oldPwd) {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $userId = Session::get('user_id');
        $user = $this->modelDao->findById($userId);
        if (!$user) {
            $result->setMessage('没有找到用户信息');
            return $result;
        }

        //验证旧密码
        $chkOldPwd = md5(md5($oldPwd).$user['salt']);
        if ($chkOldPwd != $user['password']) {
            $result->setMessage('旧密码错误，验证失败');
            return $result;
        }

        //修改数据
        $update = array();
        $salt = rand(1000, 9999);
        $update['password'] = md5(md5($newPwd).$salt);
        $update['salt'] = $salt;
        $update['update_time'] = date('Y-m-d H:i:s');
        $res = $this->modelDao->update($update,$userId);
        if(!$res){
            //数据更新失败
            $result->setMessage('修改失败，请稍后重试');
            return $result;
        }
        //数据更新成功
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('修改成功，请重新登录');
        $result->setData(['url'=>'/admin/login/index']);
        Session::set('user_id', null);
        Session::set('username', null);
        return $result;
    }

    /**
     *　退出登录操作
     */
    public function quit() {
        Session::set('user_id', null);
        Session::set('username', null);

        location('/admin/login/index');
    }

    /**
     * 判断用户是否已经登录过
     * @return bool
     */
    public function isLogined() {
        $userName = Session::get('username');
        if (empty($userName)) {
            return false;
        }

        $user = $this->isUser($userName);
        if (!$user) {
            return false;
        }

        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        location('/admin/main/index');
    }

    /**
     * 判断用户是否存在
     * 
     * @param string $userName
     * @return array|bool
     */
    public function isUser (string $userName) {
        $user = $this->modelDao->where('username',$userName)->findOne();
        return empty($user) ? false :$user;
    }

    /**
     * 删除用户操作
     * 
     * @param string $ids 要删除的用户记录id集合
     * @return JsonResult $result
     */
    public function delUsers(string $ids) {
        $result = new JsonResult(JsonResult::CODE_FAIL, '系统开了小差');
        $idsArr = explode(',', $ids);
        $res = $this->modelDao->where('id', 'in', $idsArr)->deletes();
        if ($res <= 0) {
            $result->setMessage('删除失败，请稍后重试');
            return $result;
        }
        $result->setCode(JsonResult::CODE_SUCCESS);
        $result->setMessage('删除成功');
        return $result;
    }

    /**
     * 数据过滤
     * 
     * @param array $params 表单数据
     * @return array|string
     */
    private function dataFilter(array $params) {
        $filterMap = array(
            'username' => array(Filter::DFILTER_EMAIL, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '用户名不能为空', 'length' => '用户名长度必须在6~20之间', 'type' => '请输入正确的电子邮箱')),
            'password' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '密码不能为空')),
            // 'salt' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
            //     array('require' => '密码盐不能为空', 'length' => '密码盐长度必须是4个字符')),
            'role_ids' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '角色集合不能为空')),
            'realname' => array(Filter::DFILTER_STRING, array(2, 20), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '用户姓名不能为空', 'length' => '用户姓名长度必须在2~20之内')),
            'tel' => array(Filter::DFILTER_MOBILE, null, null, array('require' => '手机号码不能为空', 'type' => '请输入正确的手机号码')),
            'department' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '部门名称不能为空', 'length' => '部门名称长度必须在2~20之内'))
        );
        $data=$params;
        $data = Filter::loadFromModel($data, $filterMap, $error);
        return !$data ? $error : $data;
    }
}