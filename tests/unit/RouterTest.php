<?php
use Codeception\Util\Stub;

class RouterTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    /**
     * @var \Hahns\Router
     */
    protected $instance;

    public function _before()
    {
        $this->instance = new \Hahns\Router('/hello');
    }

    public function testInvalidAdd()
    {
        try {
            $this->instance->add([], 'valid', function() {});
            $this->fail();
        } catch (\Hahns\Exception\VerbMustBeAStringException $e) {
            $this->assertTrue(true);
        }

        try {
            $this->instance->add('valid', [], function() {});
            $this->fail();
        } catch (\Hahns\Exception\RouteMustBeAStringException $e) {
            $this->assertTrue(true);
        }

        try {
            $this->instance->add('bla', 'bla', 'bla');
            $this->fail();
        } catch (ErrorException $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetCallback()
    {
        try {
            $this->instance->getCallback();
            $this->fail();
        } catch (\Hahns\Exception\CallbackDoesNotExistException $e) {
            $this->assertTrue(true);
        }

        // dispatch invalid route
        try {
            $this->instance->dispatch();
        } catch (Exception $e) { }

        try {
            $this->instance->getCallback();
            $this->fail();
        } catch (\Hahns\Exception\CallbackDoesNotExistException $e) {
            $this->assertTrue(true);
        }

        // dispatch valid route
        $this->instance->add('GET', '/hello', function () {
            return '1';
        });
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->instance->dispatch();
        $this->assertInstanceOf('\\Closure', $this->instance->getCallback());
    }

    public function testGetNamedParameter()
    {
        $instance = new \Hahns\Router('/hello/peter');
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // before dispatch
        $this->assertEquals([], $instance->getNamedParameters());

        // after failed dispatch
        try {
            $instance->dispatch();
        } catch (\Hahns\Exception\RouteNotFoundException $e) { }

        $this->assertEquals([], $instance->getNamedParameters());

        // after successfully dispatch
        $instance->add('GET', '/hello/[.+:name]', function () {});
        $instance->dispatch();
        $this->assertEquals(['name' => 'peter'], $instance->getNamedParameters());

        // add additional route callback (must be ignored) and test again
        $instance->add('GET', '/hello/[.+:blah]', function () {});
        $instance->dispatch();
        $this->assertEquals(['name' => 'peter'], $instance->getNamedParameters());
    }


    public function testConstructor()
    {
        // default
        $_SERVER['PATH_INFO'] = '/blahdong';
        $instance = new \Hahns\Router();
        $this->assertEquals('/blahdong', $instance->getParsable());

        // custom
        $instance = new \Hahns\Router('/ding');
        $this->assertEquals('/ding', $instance->getParsable());

        // PATH_INFO is not available
        unset($_SERVER['PATH_INFO']);
        $instance = new \Hahns\Router();
        $this->assertEquals('', $instance->getParsable());
    }

}