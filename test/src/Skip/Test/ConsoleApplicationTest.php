<?php

namespace Skip\Test\Console;

use Skip\ConsoleApplication;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputInterface;

class ConsoleApplicationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * test that loading the console will display the usual list
	 * of available commands and options
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

	/**
	 * test that console configures command defined in given config
	 */
	public function testConsoleCommandIsConfigured() {
		$console = new ConsoleApplication();
		$console->setAutoExit(false);
		$console->setConfigLoaderCallback(function(InputInterface $input){

			// test config
			$mockConfigLoader = $this->getMock('Skip\ConfigLoader', array('load'), array(), '', FALSE);
			$mockConfigLoader->expects($this->once())
				->method('load')
				->will($this->returnValue(array(
					'console' => array(
						'commands' => array(
							'Skip\Test\Helper\TestConsoleCommand'
							)
						)
					)));
			return $mockConfigLoader;
		});

		$tester = new ApplicationTester($console);
        $tester->run(array());
        $this->assertRegExp('/test:command/', $tester->getDisplay());
	}

	/*
	 * test that console sets a reference of the pimple app to the command if 
	 * provided it has implemented the correct interface
	 */
	public function testConsoleInjectsContainer() {
		$console = new ConsoleApplication();
		$console->setAutoExit(false);
		$console->setConfigLoaderCallback(function(InputInterface $input, $env=false, $devUser=false){

			// test config
			$mockConfigLoader = $this->getMock('Skip\ConfigLoader', array('load'), array(), '', FALSE);
			$mockConfigLoader->expects($this->once())
				->method('load')
				->will($this->returnValue(array(
					'settings' => array(
						'env' => $env, // @note: you need to insert env variables into your config
						'dev.user' => $devUser, // @ditto as above
					),
					'console' => array(
						'commands' => array(
							'Skip\Test\Helper\TestConsoleCommand'
							)
						)
					)));
			return $mockConfigLoader;
		});

		$tester = new ApplicationTester($console);
        $tester->run(array(
        	'command' => 'test:command',
        	'--env' => 'test-env',
        	'--devuser' => 'test-user'
        	));

       	$output = $tester->getDisplay();
        $this->assertRegExp('/runtime env is test-env/', $output);
        $this->assertRegExp('/runtime dev.user is test-user/', $output);
	}
}