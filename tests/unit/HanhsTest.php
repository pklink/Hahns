<?php

class HanhsTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    /**
     * @var \Hahns\Hahns
     */
    protected $instance;

    protected function _before()
    {
        $this->instance = new \Hahns\Hahns();
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    public function testDelete()
    {
        $this->instance->delete('delete', function() {});
    }

    public function testGet()
    {
        $this->instance->get('get', function() {});
    }


    public function testNotFound()
    {
        $this->instance->on(\Hahns\Hahns::EVENT_NOT_FOUND, function() {});
    }

    public function testPatch()
    {
        $this->instance->patch('patch', function() {});
    }

    public function testPost()
    {
        $this->instance->post('post', function() {});
    }

    public function testRun()
    {
        $this->instance->run();
    }

    public function testService()
    {
        $this->instance->service('bla', function() {});
    }

    public function testConstructor()
    {
        new \Hahns\Hahns();
    }

    public function testParameter()
    {
        $this->instance->parameter('type', function () {});

        try {
            $this->instance->parameter([], function () {});
            $this->fail();
        } catch (\Hahns\Exception\ParameterMustBeAStringException $e) { }

        try {
            $this->instance->parameter('type', 'invalid');
            $this->fail();
        } catch (ErrorException $e) { }
    }

    public function testServices()
    {
        $this->assertInstanceOf('\\Hahns\\Services', $this->instance->services());
    }

    public function testConfig()
    {
        $this->assertInstanceOf('\\Hahns\\Config', $this->instance->config());
        $this->instance->config('clif', 'blabla');
        $this->assertEquals('blabla', $this->instance->config('clif'));
    }

    public function testOn()
    {
        $this->instance->on(213, function() {});

        try {
            $this->instance->on('2', function() {});
            $this->fail();
        } catch (\Hahns\Exception\ParameterMustBeAnIntegerException $e) { }

        try {
            $this->instance->on(21, '');
            $this->fail()
;        } catch (ErrorException $e) { }
    }


    public function testAny()
    {
        $this->instance->any('any', function() {});
    }

}