<?php
/**
 * 单元测试类
 * @author yangjian<yangjian102621@gmail.com>
 * @date 2017-06-20
 */

namespace app\demo\tests;
use app\demo\service\DemoService;
use app\demo\service\UserService;
use herosphp\core\Loader;
use herosphp\http\HttpClient;

class DemoServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DemoService
     */
    protected $service;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->service = new DemoService();
    }

    /**
     * @test
     */
    public function hello() {
        $this->assertTrue($this->service->hello());
    }

    /**
     * @test
     */
    public function findOne() {
        $model = Loader::service(UserService::class);
        print_r($model->findOne());
    }
}
