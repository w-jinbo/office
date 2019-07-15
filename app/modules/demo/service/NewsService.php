<?php
/**
 * Created by PhpStorm.
 * User: mo
 * Date: 17-8-30
 * Time: 下午4:04
 */

namespace app\demo\service;


use herosphp\cache\CacheFactory;
use herosphp\filter\Filter;
use herosphp\model\CommonService;

class NewsService extends CommonService
{
    /**
     * 模型类名称
     * @var string
     */
    protected $modelClassName= 'app\demo\dao\NewsDao';

    /**
     * 显示所有公示
    */
    public function allNews($page,$size)
    {
        $all = $this->modelDao->page($page,$size)->find();
        return $all;
    }

    public function allCount()
    {
       $count = $this->modelDao->count();
       return $count;
    }
    /**
     * 过滤数据
     * @param array() fromData
     * @return string
     */
    public function filterNew($fromData)
    {
        $filterMap = array(
            'newtitle' => array(Filter::DFILTER_STRING, NULL, Filter::DFILTER_SANITIZE_TRIM|Filter::DFILTER_MAGIC_QUOTES, [ "require" => "公示标题不能为空."]),
            'newcontent' => array(Filter::DFILTER_STRING, NUll, Filter::DFILTER_SANITIZE_HTML|Filter::DFILTER_SANITIZE_TRIM|Filter::DFILTER_MAGIC_QUOTES, [ "require" => "公示内容不能为空" ])
        );

        $data = Filter::loadFromModel($fromData,$filterMap,$error);

        return $data ? $this->_insertDao($fromData) : $error ;
    }

    private function _insertDao($fromData)
    {
        $fromData['datetime'] = date("Y-m-d H:i:s");
        $this->modelDao->add($fromData);
        return "发布成功";
    }

    public function selectOne($id)
    {
//        $CACHER =  CacheFactory::create('memo');
//        $title = 'title'.$id;
//        $titleData = $CACHER->get($title);
//
//        $datetime = 'datetime'.$id;
//        $time = $CACHER->get($datetime);
//
//        $content = 'content'.$id;
//        $contentDate = $CACHER->get($content);
//
//        if ( !$titleData && !$time && !$contentDate)
//        {
//            $idData =  $this->modelDao->findById($id);
//            $CACHER->set('title'.$id,$idData['newtitle']);
//            $CACHER->set('datetime'.$id,$idData['datetime']);
//            $CACHER->set('content'.$id,$idData['newcontent']);
//            return $idData;
//        }
//        $arr = [
//            'newtitle'  =>  $titleData,
//            'datetime'  =>  $time,
//            'newcontent'  =>  $contentDate
//        ];
//        return $arr;

        $idData =  $this->modelDao->findById($id);

        return $idData;

    }






}