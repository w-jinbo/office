<?php


namespace app\admin\service;


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

    public function updateUser($params) {
        $data = $this->dataFilter($params);
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

    }
}