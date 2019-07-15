<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-29
 * Time: 上午11:26
 */

namespace app\demo\service;


use app\demo\dao\UserDao;
use herosphp\core\Loader;
use herosphp\filter\Filter;
use herosphp\model\CommonService;

class VacationService extends CommonService
{
    protected $modelClassName = "app\demo\dao\VacationDao";

    /***
     * 判断是否有尚未审核的订单
     * 本打算想着申请假期订单未结束前不能继续申请
     * 改成可以申请
    */
//    public function first($fromData)
//    {
//        $isVaction = $this->modelDao->where('userID',$fromData['userID'])->where('state','!=','1')->find();
//        if ( $isVaction )   {
//            return'该用户目前尚有尚未审核的假期申请.请查看已申请假期';
//        }   else    {
//            $this->filterVaction($fromData);
//        }
//    }

    /**
     * 过滤数据
     * @param array() fromData
     * @return string
    */
    public function filterVaction($fromData)
    {
        $fromData['starttime'] = $fromData['sdate'].' '.$fromData['stime'].':00';
        $fromData['endtime'] = $fromData['sdate'].' '.$fromData['stime'].':00';
        array_splice($fromData,2,4);
        $filterMap = array(
            'userID' => array(Filter::DFILTER_NUMERIC, [ 1, 10 ],Filter::DFILTER_SANITIZE_TRIM,[ "require" => "用户ID不能为空."]),

            'type' => array(Filter::DFILTER_NUMERIC, [ 1, 5],Filter::DFILTER_SANITIZE_TRIM, [ "require" => "请假类别不能为空."]),

            'reason' => array(Filter::DFILTER_STRING, [ 3, 300 ], Filter::DFILTER_SANITIZE_HTML|Filter::DFILTER_SANITIZE_TRIM|Filter::DFILTER_MAGIC_QUOTES, [ "require" => "请假理由不能为空", "length" => "请假理由长度必需在3-300之间."]),

            'starttime' => array(Filter::DFILTER_STRING, [ 18,30 ], Filter::DFILTER_SANITIZE_TRIM, [ "require" => "请输入正确的开始日期格式."]),

            'endtime' => array(Filter::DFILTER_STRING, NULL, Filter::DFILTER_SANITIZE_TRIM, [ "require" => "请输入正确的结束日期格式."])
        );
        $data = Filter::loadFromModel($fromData,$filterMap,$error);
        return $data ? $this->_insertVacation($fromData) : $error ;

    }

    /**
     * 添加过滤后的数据
     * @param array() fromData
     * @return string
    */
    private function _insertVacation($fromData)
    {
        $fromData['nowtime'] = date('Y-m-d H:i:s');
        $this->modelDao->add($fromData);
        return "申请成功";
    }



    public function selectOne($page,$size)
    {
        $userData = $this->modelDao->where('userID',$_SESSION['userID'])->order('id desc')->page($page,$size)->find();
        return $userData;
    }

    public function allCount()
    {
        $count = $this->modelDao->where('userID',$_SESSION['userID'])->count();
        return $count;
    }

    /**
     * 取消申请
     * @param string $id
     * @return string
    */
    public function vacationRemove($id)
    {
        $idState = $this->fields('state')->findById($id);
        if ( $idState <> '3')
        {
            $this->modelDao->set('state','3',$id);
        }
        return '该假期申请已取消';
    }

    /**
     * 所有申请假期数据,审核状态除了员工自己取消申请
     * @return array
    */
    public function vacationAll($page,$pageSize)
    {
        $data = $this->modelDao->where('state','!=','3')->page($page,$pageSize)->find();
        return $data;
    }


    /**
     * 员工申请假期详情
     * @param string $id
     * @return array
    */
    public function vacationOne($id)
    {
        $data      = $this->modelDao->where('id',$id)->findOne();
        $data['type']   = VacationService::_vacationType($data['type']);
        $data['stateName']  = VacationService::_vacationState($data['state']);
        return $data;
    }

    public function userOne($userID)
    {
        $userDao   = Loader::model(UserDao::class);
        $data      = $userDao->findById($userID);
        return $data;
    }
    /**
     * 过滤为人能看的数据
     */
    private static function _vacationType($type){
        $rname = [
            "1" =>  '事假',
            "2" =>  '年休假',
            "3" =>  '婚假',
            "4" =>  '病假'
        ];
        foreach ( $rname as $k => $v)
        {
            if ($type == $k){   return $v;  }
        }
    }

    private static function _vacationState($state){
        $rState = [
            '0' =>  '申请中',
            "1" =>  '同意申请',
            "2" =>  '拒绝申请',
            "3" =>  '取消申请'
        ];
        foreach ( $rState as $k => $v)
        {
            if ($state == $k){   return $v;  }
        }
    }

    /**
     * 判断这条数据有没有给审核了
     * @param string $id
     * @return string
     */
    public function isFeedback($gets)
    {
        $data = $this->modelDao->findById($gets['vacationID']);
        if ($data['state'] != '0'){ return "该申请已审核";  }
        $to = $this->_feedback($gets);
        return $to;
    }

    /**
     * 超级管理员反馈数据过滤
     * @param array $gets
     * @return string
    */
    private function _feedback($gets)
    {
        $state =  $gets['state'][0] == '同意' ? '1' : '2';
        $filter = array(
            'feedback' => array(Filter::DFILTER_STRING, [ 3, 300 ], Filter::DFILTER_SANITIZE_HTML|Filter::DFILTER_SANITIZE_TRIM|Filter::DFILTER_MAGIC_QUOTES, [ "require" => "反馈不能为空", "length" => "长度必需在3-300之间."])
        );
        $map = [
            'id'    =>  $gets['vacationID'],
            'state' =>  $state,
            'feedback' => $gets['feedback']
        ];
        $data = Filter::loadFromModel($map,$filter,$error);
        if ( !$data )   {   return $error;    }
        $to = $this->modelDao->update($map,$map['id']);
        return $to ? '操作成功' : '请重新选择';
    }




}