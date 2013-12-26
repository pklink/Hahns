<?php

class TestTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;


    public function testSend()
    {
        $instance = new \Hahns\Response\Text();

        $response = $instance->send('hello world');
        $this->assertEquals('hello world', $response);

        $response = $instance->send('');
        $this->assertEquals('', $response);

        try {
            $instance->send([]);
            $this->fail();
        } catch (\Hahns\Exception\ArgumentMustBeAStringException $e) { }

        try {
            $instance->send('', 'as');
            $this->fail();
        } catch (\Hahns\Exception\ArgumentMustBeAnIntegerException $e) { }
    }

}