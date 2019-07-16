<?php


namespace app\admin\service;


use herosphp\filter\Filter;
use herosphp\model\CommonService;
use herosphp\session\Session;
use herosphp\utils\JsonResult;

class UserService extends CommonService {

    protected $modelClassName = 'app\admin\dao\UserDao';

    /**
     * 用户登录操作
     * @param $userName
     * @param $passWord
     * @return JsonResult
     */
    public function login ($userName, $passWord) {
        $result = new JsonResult(JsonResult::CODE_FAIL);
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

    public function updateUser($params, $userId = '') {
        $result = new JsonResult(JsonResult::CODE_FAIL);
        $data = $this->dataFilter($params);
        if (!is_array($data)) {
            $result->setMessage($data);
            return $result;
        }
        if (empty($userId)) {
            $userId = Session::get('user_id');
        }
        //数据验证通过，更新用户数据
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

    public function setPwd($newPwd, $oldPwd) {
        $result = new JsonResult(JsonResult::CODE_FAIL);
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
     * @param $userName
     * @return array|bool
     */
    private function isUser ($userName) {
        $user = $this->modelDao->where('username',$userName)->findOne();
        return empty($user) ? false :$user;
    }

    private function dataFilter($params) {
        $filterMap = array(
            'username' => array(Filter::DFILTER_EMAIL, array(1, 20), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '用户名不能为空', 'length' => '用户名长度必须在6~20之间', 'type' => '请输入正确的电子邮箱')),
            'password' => array(Filter::DFILTER_STRING, null, Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '密码不能为空')),
            'salt' => array(Filter::DFILTER_STRING, array(4, 4), Filter::DFILTER_SANITIZE_TRIM,
                array('require' => '密码盐不能为空', 'length' => '密码盐长度必须是4个字符')),
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