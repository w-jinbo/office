<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-29
 * Time: 下午4:08
 */

namespace app\demo\service;


use app\demo\dao\UserDao;
use herosphp\core\Loader;
use herosphp\model\CommonService;
use herosphp\filter\Filter;

class RoomSevice extends CommonService
{
    protected $modelClassName = 'app\demo\dao\RoomDao';

    /***
     * 判断某时间段内的办公室是否已有申请
     */
    public function first($fromDate)
    {
        $_modelDao = $this->modelDao;
        $isroom = $this->modelDao->where("roomname",$fromDate['roomname'])
            ->where("roomdate",'=',$fromDate['roomdate'])
            ->where("roomtime",$fromDate['roomtime'])
            ->where(function($_modelDao) use($_modelDao) {
                $this->modelDao->where("state",  "0")->whereOr("state", '1');
            })->find();

        $userIsRoom = $this->modelDao->where("roomdate",$fromDate['roomdate'])
            ->where("userID",$_SESSION['userID'])
            ->where(function($_modelDao) use($_modelDao) {
                $this->modelDao->where("state",  "0")->whereOr("state", '1');
            })->find();

        if ( $isroom )   {
            return '该办公室在该时间段已有人申请,请申请别的时间段或别的办公室';
        }   elseif ( $userIsRoom )    {
            return '该日期,您已申请办公室.请查看已申请的办公室';
        }   else    {
            $flite = $this->filterRoom($fromDate);
            return $flite;
        }
    }

    /**
    *   过滤数据
     * @param array() fromData
     * @return string
     **/
    private function filterRoom($fromDate)
    {
        $filterMap = array(

            'userID' => array(Filter::DFILTER_NUMERIC, NULL,Filter::DFILTER_SANITIZE_TRIM,[ "require" => "用户ID不能为空."]),

            'roomname' => array(Filter::DFILTER_NUMERIC, NULL,Filter::DFILTER_SANITIZE_TRIM, [ "require" => "申请办公室名称不能为空."]),

            'roomdate' => array(Filter::DFILTER_STRING, NULL,Filter::DFILTER_SANITIZE_TRIM, [ "require" => "申请日期不能为空."]),

            'roomtime' => array(Filter::DFILTER_NUMERIC, NULL,Filter::DFILTER_SANITIZE_TRIM, [ "require" => "申请时间段不能为空."]),

            'reason' => array(Filter::DFILTER_STRING, [ 3, 300 ], Filter::DFILTER_SANITIZE_HTML|Filter::DFILTER_SANITIZE_TRIM|Filter::DFILTER_MAGIC_QUOTES, [ "require" => "申请理由不能为空", "length" => "长度必需在3-250之间."])
        );

        $data = Filter::loadFromModel($fromDate,$filterMap,$error);
        return $data ? $this->_insertRoom($fromDate) : $error ;
    }

    /**
     * 添加过滤后的数据
     * @param array() fromData
     * @return string
    */
    private function _insertRoom($fromDate)
    {
        $fromDate['datetime'] = date("Y-m-d H:i:s");
        $this->modelDao->add($fromDate);
        return "申请成功";
    }


    /**
     * 查看个人已申请的办公室
     */
    public function selectRooms($page,$size)
    {
        $roomData = $this->modelDao->where('userID',$_SESSION['userID'])->order('id desc')->page($page,$size)->find();
        return $roomData;
    }

    public function allCountID()
    {
        $count = $this->modelDao->where('userID',$_SESSION['userID'])->count();
        return $count;
    }

    public function allCount()
    {
        $count = $this->modelDao->where('state','!=','3')->count();
        return $count;
    }


    /***
     * 用户取消办公室申请
    */
    public function roomsRemove($id)
    {
        $idState = $this->fields('state')->findById($id);
        if ( $idState <> '3')
        {
            $this->modelDao->set('state','3',$id);
        }
        return '该假期申请已取消';
    }

    /**
     * 所有办公室申请数据,审核状态除了员工自己取消申请
     * @return array
     */
    public function roomAll($page,$pageSize)
    {
        $data = $this->modelDao->where('state','!=','3')->page($page,$pageSize)->find();
        return $data;
    }
    /**
     * 员工申请办公室详情
     */
    public function roomOne($id)
    {
        $data      = $this->modelDao->where('id',$id)->findOne();
        $data['roomname'] = RoomSevice::_roomName($data['roomname']);
        $data['roomtime'] = RoomSevice::_roomTime($data['roomtime']);
        $data['stateName'] = RoomSevice::_roomState($data['state']);
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
    private static function _roomName($roomname){
        $rname = [
            "1" =>  '办公室1',
            "2" =>  '办公室2',
            "3" =>  '办公室3'
        ];
        foreach ( $rname as $k => $v)
        {
            if ($roomname == $k){   return $v;  }
        }
    }

    private static function _roomTime($roomtime){
        $rtime = [
            "1" =>  '8:30-9:30',
            "2" =>  '9:40-10:40',
            "3" =>  '9:50-11:50',
            "4" =>  '13:40-14:40',
            "5" =>  '14:50-15:50',
            "6" =>  '16:00-17:00',
            "7" =>  '17:10-17:30'
        ];
        foreach ( $rtime as $k => $v)
        {
            if ($roomtime == $k){   return $v;  }
        }
    }
    private static function _roomState($state){
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
     */
    public function isState($gets)
    {
        $data = $this->modelDao->findById($gets['roomID']);
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
        $to = $this->modelDao->set('state',$state,$gets['roomID']);
        return $to ? '操作成功' : '请重新选择';
    }





}