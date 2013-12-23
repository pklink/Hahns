<?php
use Codeception\Util\Stub;

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
        $this->instance->notFound(function() {});
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
        new \Hahns\Hahns('bla');

        try {
            new \Hahns\Hahns([]);
            $this->fail();
        } catch (\Hahns\Exception\ParsableMustBeAStringOrNullException $e) { }
    }

}