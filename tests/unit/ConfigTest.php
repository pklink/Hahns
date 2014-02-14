<?php

class ConfigTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    /**
     * @var \Hahns\Config
     */
    protected $instance;

    protected function _before()
    {
        $this->instance = new \Hahns\Config();
        $this->instance->set('string', 'bla');
        $this->instance->set('array', []);
    }

    public function testGet()
    {
        $this->assertEquals('bla', $this->instance->get('string'));
        $this->assertEquals([], $this->instance->get('array'));
        $this->assertNull($this->instance->get('asdasdasd'));
        $this->assertEquals('rofl', $this->instance->get('asdasda', 'rofl'));

        try {
            $this->instance->get([]);
            $this->fail();
        } catch (\Hahns\Exception\VariableHasToBeAStringException $e) { }
    }

    public function testSet()
    {
        $this->instance->set('string', 'bla');
        $this->instance->set('array', []);

        try {
            $this->instance->set([], 'as');
            $this->fail();
        } catch (\Hahns\Exception\VariableHasToBeAStringException $e) { }
    }
}