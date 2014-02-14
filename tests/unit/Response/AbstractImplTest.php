<?php
namespace Response;
use Hahns\Exception\VariableHasToBeAnIntegerException;
use Hahns\Exception\VariableHasToBeAStringException;
use Hahns\Exception\VariableHasToBeAStringOrNullException;
use Hahns\Exception\StatusMessageCannotFindException;
use Hahns\Response\AbstractImpl;
use Hahns\Response;

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
        } catch (VariableHasToBeAStringException $e) { }

        try {
            $this->instance->header('bla', []);
            $this->fail();
        } catch (VariableHasToBeAStringException $e) { }
    }

    public function testStatus()
    {
        $this->instance->status(201);
        $this->instance->status(201, null);
        $this->instance->status(201, null, '1.0');
        $this->instance->status(213123, 'roflcopter');

        try {
            $this->instance->status('s');
            $this->fail();
        } catch (VariableHasToBeAnIntegerException $e) {}

        try {
            $this->instance->status(201, []);
            $this->fail();
        } catch (VariableHasToBeAStringOrNullException $e) {}

        try {
            $this->instance->status(201, null, []);
            $this->fail();
        } catch (VariableHasToBeAStringException $e) {}

        try {
            $this->instance->status(213123);
            $this->fail();
        } catch (StatusMessageCannotFindException $e) {}
    }

    public function testRedirect()
    {
        $this->instance->redirect('/blabla');
        $this->instance->redirect('/blabla', Response::CODE_FOUND);

        try {
            $this->instance->redirect([]);
            $this->fail();
        } catch (VariableHasToBeAStringException $e) { }

        try {
            $this->instance->redirect('1', 324234);
            $this->fail();
        } catch (StatusMessageCannotFindException $e) { }
    }

}