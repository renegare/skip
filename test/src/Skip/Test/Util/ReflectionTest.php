<?php
	
	namespace Skip\Test;

	use Skip\Util\Reflection;
	use Silex\Application;
	use Skip\Config;


	class ReflectionTest extends \PHPUnit_Framework_TestCase {

		public function testGetClosure(){
			$reflect = new Reflection();

			$closure = $reflect->getClosure('Skip\Test\Helper\TestServiceProvider::getTime', function($object){
					$this->assertInstanceOf('Skip\Test\Helper\TestServiceProvider', $object);
				});

			$this->assertEquals(time(), $closure());
		}

		/**
		 * @expectedException RuntimeException
		 */
		public function testGetClosureForRuntimeException(){
			$reflect = new Reflection();
			$closure = $reflect->getClosure('Skip\Test\Helper\NonExistantClass');
		}
	}