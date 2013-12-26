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
        $this->instance->service('bla', function() { return new stdClass(); });
        $this->instance->service('bla');

        $this->instance->config('service-test-bla', 'hello wort!');
        $this->instance->service('bla', function(\Hahns\Hahns $app) {
            $o = new stdClass();
            $o->arg = $app->config('service-test-bla');
            return $o;
        });
        $this->assertEquals('hello wort!', $this->instance->service('bla')->arg);

        try {
            $this->instance->service('asdasd');
            $this->fail();
        } catch (\Hahns\Exception\ServiceDoesNotExistException $e) { }

        try {
            $this->instance->service([]);
            $this->fail();
        } catch (\Hahns\Exception\ArgumentMustBeAStringException $e) { }

        try {
            $this->instance->service('asdas', 'asdas');
            $this->fail();
        } catch (\ErrorException $e) { }
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
        } catch (\Hahns\Exception\ArgumentMustBeAStringException $e) { }

        try {
            $this->instance->parameter('type', 'invalid');
            $this->fail();
        } catch (ErrorException $e) { }
    }

    public function testConfig()
    {
        $this->instance->config('clif', 'blabla');
        $this->assertEquals('blabla', $this->instance->config('clif'));

        try {
            $this->instance->config([]);
            $this->fail();
        } catch (\Hahns\Exception\ArgumentMustBeAStringException $e) { }
    }

    public function testOn()
    {
        $this->instance->on(213, function() {});

        try {
            $this->instance->on('2', function() {});
            $this->fail();
        } catch (\Hahns\Exception\ArgumentMustBeAnIntegerException $e) { }

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