<?php

namespace Concise\Mock;

abstract class MockAbstractClass
{
    public $constructorRun = false;

    public function __construct($a, $b)
    {
        $this->constructorRun = $b;
    }

    public function myMethod()
    {
        return 'abc';
    }

    protected function mySecretMethod()
    {
        return 'abc';
    }

    public function mySecondMethod()
    {
        return 'bar';
    }

    private function myPrivateMethod()
    {
    }

    public static function myStaticMethod()
    {
        return 'foo';
    }

    public function myWithMethod($a)
    {
    }

    abstract public function myAbstractMethod();

    final public function myFinalMethod()
    {
    }
}

class MockBuilderForAbstractClassTest extends AbstractMockBuilderTestCase
{
    public function getClassName()
    {
        return '\Concise\Mock\MockAbstractClass';
    }
}