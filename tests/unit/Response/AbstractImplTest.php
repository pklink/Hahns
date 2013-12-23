<?php
namespace Response;
use Hahns\Exception\ParameterMustBeAStringException;
use Hahns\Response\AbstractImpl;

class AbstractImplTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    /**
     * @var AbstractImpl
     */
    protected $instance;

    protected function _before()
    {
        $this->instance = $this->getMockForAbstractClass('\\Hahns\\Response\\AbstractImpl');
    }

    public function testHeader()
    {
        $this->instance->header('bla', 'blub');

        try {
            $this->instance->header([], 'bla');
            $this->fail();
        } catch (ParameterMustBeAStringException $e) { }

        try {
            $this->instance->header('bla', []);
            $this->fail();
        } catch (ParameterMustBeAStringException $e) { }
    }

}