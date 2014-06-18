<?php

namespace Concise\Matcher;

class IsNotAnObject extends IsAnObject
{
	public function supportedSyntaxes()
	{
		return array(
			'? is not an object',
		);
	}

	public function match($syntax, array $data = array())
	{
		return !parent::match($syntax, $data);
	}
}