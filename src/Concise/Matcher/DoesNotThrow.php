<?php

namespace Concise\Matcher;

class DoesNotThrow extends Throws
{
	public function supportedSyntaxes()
	{
		return array(
			'?:callable does not throw ?:class',
		);
	}

	public function match($syntax, array $data = array())
	{
		try {
			$data[0]();
			return true;
		}
		catch(\Exception $exception) {
			if($this->isKindOfClass($exception, $data[1])) {
				$exceptionClass = get_class($exception);
				throw new DidNotMatchException("Expected {$data[1]} not to be thrown, but $exceptionClass was thrown.");
			}
			return true;
		}
		throw new DidNotMatchException("Expected {$data[1]} not to be thrown, but nothing was thrown.");
	}
}