<?php

namespace Concise;

use DateTime;

class TestCasePartialMockObject
{
    protected $foo = 'bar';
}

/**
 * @group mocking
 * @group #129
 */
class TestCasePartialMockTest extends TestCase
{
    public function testPartialMockReturnsMockBuilder()
    {
        $instance = new DateTime();
        $this->assert($this->partialMock($instance), instance_of, '\Concise\Mock\MockBuilder');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected object, but got string for argument 1
     */
    public function testPartialMockMustReceiveAnObject()
    {
        $this->partialMock('foo');
    }

    public function testPartialMockReturnsAnInstanceOfItself()
    {
        $instance = new DateTime();
        $mock = $this->partialMock($instance)->get();
        $this->assert($mock, instance_of, '\DateTime');
    }

    public function testPartialMockWillInheritObjectValuesToMaintainState()
    {
        $instance = json_decode('{"foo":"bar"}');
        $mock = $this->partialMock($instance)->get();
        $this->assert($mock->foo, equals, 'bar');
    }

    public function testPartialMockWillInheritProtectedObjectValuesToMaintainState()
    {
        $instance = new TestCasePartialMockObject();
        $mock = $this->partialMock($instance)->get();
        $this->assert($this->getProperty($mock, 'foo'), equals, 'bar');
    }
}
