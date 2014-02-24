<?php

namespace Skip\Test\Console;

use Skip\ConsoleApplication;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputInterface;

class ConsoleApplicationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * test that loading the console will display the usual list of available commands and options
	 */
	public function testConsoleDefaultDisplay() {
		$console = new ConsoleApplication();
		$console->setAutoExit(false);

		$tester = new ApplicationTester($console);
        $tester->run(array());

        $this->assertRegExp('/Available commands:/', $tester->getDisplay());
	}

	/**
	 * test that console app captures env and dev user variables
	 */
	public function testConsoleIsEnvAndUserAware() {
		$console = new ConsoleApplication();
		$console->setAutoExit(false);
		$console->setConfigLoaderCallback(function(InputInterface $input, $env=false, $devUser=false){
			$this->assertEquals('test-env', $env);
			$this->assertEquals('test-dev', $devUser);

			return;
		});

		$tester = new ApplicationTester($console);
        $tester->run(array(
        	'--env' => 'test-env',
        	'--devuser' => 'test-dev'
        	));
	}

}