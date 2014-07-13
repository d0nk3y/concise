<?php

namespace Concise\Mock;

class ClassCompiler
{
	protected $className;

	protected $mockUnique;

	protected $rules = array();

	protected $niceMock;

	public function __construct($className, $niceMock = false)
	{
		if(!class_exists($className)) {
			throw new \Exception("The class '$className' is not loaded so it cannot be mocked.");
		}
		$this->className = ltrim($className, '\\');
		$this->mockUnique = '_' . substr(md5(rand()), 24);
		$this->niceMock = $niceMock;
	}

	protected function getNamespaceName()
	{
		$parts = explode('\\', $this->className);
		array_pop($parts);
		return implode('\\', $parts);
	}

	protected function getClassName()
	{
		$parts = explode('\\', $this->className);
		return $parts[count($parts) - 1];
	}

	public function generateCode()
	{
		$refClass = new \ReflectionClass($this->className);
		$prototypeBuilder = new PrototypeBuilder();
		$prototypeBuilder->hideAbstract = true;

		$code = '';
		if($this->getNamespaceName()) {
			$code = "namespace " . $this->getNamespaceName() . "; ";
		}

		$methods = array();
		if(!$this->niceMock) {
			foreach($refClass->getMethods() as $method) {
				$methods[$method->getName()] = $prototypeBuilder->getPrototype($method) . ' { throw new \\Exception("' .
					$method->getName() . '() does not have an associated action - consider a niceMock()?"); }';
			}
		}

		foreach($this->rules as $method => $rule) {
			$action = $rule['action'];
			$realMethod = new \ReflectionMethod($this->className, $method);
			$methods[$method] = $prototypeBuilder->getPrototype($realMethod) . " { if(!array_key_exists('$method', \$this->_methodCalls)) { \$this->_methodCalls['$method'] = array(); } \$this->_methodCalls['$method'][] = func_get_args(); " . $action->getActionCode() . ' }';
		}

		$methods['getCallsForMethod'] = 'public function getCallsForMethod($method) { return array_key_exists($method, $this->_methodCalls) ? $this->_methodCalls[$method] : array(); }';

		unset($methods['__construct']);

		return $code . "class {$this->getMockName()} extends \\{$this->className} { public \$_methodCalls = array(); " . implode(" ", $methods) . "}";
	}

	protected function getMockName()
	{
		return $this->getClassName() . $this->mockUnique;
	}

	public function newInstance()
	{
		return eval($this->generateCode() . " return new {$this->getMockName()}();");
	}

	public function setRules(array $rules)
	{
		$this->rules = $rules;
	}
}
