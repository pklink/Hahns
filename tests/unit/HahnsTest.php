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

    /**
     * @return void
     */
    protected function _before()
    {
        // create configuration
        $config = [
            'clif' => 'blabla'
        ];

        // create instance of Hahns
        $this->instance = new \Hahns\Hahns($config);

        // simulate the request
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    /**
     * test DELETE-routing
     */
    public function testDelete()
    {
        // set DELETE route `delete`
        $this->instance->delete('delete', function() {});
    }

    /**
     * test GET-routing
     */
    public function testGet()
    {
        // set GET route `get`
        $this->instance->get('get', function() {});
    }

    /**
     * test handlich of notFound event
     */
    public function testNotFound()
    {
        $this->instance->on(\Hahns\Hahns::EVENT_NOT_FOUND, function() {});
    }

    /**
     * test PATCH-routing
     */
    public function testPatch()
    {
        $this->instance->patch('patch', function() {});
    }

    /**
     * test POST-routing
     */
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

        // invalid route
        try {
            $app->run([]);
            $this->fail();
        } catch (InvalidArgumentException $e) { }

        // invalid request method
        try {
            $app->run('asd', []);
            $this->fail();
        } catch (InvalidArgumentException $e) { }

        // check if routing argument is working
        $tmp = '';
        $app->get('/test', function() use (&$tmp) {
            $tmp = 'test';
        });
        $app->run('/test');
        $this->assertEquals('test', $tmp);

        // check if requestMethod argument is working
        $tmp = '';
        $app->post('/test', function() use (&$tmp) {
            $tmp = 'test2';
        });
        $app->run('/test', 'post');
        $this->assertEquals('test2', $tmp);
    }

    public function testService()
    {
        // check if registration of service is working
        $this->instance->service('bla', function() {
            $x = new stdClass();
            $x->test = 'blub';
            return $x;
        });
        $service = $this->instance->service('bla');
        $this->assertInstanceOf('stdClass', $service);
        $this->assertEquals('blub', $service->test);

        // check if Hahns will throw an exception on getting a non-existing service
        try {
            $this->instance->service('asdasd');
            $this->fail();
        } catch (\Hahns\Exception\ServiceDoesNotExistException $e) { }

        // check if Hahns will throw an exception on getting a service with an invalid name
        try {
            $this->instance->service([]);
            $this->fail();
        } catch (InvalidArgumentException $e) { }

        // register invalid service
        try {
            $this->instance->service('asdas', 'asdas');
            $this->fail();
        } catch (\ErrorException $e) { }
    }

    public function testConstructor()
    {
        new \Hahns\Hahns();
        new \Hahns\Hahns([]);

        // invalid configuration
        try {
            new \Hahns\Hahns('asd');
            $this->fail();
        } catch (InvalidArgumentException $e) { }

        try {
            new \Hahns\Hahns(1);
            $this->fail();
        } catch (InvalidArgumentException $e) { }

        try {
            new \Hahns\Hahns(new stdClass());
            $this->fail();
        } catch (InvalidArgumentException $e) { }
    }

    public function testParameter()
    {
        // register parameter
        $this->instance->parameter('type', function () {});

        // register parameter with invalid type
        try {
            $this->instance->parameter([], function () {});
            $this->fail();
        } catch (InvalidArgumentException $e) { }

        // register invalid parameter
        try {
            $this->instance->parameter('type', 'invalid');
            $this->fail();
        } catch (ErrorException $e) { }

        // with app as parameter
        $tmp = '';
        $this->instance->get('/bla', function (\Hahns\Hahns $app) use (&$tmp) {
            $tmp = $app;
        });
        $this->instance->run('/bla', 'get');
        $this->assertInstanceOf('\Hahns\Hahns', $tmp);

        // register parameter as singleton (default)
        $hahns = new \Hahns\Hahns();
        $hahns->parameter('\\IsSingleton', function() {
            return new IsSingleton();
        });

        // regiser parameter as non-singleton
        $hahns->parameter('\\IsNotSingleton', function() {
            return new IsNotSingleton();
        }, false);

        // test singleton parameter
        $tmp = '';
        $hahns->get('/singleton', function(IsSingleton $p) use ($tmp) {
            $tmp = $p->test;
        });
        $hahns->run('/singleton', 'get');
        $test = $tmp;
        $hahns->run('/singleton', 'get');
        $this->assertEquals($test, $tmp);

        // test non-singleton parameter
        $tmp = '';
        $hahns->get('/is/not/singleton', function(IsNotSingleton $p) use (&$tmp) {
            $tmp = $p->test;
        });
        $hahns->run('/is/not/singleton', 'get');
        $test = $tmp;
        $hahns->run('/is/not/singleton', 'get');
        $this->assertNotEquals($test, $tmp);
    }

    public function testConfig()
    {
        $this->assertEquals('blabla', $this->instance->config('clif'));

        // illegal config-key
        try {
            $this->instance->config([]);
            $this->fail();
        } catch (InvalidArgumentException $e) { }
    }

    public function testOn()
    {
        // register handler for event 213
        $this->instance->on(213, function() {});

        // invalid event
        try {
            $this->instance->on('2', function() {});
            $this->fail();
        } catch (InvalidArgumentException $e) { }

        // register invalid event handler
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