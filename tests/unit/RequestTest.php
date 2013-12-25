<?php

class RequestTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    /**
     * @var \Hahns\Request
     */
    protected $instance;

    protected function _before()
    {
        $this->instance = new \Hahns\Request();

        // set get
        $_GET = [
            'string' => 'hello-get',
            'array'  => ['get']
        ];

        // set post
        $_POST = [
            'string' => 'hello-post',
            'array'  => ['post']
        ];
    }

    public function testGet()
    {
        $this->assertEquals('hello-get', $this->instance->get('string'));
        $this->assertEquals(['get'], $this->instance->get('array'));
        $this->assertNull($this->instance->get('notexist'));
        $this->assertTrue($this->instance->get('notexist', true));
    }

    public function testPost()
    {
        $this->assertEquals('hello-post', $this->instance->post('string'));
        $this->assertEquals(['post'], $this->instance->post('array'));
        $this->assertNull($this->instance->post('notexist'));
        $this->assertTrue($this->instance->post('notexist', true));
    }

    public function testHeader()
    {
        $this->assertNull($this->instance->header('asdasdas'));
        $this->assertEquals('blub', $this->instance->header('asdasdasd', 'blub'));

        try {
            $this->instance->header([]);
            $this->fail();
        } catch (\Hahns\Exception\ParameterMustBeAStringException $e) { }

    }

}