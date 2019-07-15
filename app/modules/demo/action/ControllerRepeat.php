<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-28
 * Time: 下午6:00
 */

namespace app\demo\action;


use herosphp\core\Controller;
use herosphp\http\HttpRequest;
use herosphp\session\Session;
use herosphp\utils\Page;

class ControllerRepeat extends Controller
{

    public function  __construct()
    {
        parent::__construct();
        Session::start();
    }

    /**
     * 判断用户是否登录
    */
    protected function isLogin(){
        if ( !isset($_SESSION['userID']) )   {
            location('/demo/login/index');
            exit();
        }
    }


    /**
     * 处理权限
     * @param String $roleNum
     * @return array
    */
    protected function roleString($roleNum){
        $roleArray = ControllerRepeat::_arrayEx($roleNum);
        $data = $this->_main($roleArray);
        return $data;
    }
    private static function _arrayEx($roleNum)
    {
        $roleArray = explode(',',$roleNum);
        array_pop($roleArray);
        return $roleArray;
    }

    protected function isAuth($roleNum,$uri)
    {
        $arr = parse_url($uri);
        $uri = $arr['path'];
        $roleArray = ControllerRepeat::_arrayEx($roleNum);
        $menu = $this->mainMenu();
        foreach ( $menu as $k => $val)
        {
            if ($val['href'] == $uri)
            {
                $authID = $k;
            }
        }
        $is = in_array($authID,$roleArray);
        if (!$is)
        {
            location('/demo/index/user');
        }
    }


    /**
     * 权限与菜单配置操作
     * @param array $arrRole
     * @return array
    */
    private function _main($arrRole)
    {
        $arrNew = [];
        $menu = $this->mainMenu();
        foreach ( $arrRole as $v )
        {
            foreach ( $menu as $k => $val)
            {
                if ($v == $k)
                {
                   //var_dump($val);
                    $arrNew[$val['fatherName']][$v]['name'] = $val['name'];
                    $arrNew[$val['fatherName']][$v]['href'] = $val['href'];
                }
            }
        }
        return $arrNew;
    }

    /**
     * 菜单配置
     */
    public function mainMenu()
    {
        $main = array(
            '1'     => [
                'name'          =>  '基本资料',
                'href'          =>  '/demo/index/user',
                'fatherName'    =>  '账户管理'
            ],
            '2'     => [
                'name'          =>  '假期申请',
                'href'          =>  '/demo/vacation/apply',
                'fatherName'    =>  '申请项目'
            ],
            '3'     => [
                'name'          =>  '办公室申请',
                'href'          =>  '/demo/room/apply',
                'fatherName'    =>  '申请项目'
            ],
            '4'     => [
                'name'          =>  '物品领取',
                'href'          =>  '/demo/goods/apply',
                'fatherName'    =>  '申请项目'
            ],
            '5'     => [
                'name'          =>  '查看内部公告',
                'href'          =>  '/demo/news/apply',
                'fatherName'    =>  '内部公告'
            ],
            '6'     => [
                'name'          =>  '发布内部公告',
                'href'          =>  '/demo/news/writeNew',
                'fatherName'    =>  '内部公告'
            ],
            '7'     => [
                'name'          =>  '假期申请审核',
                'href'          =>  '/demo/vacation/trial',
                'fatherName'    =>  '申请审核'
            ],
            '8'     => [
                'name'          =>  '办公室申请审核',
                'href'          =>  '/demo/room/trial',
                'fatherName'    =>  '申请审核'
            ],
            '9'     => [
                'name'          =>  '物品领用审核',
                'href'          =>  '/demo/goods/trial',
                'fatherName'    =>  '申请审核'
            ],
            '10'     => [
                'name'          =>  '添加用户',
                'href'          =>  '/demo/index/userInsert',
                'fatherName'    =>  '用户管理'
            ],
            '11'     => [
                'name'          =>  '查看角色',
                'href'          =>  '/demo/role/allRole',
                'fatherName'    =>  '用户管理'
            ],
    );
        return $main;
    }


    protected function nowPage($total, $pagesize, $page)
    {
        //初始化分页类
        $pageHandler = new Page($total, $pagesize, $page,3);
        //获取分页数据
        $pageData = $pageHandler->getPageData(DEFAULT_PAGE_STYLE,true);
        //组合分页HTML代码
        if ( $pageData ) {
            $pagemenu = '<ul class="pagination">';
            $pagemenu .= '<li><a href="'.$pageData['prev'].'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
            foreach ( $pageData['list'] as $key => $value ) {
                if ( $key == $page ) {
                    $pagemenu .= '<li class="active"><a href="#fakelink">'.$key.'</a></li> ';
                } else {
                    $pagemenu .= '<li><a href="'.$value.'">'.$key.'</a></li> ';
                }
            }
            $pagemenu .= '<li><a href="'.$pageData['next'].'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
            $pagemenu .= '</ul>';
        }
        return $pagemenu;
    }



}