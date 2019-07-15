<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-9-1
 * Time: 下午5:10
 */

namespace app\demo\service;


use app\demo\dao\RauthorityDao;
use herosphp\core\Loader;
use herosphp\filter\Filter;
use herosphp\model\CommonService;

class RoleService extends CommonService
{
    /**
     * 模型类名称
     * @var string
     */
    protected $modelClassName= 'app\demo\dao\RoleDao';

    /**
     *所有角色
    */
    public function role()
    {
        $all = $this->modelDao->find();
        return $all;
    }

    /**
     * 单用户资料
    */
    public function roleDate($id)
    {
        $all = $this->modelDao->findById($id);
        return $all;
    }
    public function authID($id)
    {
        $authDao = Loader::model(RauthorityDao::class);
        $authData = $authDao->where('roleID',$id)->findOne();
        $authData =  RoleService::_auth($authData['authority']);
        return $authData;
    }
    private  function _auth($authority)
    {
        $arr = explode(',',$authority);
        array_pop($arr);
        return $arr;
    }
    /**
     *所有用户,分页
     * */
    public function allRole($page,$pageSize)
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

    /**
     * 权限表
     * @return array
    */
    public function authority()
    {
        $authDao = Loader::model(RauthorityDao::class);
        $authData = $authDao->find();
        return $authData;
    }

    public function handle($auth,$main)
    {
        $mate = $this->_mate($auth);
        //菜单
        foreach ( $main as $k => $v )
        {
            //权限ID,二维数组
            foreach ($mate as $kk => $vv)
            {
                foreach ($vv as $kkk =>$vvv)
                {
                    if ($k == $kkk)
                    {
                        $mate[$kk][$kkk] = $v['name'];
                    }
                }
            }
        }
        return $mate;
    }

    /**
     * 分割大法
     * 字符串转数组,key/value 转换
     * @param array
     * @return array[key]
    */
    private function _mate($auth)
    {
        $newArr = [];
        foreach (   $auth as $k => $v  )
        {
            $arr = $v['authority'];
            $arr = explode(',',$arr);
            array_pop($arr);
            $arr = array_flip($arr);
            $newArr[$v['roleID']] = $arr;
        }
        return $newArr;

    }

    public function convert($role)
    {
        $arr = [];
        foreach ($role as $k => $v) {
            $arr[$k] = $v['name'] ;
        }
        return $arr;
    }

    /**
     * 判断角色名称有没有给占用
    */
    public function isAddRole($gets)
    {
        $isRole = $this->modelDao->where('roleName',$gets['roleName'])->find();
        if ($isRole) {  return "该角色已有,请重新填写";   }

        if (!$gets['authority']) { return '权限不能为空'; }

        $gets['authority'] = RoleService::_string($gets['authority']);
        $to = $this->roleFilter($gets);
        if (!is_array($to)) {   return $to;}

        $this->modelDao->add($gets);
        $role = $this->modelDao->where('roleName',$gets['roleName'])->findOne();

        $authDao = Loader::model(RauthorityDao::class);
        $data= [ 'roleID' => $role['id'], 'authority' => $gets['authority']];

        $authDao->add($data);

        return '添加成功';
    }

    private static function _string($auth)
    {
        $auth = implode(',',$auth).',';
        return $auth;
    }

    /**
     * 过滤数据
     * */
    public function roleFilter($gets)
    {
        $filterMap = array(
            'roleName' => array(Filter::DFILTER_STRING, [ 2, 15 ],Filter::DFILTER_SANITIZE_TRIM,[ "require" => "角色名字不能为空.", "length" => "名字长度必需在2-15之间." ]),
            'authority' => array(Filter::DFILTER_STRING,NULL,Filter::DFILTER_SANITIZE_TRIM, [ "require" => "权限不能为空."]),
        );
        $to = Filter::loadFromModel($gets,$filterMap,$error);
//        var_dump($gets);
        if ( !$to )   {   return $error;    }
        return $gets;
    }

    /**
     *删除用户
     * */
    public function deleteRole($id)
    {
        $this->modelDao->delete($id);
        $authDao = Loader::model(RauthorityDao::class);
        $authDao->where('roleID',$id)->deletes();
        return '删除成功';
    }

    /**
     *修改用户权限
     * */
    public function updateRole($gets)
    {
        if (!$gets['authority']) { return '权限不能为空'; }

        $gets['authority'] = RoleService::_string($gets['authority']);
        $to = $this->roleFilter($gets);
        if (!is_array($to)) {   return $to;}

        $data['authority'] = $gets['authority'];
        $authDao = Loader::model(RauthorityDao::class);
        $authDao->where('roleID',$gets['roleID'])->updates($data);
        return '修改成功';
    }



}