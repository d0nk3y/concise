<?php

namespace Concise\Matcher;

use \Concise\TestCase;

class IsNotANumberTest extends AbstractMatcherTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->matcher = new IsNotANumber();
	}

	public function testIntegerIsANumber()
	{
		$this->assertFailure('123 is not a number');
	}

	public function testStringThatRepresentsANumberIsNotANumber()
	{
		$this->assert('"123" is not a number');
	}

	public function testFloatIsANumber()
	{
		$this->assertFailure('12.3 is not a number');
	}
}
