<?php

namespace Concise\Matcher;

use \Concise\TestCase;

class NotExactlyEqualsTest extends AbstractMatcherTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->matcher = new NotExactlyEquals();
	}

	public function testIntegerAndFloatOfTheSameValueAreNotExactlyEqual()
	{
		$this->assert('123 is not exactly equal to 123.0');
	}

	public function testIntegerAndStringRepresentationOfTheSameValueAreNotExactlyEqual()
	{
		$this->assert('123 is not exactly equal to "123"');
	}
}
