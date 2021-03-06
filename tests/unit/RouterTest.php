<?php

use Hahns\Exception\NotFoundException;

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

    public function testAdd()
    {
        // add route
        $this->instance->add('asdas', function(){});

        // add route with name `name`
        $this->instance->add('asdas', function(){}, 'name');

        // add named route
        $this->instance->add('asdas', 'name', 'anothername');

        // add another named route
        $this->instance->add('asdas', 'anothername');

        // add invalid route name
        try {
            $this->instance->add([], function() {});
            $this->fail();
        } catch (InvalidArgumentException $e) { }

        // add non-existing named route
        try {
            $this->instance->add('bla', 'bla');
            $this->fail();
        } catch (\Hahns\Exception\RouteDoesNotExistException $e) { }

        // add route with invalid name
        try {
            $this->instance->add('asdas', function(){}, []);
            $this->fail();
        } catch (InvalidArgumentException $e) { }
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
        } catch (InvalidArgumentException $e) { }

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
        } catch (NotFoundException $e) { }

        // named any parameter
        $this->instance->add('/blah/[.+:blub]', function() {});
        $this->instance->dispatch('/blah/asdsada');
        $this->instance->dispatch('/blah/123s');

        try {
            $this->instance->dispatch('/int/asda/');
            $this->fail();
        } catch (NotFoundException $e) { }

        // named int parameter
        $this->instance->add('/int/[\d+:blub]', function() {});
        $this->instance->dispatch('/int/123');

        try {
            $this->instance->dispatch('/int/asda');
            $this->fail();
        } catch (NotFoundException $e) { }

        // named int parameters
        $this->instance->add('/int/[\d+:blub]/[\d+:blah]', function() {});
        $this->instance->dispatch('/int/123/241');

        try {
            $this->instance->dispatch('/int/asda/d23g');
            $this->fail();
        } catch (NotFoundException $e) { }

        // complex named parameters
        $this->instance->add('/complex/----[\d+:blub]hallo/', function() {});
        $this->instance->dispatch('/complex/----2131231hallo/');

        try {
            $this->instance->dispatch('/complex/---2131231hallo/');
            $this->fail();
        } catch (NotFoundException $e) { }
    }

    public function testGetNamedParameter()
    {
        $instance = $this->instance;

        // before dispatch
        $this->assertEquals([], $instance->getNamedParameters());

        // after failed dispatch
        try {
            $instance->dispatch('');
        } catch (NotFoundException $e) { }

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

    public function testGetRoute()
    {
        $this->instance->add('hello', function(){}, 'route1');
        $this->instance->add('hallo', function(){}, 'route2');
        $this->instance->add('h0llo', function(){}, 'route3');
        $this->instance->add('bye', function(){}, 'route3');
        $this->instance->add('route4', 'route3', 'route4');
        $this->instance->add('route5', 'route4', 'route5');

        $route = $this->instance->getRoute('route1');
        $this->assertEquals('hello', $route[0]);

        $route = $this->instance->getRoute('route2');
        $this->assertEquals('hallo', $route[0]);

        $route = $this->instance->getRoute('route3');
        $this->assertEquals('bye', $route[0]);

        $route = $this->instance->getRoute('route4');
        $this->assertEquals('route4', $route[0]);

        $route = $this->instance->getRoute('route5');
        $this->assertEquals('route5', $route[0]);

        try {
            $this->instance->getRoute('asdasd');
            $this->fail();
        } catch (\Hahns\Exception\RouteDoesNotExistException $e) {}

        try {
            $this->instance->getRoute([]);
            $this->fail();
        } catch (InvalidArgumentException $e) {}
    }

}