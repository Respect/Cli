<?php

namespace Respect\Cli;

class Runner
{
	function __construct(array $arguments = array())
	{
		global $argv;
		$arguments = $arguments ?: $argv;
	}
}
