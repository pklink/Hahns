<?php

class HahnsTest extends \Codeception\TestCase\Test
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
        $config = [
            'clif' => 'blabla'
        ];

        $this->instance = new \Hahns\Hahns($config);
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
        $app = new \Hahns\Hahns();

        $app->run();
        $app->run('asdas');
        $app->run('asdas', 'asdas');

        try {
            $app->run([]);
            $this->fail();
        } catch (\Hahns\Exception\VariableHasToBeAStringOrNullException $e) { }

        try {
            $app->run('asd', []);
            $this->fail();
        } catch (\Hahns\Exception\VariableHasToBeAStringOrNullException $e) { }

        $app->get('/test', function(\Hahns\Hahns $p) {
            $p->config('test', 'test');
        });
        $app->run('/test');
        $this->assertEquals('test', $app->config('test'));

        $app->post('/test', function(\Hahns\Hahns $p) {
            $p->config('test', 'test2');
        });
        $app->run('/test', 'post');
        $this->assertEquals('test2', $app->config('test'));
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
        } catch (\Hahns\Exception\VariableHasToBeAStringException $e) { }

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
        } catch (\Hahns\Exception\VariableHasToBeAStringException $e) { }

        try {
            $this->instance->parameter('type', 'invalid');
            $this->fail();
        } catch (ErrorException $e) { }

        // with app as parameter. parameterparameter roflcopter
        $this->instance->get('/bla', function (\Hahns\Hahns $app) {
            $app->config('roflcopter', '111');
        });
        $this->instance->run('/bla', 'get');
        $this->assertEquals('111', $this->instance->config('roflcopter'));

        // singleton test
        $hahns = new \Hahns\Hahns();
        $hahns->parameter('\\IsSingleton', function() {
            return new IsSingleton();
        });

        $hahns->parameter('\\IsNotSingleton', function() {
            return new IsNotSingleton();
        }, false);

        $hahns->get('/singleton', function(IsSingleton $p, \Hahns\Hahns $app) {
            $app->config('test', $p->test);
        });
        $hahns->run('/singleton', 'get');
        $test = $hahns->config('test');
        $hahns->run('/singleton', 'get');
        $this->assertEquals($test, $hahns->config('test'));

        $hahns->get('/is/not/singleton', function(IsNotSingleton $p, \Hahns\Hahns $app) {
            $app->config('test', $p->test);
        });
        $hahns->run('/is/not/singleton', 'get');
        $test = $hahns->config('test');
        $hahns->run('/is/not/singleton', 'get');
        $this->assertNotEquals($test, $hahns->config('test'));
    }

    public function testConfig()
    {
        $this->assertEquals('blabla', $this->instance->config('clif'));

        try {
            $this->instance->config([]);
            $this->fail();
        } catch (InvalidArgumentException $e) { }
    }

    public function testOn()
    {
        $this->instance->on(213, function() {});

        try {
            $this->instance->on('2', function() {});
            $this->fail();
        } catch (\Hahns\Exception\VariableHasToBeAnIntegerException $e) { }

        try {
            $this->instance->on(21, '');
            $this->fail()
;        } catch (ErrorException $e) { }
    }


    public function testAny()
    {
        $this->instance->any('any', function() {});
    }

    public function testBuiltInServices()
    {
        $app = new \Hahns\Hahns();
        $this->assertInstanceOf('\\Hahns\\Response\\Html', $app->service('html-response'));
        $this->assertInstanceOf('\\Hahns\\Response\\Json', $app->service('json-response'));
        $this->assertInstanceOf('\\Hahns\\Response\\Text', $app->service('text-response'));
    }

}