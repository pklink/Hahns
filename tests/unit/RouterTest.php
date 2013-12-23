<?php

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
        $this->instance = new \Hahns\Router();
    }

    public function testInvalidAdd()
    {
        try {
            $this->instance->add([], function() {});
            $this->fail();
        } catch (\Hahns\Exception\ParameterMustBeAStringException $e) { }

        try {
            $this->instance->add('bla', 'bla');
            $this->fail();
        } catch (ErrorException $e) { }
    }

    public function testGetCallback()
    {
        try {
            $this->instance->getCallback();
            $this->fail();
        } catch (\Hahns\Exception\CallbackDoesNotExistException $e) { }

        // dispatch invalid route
        try {
            $this->instance->dispatch([]);
        } catch (\Hahns\Exception\ParameterMustBeAStringException $e) { }

        try {
            $this->instance->getCallback();
            $this->fail();
        } catch (\Hahns\Exception\CallbackDoesNotExistException $e) { }

        // dispatch valid route
        $this->instance->add('/hello', function () {
            return '1';
        });
        $this->instance->dispatch('/hello');
        $this->assertInstanceOf('\\Closure', $this->instance->getCallback());
    }

    public function testDispatch()
    {
        // simple
        $this->instance->add('/blah', function () { });
        $this->instance->dispatch('/blah');

        try {
            $this->instance->dispatch('blah');
            $this->fail();
        } catch (\Hahns\Exception\RouteNotFoundException $e) { }

        // named any parameter
        $this->instance->add('/blah/[.+:blub]', function() {});
        $this->instance->dispatch('/blah/asdsada');
        $this->instance->dispatch('/blah/123s');

        try {
            $this->instance->dispatch('/int/asda/');
            $this->fail();
        } catch (\Hahns\Exception\RouteNotFoundException $e) { }

        // named int parameter
        $this->instance->add('/int/[\d+:blub]', function() {});
        $this->instance->dispatch('/int/123');

        try {
            $this->instance->dispatch('/int/asda');
            $this->fail();
        } catch (\Hahns\Exception\RouteNotFoundException $e) { }

        // named int parameters
        $this->instance->add('/int/[\d+:blub]/[\d+:blah]', function() {});
        $this->instance->dispatch('/int/123/241');

        try {
            $this->instance->dispatch('/int/asda/d23g');
            $this->fail();
        } catch (\Hahns\Exception\RouteNotFoundException $e) { }

        // complex named parameters
        $this->instance->add('/complex/----[\d+:blub]hallo/', function() {});
        $this->instance->dispatch('/complex/----2131231hallo/');

        try {
            $this->instance->dispatch('/complex/---2131231hallo/');
            $this->fail();
        } catch (\Hahns\Exception\RouteNotFoundException $e) { }
    }

    public function testGetNamedParameter()
    {
        $instance = $this->instance;

        // before dispatch
        $this->assertEquals([], $instance->getNamedParameters());

        // after failed dispatch
        try {
            $instance->dispatch('');
        } catch (\Hahns\Exception\RouteNotFoundException $e) { }

        $this->assertEquals([], $instance->getNamedParameters());

        // after successfully dispatch
        $instance->add('/hello/[.+:name]', function () {});
        $instance->dispatch('/hello/peter');
        $this->assertEquals(['name' => 'peter'], $instance->getNamedParameters());

        // add additional route callback (must be ignored) and test again
        $instance->add('/hello/[.+:blah]', function () {});
        $instance->dispatch('/hello/peter');
        $this->assertEquals(['name' => 'peter'], $instance->getNamedParameters());
    }

}