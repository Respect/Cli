<?php

namespace Respect\Cli;

use Respect\Config\Container;

class Runner
{
	public $arguments;
	public $container;
	public $autoDispatched = true;
	public $command;
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
			elseif ($this->matchInstance($arg)) 
			    $this->command = $this->container->{$arg};
			elseif ($this->matchCommand($arg)) 
				return call_user_func_array(
					array($this->command, $arg), 
					array_slice($this->arguments, $i+1)
				);
	}
	function cleanupArgument($dirtyArgument)
	{
		return ltrim($dirtyArgument, '- ');
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
	function __destruct() 
	{
		if ($this->autoDispatched)
			print $this->run().PHP_EOL;
	}
}
