<?php

namespace app\admin;

use herosphp\core\WebApplication;
use herosphp\http\HttpRequest;
use herosphp\listener\IWebAplicationListener;
use herosphp\listener\WebApplicationListenerMatcher;
use herosphp\session\Session;
use app\admin\service\UserService;
use herosphp\utils\JsonResult;
use app\admin\action\BaseAction;

/**
 * 当前模块请求的生命周期监听器
 * @author yangjian<yangjian102621@gmail.com>
 */
class ModuleListener extends WebApplicationListenerMatcher implements IWebAplicationListener {

    protected static $uriWhileList = array(
        '/',
        '/admin/login/',
        '/admin/main/',
    );

    /**
     * action 方法调用之前
     * @return mixed
     */
    public function beforeActionInvoke(HttpRequest $request) {
        Session::start();
        //权限认证的代码可以写在这里
        //die("您没有权限。");
        // $userService = new UserService();
        // $user =$userService->getUser();
        // var_dump($user);exit;
        $this->chkPermission($request);
    }

    /**
     * 响应发送之前
     * @return mixed
     */
    public function beforeSendResponse(HttpRequest $request, $actionInstance) {}

    /**
     * 响应发送之后
     * @return mixed
     */
    public function afterSendResponse($actionInstance) {
        // TODO: Implement afterSendResponse() method.
    }

    /**
     * 检查权限
     *
     * @param HttpRequest $request
     * @return mixed
     */
    private function chkPermission(HttpRequest $request) {
        $module = $request->getModule();
        $action = $request->getAction();
        $method = $request->getMethod();
        $permission = $module . '_' . $action . '_' . $method;
        $permission = strtolower($permission);

        $requestUri = '/' . $module . '/' . $action . '/';
        $requestUri = strtolower($requestUri);

        //白名单过滤
        if (in_array($requestUri, self::$uriWhileList)) {
            return true;
        }

        $chkFlag = false;
        //获取用户权限
        $userService = new UserService();
        $user = $userService->getUser();
        $permissionArr =  $user->getPermissions();

        //验证权限
        $chkFlag = filterByValue($permissionArr, 'permission', $permission);
        if (!$chkFlag) {
            //不通过，验证url
            $requestUri = $request->getRequestUri();
            $chkFlag = filterByValue($permissionArr, 'url', $requestUri);
        }

        if (!$chkFlag) {
            //权限验证不通过，返回错误页面
            $errorMsg = '您没有权限进行此操作';
            $requestMethod = getRequestMethod();
            if ($requestMethod == 'AJAX') {
                JsonResult::fail($errorMsg);
            }
            $controller = new BaseAction();
            $controller->error($errorMsg);
        }
        return true;
    }
}
