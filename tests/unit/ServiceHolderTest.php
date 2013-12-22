<?php

namespace Hahns\Test;

use Codeception\TestCase\Test;
use Hahns\Exception\ServiceMustBeAnObjectException;
use Hahns\ServiceHolder;

class ServiceHolderTest extends Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    /**
     * @var ServiceHolder
     */
    protected $instance;

    protected function _before()
    {
        $this->instance = new ServiceHolder();
    }

    public function testAllFine()
    {
        $this->instance->register('test', function () {
            $service = new \stdClass();
            $service->test = 'blah';
            return $service;
        });

        $this->assertEquals('blah', $this->instance->get('test')->test);
    }

    /**
     * @expectedException \ErrorException
     */
    public function testSetInvalidCallback()
    {
        $this->instance->register('asdas', 1);
    }

    /**
     * @expectedException \Hahns\Exception\ServiceNameMustBeAStringException
     */
    public function testInvalidName()
    {
        $this->instance->register([], function() {});
    }

    public function testCallbackReturnsNoObject()
    {
        // return int
        $this->instance->register('test', function () {
            return 1;
        });

        try {
            $this->instance->get('test');
            $this->assertTrue(false);
        } catch (ServiceMustBeAnObjectException $e) {
            $this->assertTrue(true);
        }

        // return noting
        $this->instance->register('test', function () {
        });

        try {
            $this->instance->get('test');
            $this->assertTrue(false);
        } catch (ServiceMustBeAnObjectException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @expectedException \Hahns\Exception\ServiceDoesNotExistException
     */
    public function testServiceDoesNotExist()
    {
        $this->instance->get('asdasdasd');
    }

}