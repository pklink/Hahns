<?php

class JsonTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;


    public function testSend()
    {
        $instance = new \Hahns\Response\Json();

        $json = $instance->send(['hello' => 'world']);
        $this->assertEquals('{"hello":"world"}', $json);

        $json = $instance->send([]);
        $this->assertEquals('[]', $json);

        $obj = new stdClass();
        $obj->hello = 'world!';
        $json = $instance->send($obj, \Hahns\Response::CODE_OK);
        $this->assertEquals('{"hello":"world!"}', $json);

        try {
            $instance->send('asd');
            $this->fail();
        } catch (\Hahns\Exception\ArgumentMustBeAnArrayOrAnObjectException $e) { }

        try {
            $instance->send([], 'as');
            $this->fail();
        } catch (\Hahns\Exception\ArgumentMustBeAnIntegerException $e) { }
    }

}