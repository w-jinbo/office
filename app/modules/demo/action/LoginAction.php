<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-9-4
 * Time: 下午4:40
 */

namespace app\demo\action;


use app\demo\service\UserService;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\image\VerifyCode;
use herosphp\session\Session;

class LoginAction extends ControllerRepeat
{
    private  $userServer ;
    
    public function  __construct()
    {
        parent::__construct();
        $this->userServer  = Loader::service(UserService::class);
    }
    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request)
    {
        $this->setView("login/login");
    }

    /**
     *登录方法
     */
    public function login(HttpRequest $request)
    {
        $username = $request->getParameter('username', 'trim|urldecode');
        $password = $request->getParameter('password', 'md5');//md5
        $scode = $request->getParameter('scode', 'trim');
        $info = $this->userServer->logins($username,$password,$scode);
        if ( $info ){
            $this->assign("tip",$info);
            $this->setView('login/login');
        }   else {
            location('/demo/index/user') ;
        }
    }


    /**
     *验证码
     */
    public function verify()
    {
        $config = [ 'x'=>10, 'y'=>30, 'w'=>120, 'h'=>50, 'f'=>22 ];
        $ver =  VerifyCode::getInstance();
        Session::set('verifyCodes',$ver->configure($config)->generate(1));
        $ver->show('png');
    }


}