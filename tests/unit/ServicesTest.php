<?php

namespace Hahns\Test;

use Codeception\TestCase\Test;
use Hahns\Exception\ParameterMustBeAnArrayException;
use Hahns\Exception\ParameterMustBeAStringException;
use Hahns\Exception\ServiceDoesNotExistException;
use Hahns\Exception\ServiceMustBeAnObjectException;
use Hahns\Services;

class ServicesTest extends Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    /**
     * @var Services
     */
    protected $instance;

    protected function _before()
    {
        $this->instance = new Services();
    }

    public function testGet()
    {
        $this->instance->register('test', function () { return new \stdClass(); });
        $this->assertTrue($this->instance->get('test') instanceof \stdClass);

        try {
            $this->instance->get([]);
            $this->fail();
        } catch (ParameterMustBeAStringException $e) { }

        try {
            $this->instance->get('Asdasda');
            $this->fail();
        } catch (ServiceDoesNotExistException $e) { }

        $this->instance->register('test', function ($arg) {
            $o = new \stdClass();
            $o->arg = $arg;
            return $o;
        }, ['hello world']);
        $this->assertEquals('hello world', $this->instance->get('test')->arg);
    }

    public function testRegister()
    {
        $this->instance->register('test', function () { return new \stdClass(); });
        $this->instance->register('test', function () { return new \stdClass(); }, ['asdas', 'asd']);

        try {
            $this->instance->register('asdas', 1);
            $this->fail();
        } catch (\ErrorException $e) { }

        // return int
        $this->instance->register('test', function () {
            return 1;
        });

        try {
            $this->instance->get('test');
            $this->fail();
        } catch (ServiceMustBeAnObjectException $e) { }

        // return nothing
        $this->instance->register('test', function () {});

        try {
            $this->instance->get('test');
            $this->fail();
        } catch (ServiceMustBeAnObjectException $e) { }

        try {
            $this->instance->register('test', function() {}, 1);
            $this->fail();
        } catch (ParameterMustBeAnArrayException $e) { }
    }
}