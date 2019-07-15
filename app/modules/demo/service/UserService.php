<?php
namespace app\demo\service;

use app\demo\dao\RauthorityDao;
use app\demo\dao\LoginErrorDao;
use app\demo\dao\RoleDao;
use herosphp\filter\Filter;
use herosphp\model\CommonService;
use herosphp\core\Loader;
use herosphp\session\Session;

/**
 * UserService
 * @author yangjian<yangjian102621@gmail.com>
 * @date 2017-07-05
 */
class UserService extends CommonService {


    /**
     * 模型类名称
     * @var string
     */
    protected $modelClassName= 'app\demo\dao\UserDao';


    /**
     * 1.过滤数据
     * 2.判断用户是否存在
     * 3.判断验证码
     * 4.判断密码
     * 5.密码错误次数5次内
     * 6.登录
     * 7.session值
     * */
    public function logins($username,$password,$scode) {

        if ( strtolower(Session::get('verifyCodes')) !== strtolower($scode))
        {
            return "验证码错误,请重新输入";
        }

        $userData = $this->isUser($username);

        if ( !$userData ) { return  '没有该用户信息.'; }

        $pwd = md5($password.$userData['salt']);

        if ( $pwd !== $userData['password'] ) {
           return   $this->pwdFalse($userData['id']);
        }

        $errorLogModel =  Loader::model(LoginErrorDao::class);
        $error = $errorLogModel->where('userID',$userData['id'])->findOne();
        $nowTime = date('Y-m-d H:i:s');
        if ($error['errorNum'] >= 5 && $nowTime < $error['releaseTime'])
        {
            return "请于".$error['releaseTime']."后登录";
        }else{
            $errorLogModel->where('userID',$userData['id'])->sets('errorNum','0');
        }

        Session::set('userID',$userData['id']);
        Session::set('name',$userData['name']);
        Session::set('roleID',$userData['roleID']);

        $roleDao = Loader::model(RauthorityDao::class);
        $data = $roleDao->where('roleID',$userData['roleID'])->findOne();

        Session::set('roleAuth',$data['authority']);
    }

    /**
     * 判断是否有该用户
     * @param string email
     * @return bool
     * */
    public function isUser($username)
    {
        $user = $this->modelDao->where("username","=",$username)->findOne();
        return $user;
    }

    /**
     * 用户登录次数限制
     * @param int $userid
     * @return string
     */
    private function pwdFalse($userid)
    {
        $errorNum = 1;
        $nowTime = date('Y-m-d H:i:s');
        $oneTime = date('Y-m-d H:i:s',strtotime("+1 Hour"));

        $errorLogModel =  Loader::model(LoginErrorDao::class);

        $isErrorLog = $errorLogModel->where("userID","=",$userid)->findOne();

        if ( $isErrorLog['errorNum'] >= '5' && $isErrorLog['releaseTime'] > $nowTime){
            return "请于".$isErrorLog['releaseTime']."后登录";
        }

        if ( !$isErrorLog ) {
            $errorData = [
                'userID' => $userid,
                'errorNum' => $errorNum,
                'errorTime' => $nowTime,
                'releaseTime' => $oneTime
            ];
            $errorLogModel->add($errorData);
        }
        if ( $isErrorLog['errorNum'] < 4 ){

            $errorLogModel->increase('errorNum',1,$isErrorLog['id']);
            return  "密码错误次数：".++$isErrorLog['errorNum']." . 当密码输入5次错误后,将锁定账户一个小时";
        } elseif ($isErrorLog['errorNum'] == 4) {
            $update = [
                'errorNum' => ++$isErrorLog['errorNum'],
                'errorTime' => $nowTime,
                'releaseTime' => $oneTime
            ];
            $errorLogModel->update($update,$isErrorLog['id']);
            return "登录密码已错误5次.请".$oneTime."后,再登录.谢谢";
        }

    }


    /**
     * 用户个人资料
     */
    public function userDate($id)
    {
        $user = $this->modelDao->findById($id);
        return $user;
    }

    /**
     * 添加用户
    */
    public function isFirst($userData)
    {
        $isUser = $this->isUser($userData['username']);
        if ($isUser)
        {
            return "该Email账号已有员工使用,请重新填写";
        }
        $data = $this->userFilter($userData);
        if (!is_array($data)){
            return $data;
        }
        $this->modelDao->add($data);
        return '添加成功';
    }

    public function userFilter($userData)
    {
        $filterMap = array(
            'name' => array(Filter::DFILTER_STRING, [ 2, 15 ],Filter::DFILTER_SANITIZE_TRIM,[ "require" => "名字不能为空.", "length" => "名字长度必需在2-15之间." ]),
            'password' => array(Filter::DFILTER_STRING,NULL,Filter::DFILTER_SANITIZE_TRIM, [ "require" => "密码不能为空."]),
            'username' => array(Filter::DFILTER_EMAIL, NULL, Filter::DFILTER_SANITIZE_TRIM, [ "type" => "请输入正确的邮箱地址." ]),
            'department' => array(Filter::DFILTER_STRING, [ 3, 30 ], Filter::DFILTER_SANITIZE_TRIM, [ "require" => "所属部门不能为空.", "length" => "所属部门长度必需在3-30之间." ]),
            'position' => array(Filter::DFILTER_STRING, [ 3, 30 ], Filter::DFILTER_SANITIZE_TRIM, [ "require" => "职务不能为空", "length" => "职务长度必需在3-30之间."]),
            'roleID' => array(Filter::DFILTER_NUMERIC, NULL, Filter::DFILTER_SANITIZE_TRIM, [ "require" => "角色不能为空"])
        );
        $salt = rand(1000,9999);
        $pwd = md5(md5($userData['password']).$salt);
        $data = array(
            'username'  =>  $userData['username'],
            'password'  =>  $pwd,
            'name'      =>  $userData['name'],
            'department'=>  $userData['department'],
            'position'  =>  $userData['position'],
            'roleID'    =>  $userData['roleID'],
            'salt'      =>  $salt
        );
        $to = Filter::loadFromModel($data,$filterMap,$error);
        if ( !$to )   {   return $error;    }
        return $data;
    }

    /**
     * 修改用户
     */
    public function updateUser($userData)
    {
        $data = $this->userFilter($userData);
        if (!is_array($data)){
            return $data;
        }
        $this->modelDao->where('username',$data['username'])
            ->updates($data);
        return '修改成功';
    }

    public function delUser($id)
    {
        $this->modelDao->delete($id);
        return '删除成功';
    }

    /**
     * 所有用户
     * */
    public function allUser($page,$pageSize)
    {
        $all = $this->modelDao->page($page,$pageSize)->find();
        return $all;
    }

    /**
     * 总行数
    */
    public function allCount()
    {
        $count = $this->modelDao->count();
        return $count;
    }







}