<?php

namespace Concise;

class AssertionTest extends TestCase
{
	public function testCreatingAssertionRequiresTheAssertionString()
	{
		$assertion = new Assertion('some string');
		$this->assertEquals('some string', $assertion->getAssertion());
	}
}
