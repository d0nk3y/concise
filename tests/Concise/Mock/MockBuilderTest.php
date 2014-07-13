<?php

namespace Concise\Mock;

use \Concise\TestCase;

class Mock1
{
	public function myMethod()
	{
		return 'abc';
	}
}

abstract class Mock2
{
	public abstract function foo();

	public function bar()
	{
		return 123;
	}
}

class MockBuilderTest extends TestCase
{
	protected function _mock($className = '\stdClass')
	{
		return new MockBuilder($this, $className, false, true);
	}

	protected function _niceMock($className = '\stdClass')
	{
		return new MockBuilder($this, $className, true, true);
	}

	public function testMockCanBeCreatedFromAClassThatExists()
	{
		$mock = $this->_mock('\Concise\TestCase')
		             ->done();
		$this->assert($mock, instance_of, '\Concise\TestCase');
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Class '\Abc' does not exist.
	 */
	public function testExceptionIsThrownIfTheClassTryingToBeMockedDoesNotExist()
	{
		$this->_mock('\Abc')->done();
	}

	public function testCanStubMethodWithAssociativeArray()
	{
		$mock = $this->_mock('\Concise\Mock\Mock1')
		             ->stub(array('myMethod' => 123))
		             ->done();
		$this->assert($mock->myMethod(), equals, 123);
	}

	public function testStubbingWithAnArrayCanCreateMultipleStubs()
	{
		$mock = $this->_mock('\Concise\Mock\Mock2')
		             ->stub(array('bar' => 123, 'foo' => 'bar'))
		             ->done();
		$this->assert($mock->foo(), equals, 'bar');
	}

	// @test you cannot mock methods that do not exist

	// @test you can only mock methods that do not exist if there is an approproate __call

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage stub() called with array must have at least 1 element.
	 */
	public function testStubbingWithAnArrayMustHaveMoreThanZeroElements()
	{
		$this->_mock('\Concise\Mock\Mock1')
		     ->stub(array())
		     ->done();
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage myMethod() does not have an associated action - consider a niceMock()?
	 */
	public function testCallingMethodThatHasNoAssociatedActionWillThrowAnException()
	{
		$mock = $this->_mock('\Concise\Mock\Mock1')
		             ->done();
		$mock->myMethod();
	}

	public function testNiceMockCanBeCreatedFromAClassThatExists()
	{
		$mock = $this->_niceMock('\Concise\TestCase')
		             ->done();
		$this->assert($mock, instance_of, '\Concise\TestCase');
	}

	public function testCallingMethodThatHasNoAssociatedActionOnANiceMockWillUseOriginal()
	{
		$mock = $this->_niceMock('\Concise\Mock\Mock1')
		             ->done();
		$this->assert($mock->myMethod(), equals, 'abc');
	}

	public function testCallingMethodOnNiceMockWithStub()
	{
		$mock = $this->_niceMock('\Concise\Mock\Mock1')
		             ->stub(array('myMethod' => 123))
		             ->done();
		$this->assert($mock->myMethod(), equals, 123);
	}

	public function testStubsCanBeCreatedByChainingAnAction()
	{
		$mock = $this->_mock('\Concise\Mock\Mock1')
		             ->stub('myMethod')->andReturn(123)
		             ->done();
		$this->assert($mock->myMethod(), equals, 123);
	}

	public function testStubWithNoActionWillReturnNull()
	{
		$this->mock = $this->_mock('\Concise\Mock\Mock1')
		                   ->stub('myMethod')
		                   ->done();
		$this->assert($this->mock->myMethod(), is_null);
	}

	public function testStubCanReturnNull()
	{
		$mock = $this->_mock('\Concise\Mock\Mock1')
		             ->stub('myMethod')->andReturn(null)
		             ->done();
		$this->assert($mock->myMethod(), is_null);
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage whatever
	 */
	public function testStubCanThrowException()
	{
		$mock = $this->_mock('\Concise\Mock\Mock1')
		             ->stub('myMethod')->andThrow(new \Exception('whatever'))
		             ->done();
		$mock->myMethod();
	}

	public function testCanCreateAnExpectation()
	{
		$mock = $this->mock('\Concise\Mock\Mock1')
		             ->expect('myMethod')->once()->andReturn(null)
		             ->done();
		$mock->myMethod();
	}

	public function testCanCreateAnExpectationOfTwice()
	{
		$mock = $this->mock('\Concise\Mock\Mock1')
		             ->expect('myMethod')->twice()->andReturn(null)
		             ->done();
		$mock->myMethod();
		$mock->myMethod();
	}

	public function testCanCreateAnExpectationOfNever()
	{
		$mock = $this->mock('\Concise\Mock\Mock1')
		             ->expect('myMethod')->never()->andReturn(null)
		             ->done();
	}

	public function testWeDoNotNeedToSpecifyAnActionForAnExpectationWeNeverWantToHappen()
	{
		$mock = $this->mock('\Concise\Mock\Mock1')
		             ->expect('myMethod')->never()
		             ->done();
	}

	public function testCanCreateAnExpectationOfASpecificAmountOfTimes()
	{
		$mock = $this->mock('\Concise\Mock\Mock1')
		             ->expect('myMethod')->exactly(3)->andReturn(null)
		             ->done();
		$mock->myMethod();
		$mock->myMethod();
		$mock->myMethod();
	}

	public function testExactlyZeroIsTheSameAsNever()
	{
		$mock = $this->mock('\Concise\Mock\Mock1')
		             ->expect('myMethod')->exactly(0)
		             ->done();
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage myMethod() has more than one action attached.
	 */
	public function testMethodsCanOnlyHaveOneActionAppliedToThem()
	{
		$mock = $this->mock('\Concise\Mock\Mock1')
		             ->stub('myMethod')->andReturn(123)->andReturn(456)
		             ->done();
	}

	public function testDefaultExpectationIsOnce()
	{
		$mock = $this->mock('\Concise\Mock\Mock1')
		             ->expect('myMethod')->andReturn(null)
		             ->done();
		$mock->myMethod();
	}

	public function testCanCreateAnExpectationWithArgumentValues()
	{
		$mock = $this->mock('\Concise\Mock\Mock1')
		             ->expect('myMethod')->with('foo')->andReturn('bar')
		             ->done();
		$this->assert($mock->myMethod('foo'), equals, 'bar');
	}

	public function testCanUseExpectsInsteadOfExpect()
	{
		$mock = $this->mock('\Concise\Mock\Mock1')
		             ->expects('myMethod')
		             ->done();
		$mock->myMethod();
	}

	public function testMockClassDefaultsToStdClass()
	{
		$mock = $this->mock()
		             ->done();
		$this->assert($mock, instance_of, '\stdClass');
	}

	public function testNiceMockClassDefaultsToStdClass()
	{
		$mock = $this->niceMock()
		             ->done();
		$this->assert($mock, instance_of, '\stdClass');
	}

	public function testNiceMockCanBeCreatedFromAnAbstractClass()
	{
		$mock = $this->niceMock('\Concise\Mock\Mock2')
		             ->stub(array('foo' => 'bar'))
		             ->done();
		$this->assert($mock->foo(), equals, 'bar');
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage You cannot create a mock() from an abstract class - use a niceMock() instead.
	 */
	public function testYouCannotCreateAMockFromAnAbstractClass()
	{
		$mock = $this->mock('\Concise\Mock\Mock2')
		             ->done();
	}

	public function testMockSetsActualCallsToZeroWhenRuleIsCreated()
	{
		$this->_mock('\Concise\Mock\Mock1')
		     ->stub(array('myMethod' => 123))
		     ->done();

		$this->assert(end($this->_mocks)->getCallCountForMethod('myMethod'), exactly_equals, 0);
	}
}
