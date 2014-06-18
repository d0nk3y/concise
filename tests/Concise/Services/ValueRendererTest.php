<?php

namespace Concise\Services;

class ValueRendererTest extends \Concise\TestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->renderer = new ValueRenderer();
	}

	public function testIntegerValueRendersWithoutModification()
	{
		$this->assertSame('123', $this->renderer->render(123));
	}

	public function testFloatingPointValueRendersWithoutModification()
	{
		$this->assertSame('1.23', $this->renderer->render(1.23));
	}

	public function testStringValueRendersWithDoubleQuotes()
	{
		$this->assertSame('"abc"', $this->renderer->render("abc"));
	}

	public function testArrayValueRendersAsJson()
	{
		$this->assertSame('[123,"abc"]', $this->renderer->render(array(123, "abc")));
	}

	public function testObjectValueRendersAsJson()
	{
		$obj = new \stdClass();
		$obj->a = 123;
		$this->assertSame('{"a":123}', $this->renderer->render($obj));
	}

	public function testTrueValueRendersAsTrue()
	{
		$this->assertSame('true', $this->renderer->render(true));
	}

	public function testFalseValueRendersAsFalse()
	{
		$this->assertSame('false', $this->renderer->render(false));
	}

	public function testNullRendersAsNull()
	{
		$this->assertSame('null', $this->renderer->render(null));
	}

	public function testResourceValueRendersAsResource()
	{
		$renderer = new ValueRenderer();
		$this->str = $renderer->render(fopen('.', 'r'));
		$this->assert('str starts with "Resource id #"');
	}

	public function testFunctionRendersAsString()
	{
		$this->assertSame('function', $this->renderer->render(function() {}));
	}
}