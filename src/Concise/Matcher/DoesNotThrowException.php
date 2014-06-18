<?php

namespace Concise\Matcher;

class DoesNotThrowException extends AbstractMatcher
{
	public function supportedSyntaxes()
	{
		return array(
			'?:callable does not throw exception',
		);
	}

	public function match($syntax, array $data = array())
	{
		try {
			$data[0]();
			return true;
		}
		catch(\Exception $exception) {
			throw new DidNotMatchException("Expected exception not to be thrown.");
		}
	}
}