<?php

class HtmlTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;


    public function testSend()
    {
        $instance = new \Hahns\Response\Html();

        $response = $instance->send('<h1>hello world</h1>');
        $this->assertEquals('<h1>hello world</h1>', $response);

        $response = $instance->send('');
        $this->assertEquals('', $response);

        try {
            $instance->send([]);
            $this->fail();
        } catch (InvalidArgumentException $e) { }

        try {
            $instance->send('', 'as');
            $this->fail();
        } catch (InvalidArgumentException $e) { }
    }

}