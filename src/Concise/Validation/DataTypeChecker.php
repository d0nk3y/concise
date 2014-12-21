<?php

namespace Concise\Validation;

use Closure;
use Concise\Syntax\Token\Attribute;
use Exception;

class DataTypeChecker
{
    /**
	 * @var boolean
	 */
    protected $excludeMode = false;

    /**
	 * @var array
	 */
    protected $context = array();

    /**
	 * @param array $context
	 */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
	 * @param  array $acceptedTypes
	 * @param  mixed $value
	 * @return bool
	 */
    public function check(array $acceptedTypes, $value)
    {
        if (count($acceptedTypes) === 0) {
            return $value;
        }

        return $this->throwInvalidArgumentException($acceptedTypes, $value, !$this->excludeMode);
    }

    /**
	 * @param  array $acceptedTypes
	 * @param  mixed $value
	 * @return bool
	 */
    protected function matchesInAcceptedTypes(array $acceptedTypes, $value)
    {
        foreach ($acceptedTypes as $acceptedType) {
            if ($this->matches($acceptedType, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Will check to see if the value is an object and its class is listed in the accepted types.
     * @param  array       $acceptedTypes Accepted types.
     * @param  mixed       $value         Value to test.
     * @return null|object
     */
    protected function checkSpecificObject(array $acceptedTypes, $value)
    {
        if (is_object($value)) {
            foreach ($acceptedTypes as $type) {
                $c = get_class($value);
                if ($c == $type || "\\$c" == $type) {
                    return $value;
                }
            }
        }

        return null;
    }

    /**
     * @param array $acceptedTypes
     * @param $value
     * @param boolean $expecting
     * @throws Exception
     * @return string
     */
    protected function checkExpecting(array $acceptedTypes, $value, $expecting)
    {
        $match = $this->matchesInAcceptedTypes($acceptedTypes, $value);
        if ($expecting === $match) {
            if (is_object($value) && $value instanceof Attribute) {
                $value = $this->getAttribute($value->getValue());
            }
            if (in_array('class', $acceptedTypes) && is_string($value)) {
                if (!class_exists($value) && !interface_exists($value)) {
                    throw new Exception("No such class or interface '$value'.'");
                }
                return ltrim($value, '\\');
            }
            if (in_array('regex', $acceptedTypes)) {
                return $value;
            }

            return $value;
        }

        throw new DataTypeMismatchException($this->getType($value), $acceptedTypes);
    }

    /**
	 * @param  array  $acceptedTypes
	 * @param  mixed  $value
	 * @param  bool   $expecting
	 * @return mixed
	 */
    protected function throwInvalidArgumentException(array $acceptedTypes, $value, $expecting)
    {
        if (($r = $this->checkSpecificObject($acceptedTypes, $value))) {
            return $r;
        }

        return $this->checkExpecting($acceptedTypes, $value, $expecting);
    }

    /**
     * @param string $name
     * @throws Exception if the attribute does not exist.
     * @return mixed
     */
    protected function getAttribute($name)
    {
        if (!array_key_exists($name, $this->context)) {
            throw new Exception("Attribute '$name' does not exist.");
        }

        return $this->context[$name];
    }

    protected function isRegex($value)
    {
        return is_object($value) && get_class($value) === 'Concise\Syntax\Token\Regexp';
    }

    protected function isAttribute($value)
    {
        return is_object($value) && get_class($value) === 'Concise\Syntax\Token\Attribute';
    }

    /**
	 * @param  mixed $value
	 * @return string
	 */
    protected function getType($value)
    {
        if ($this->isRegex($value)) {
            return 'regex';
        }
        if ($this->isAttribute($value)) {
            return $this->getType($this->getAttribute($value->getValue()));
        }
        if ($value instanceof Closure) {
            return 'callable';
        }

        return gettype($value);
    }

    /**
	 * @param  string $type
	 * @param  string $value
	 * @return bool
	 */
    protected function singleMatch($type, $value)
    {
        return $type === $this->simpleType($this->getType($value));
    }

    /**
	 * @param  string $type
	 * @param  mixed $value
	 * @return bool
	 */
    protected function matches($type, $value)
    {
        if ($type === 'number') {
            return $this->singleMatch('int', $value) || $this->singleMatch('float', $value) || is_numeric($value);
        }

        return $this->singleMatch($this->simpleType($type), $value);
    }

    /**
	 * @param  string $type
	 * @return string
	 */
    protected function simpleType($type)
    {
        $aliases = array(
            'integer' => 'int',
            'double'  => 'float',
            'class'   => 'string',
            'bool'    => 'boolean',
            'regex'   => 'string',
        );
        if (array_key_exists($type, $aliases)) {
            return $aliases[$type];
        }

        return $type;
    }

    public function setExcludeMode()
    {
        $this->excludeMode = true;
    }
}
