<?php

namespace Respect\Cli;

use Respect\Config\Container;
use Respect\Config\Instantiator;

class Runner
{
	public $arguments;
	public $container;
	public $autoDispatched = true;
	public $command;
	public $params = array();
	function __construct(array $arguments = array())
	{
		global $argv;
		$this->arguments = array_slice($arguments ?: $argv, 1);
	}
	function run()
	{
		foreach ($this->arguments as $i => $arg)
			if ($this->matchConfigFile($arg))
				$this->container = new Container(realpath($this->cleanupArgument($arg)));
			elseif ($this->matchClass($arg))
				$this->command = new Instantiator($this->cleanupArgument($arg));
			elseif ($this->matchParam($arg)) 
			    $this->setCommandParam($this->cleanupArgument($arg));
			elseif ($this->matchInstance($arg)) 
			    $this->command = $this->container->{$arg};
			elseif ($this->matchCommand($arg)) 
				return $this->command = call_user_func_array(
					array($this->getCommandForRun(), $arg), 
					array_slice($this->arguments, $i+1)
				);
	}
	function getCommandForRun()
	{
		return $this->command instanceof Instantiator 
			? call_user_func($this->command) 
			: $this->command;
	}
	function setCommandParam($paramAsString)
	{
		list($name, $value) = explode('=', $paramAsString);
		$this->command->setParam($name, $value);
	}
	function matchParam($argument)
	{
		return $this->matchConfig($argument) && false !== stripos($argument, '=');
	}
	function cleanupArgument($dirtyArgument)
	{
		return ltrim($dirtyArgument, '- ');
	}
	function matchClass($argument)
	{
		return $this->matchConfig($argument) 
		    && class_exists($this->cleanupArgument($argument));
	}
	function matchInstance($argument)
	{
		return $this->matchCommand($argument) 
		    && !is_null($this->container) 
		    && isset($this->container->{$argument});
	}
	function matchCommand($argument)
	{
		return !$this->matchConfig($argument);
	}
	function matchConfig($argument)
	{
		return substr($argument, 0, 2) == '--';
	}
	function matchConfigFile($argument)
	{
		return $this->matchConfig($argument) && false !== strripos($argument, '.ini');
	}
	function formatOutput($commandReturn)
	{
		if (is_array($commandReturn))
			return print_r($commandReturn, 1);
		elseif (is_scalar($commandReturn))
			return $commandReturn;
		elseif (!method_exists($commandReturn, '__toString'))
			return get_class($commandReturn);
	}
	function __destruct() 
	{
		if ($this->autoDispatched)
			print $this->formatOutput($this->run()).PHP_EOL;
	}
}
