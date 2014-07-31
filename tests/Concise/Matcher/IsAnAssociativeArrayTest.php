<?php

namespace Concise\Matcher;

use \Concise\TestCase;

class IsAnAssociativeArrayTest extends AbstractMatcherTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->matcher = new IsAnAssociativeArray();
	}

	public function testAnAssociativeArrayContainsAtLeastOneKeyThatsNotANumber()
	{
		$x = array(
			"a" => 123,
			0 => "foo",
		);
		$this->assert($x, is_an_associative_array);
	}

	public function testAnArrayIsAssociativeIfAllIndexesAreIntegersButNotZeroIndexed()
	{
		$x = array(
			5 => 123,
			10 => "foo",
		);
		$this->assert($x, is_an_associative_array);
	}

	public function testAnArrayIsNotAssociativeIfZeroIndexed()
	{
		$this->assertFailure('[1,"foo"] is an associative array');
	}
}