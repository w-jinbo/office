<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-30
 * Time: 下午2:40
 */

namespace app\demo\service;


use app\demo\dao\UserDao;
use herosphp\core\Loader;
use herosphp\filter\Filter;
use herosphp\model\CommonService;

class GoodsService extends CommonService
{
    /**
     * 模型类名称
     * @var string
     */
    protected $modelClassName= 'app\demo\dao\GoodsDao';

    /**
     * 判断员工是否已申请物品领用
    */
    public function firstSelect($get)
    {
        $isGoods = $this->modelDao->where('state','0')
            ->where('userID',$_SESSION['userID'])->findOne();
        if ( $isGoods )
        {
            return "您已申请物品领用,请速速前往前台领取";
        }   else    {
            return $this->_insert($get);
        }
    }

    private  function _insert($get)
    {
        $_string = implode(',',$get);
        $filter = array(
            'goodsname' => array(Filter::DFILTER_STRING, NULL, Filter::DFILTER_SANITIZE_TRIM, [ "require" => "领取物品不能为空"])
        );

        $map = [
            'userID'    =>  $_SESSION['userID'],
            'datetime'  =>  date('Y-m-d H:i:s'),
            'goodsname' =>  $_string
        ];
        $data = Filter::loadFromModel($map,$filter,$error);
        if ( !$data )   {   return $error;    }
        $this->modelDao->add($map);
        return "您已申请物品领用,请速速前往前台领取";
    }

    /**
     * 物品领用数据
     * @return array
     */
    public function goodsAll($page,$pageSize)
    {
        $data = $this->modelDao->page($page,$pageSize)->find();
        return $data;
    }

    public function goodsID($page,$size)
    {
        $data = $this->modelDao->where('userID',$_SESSION['userID'])->page($page,$size)->find();
        foreach ( $data as $k => $v )
        {
            $data[$k]['goodsname'] = GoodsService::_goodsname($v['goodsname']);
            $data[$k]['stateName'] = GoodsService::_goodsState($v['state']);
        }
        return $data;
    }

    public function allCountID()
    {
        $count = $this->modelDao->where('userID',$_SESSION['userID'])->count();
        return $count;
    }

    public function allCount()
    {
        $count = $this->modelDao->count();
        return $count;
    }

    /**
     * 员工申请物品领用详情
     */
    public function goodsOne($id)
    {
        $data      = $this->modelDao->where('id',$id)->findOne();
        $data['stateName'] = GoodsService::_goodsState($data['state']);
        $data['goodsname'] = GoodsService::_goodsname($data['goodsname']);
        return $data;
    }

    public function userOne($userID)
    {
        $userDao   = Loader::model(UserDao::class);
        $data      = $userDao->findById($userID);
        return $data;
    }

    /**
     * 判断这条数据有没有给审核了
     * @param string $id
     */
    public function isState($gets)
    {
        $data = $this->modelDao->findById($gets['goodsID']);
        if ($data['state'] != '0'){ return "该申请已审核";  }
        $to = $this->_isState($gets);
        return $to;
    }

    /**
     * 普通管理员 反馈数据
     */
    private function _isState($gets)
    {
        $state =  $gets['state'][0] == '同意' ? '1' : '2';
        $this->modelDao->set('state',$state,$gets['goodsID']);
        return '操作成功';
    }

    /**
     * 处理数据
     * */
    private static function _goodsState($state)
    {
        $rState = [
            '0' =>  '申请中',
            "1" =>  '同意申请',
            "2" =>  '拒绝申请'
        ];
        foreach ( $rState as $k => $v)
        {
            if ($state == $k){
                return $v;
            }
        }

    }
    private static function _goodsname($goodsname){
        $_arr = explode(',',$goodsname);
        $gName = [
            '1' =>  '普通笔记本',
            "2" =>  '黑色签字笔',
            "3" =>  '透明胶',
            '4' =>  '订书机',
            "5" =>  '资料夹'
        ];
        foreach ( $gName as $k => $v)
        {
            foreach ( $_arr as $vv)
            {
                if ($vv == $k)
                {
                    $newArr[] = $v;
                }
            }
        }
        $now = implode(',',$newArr);
        return $now;
    }




}