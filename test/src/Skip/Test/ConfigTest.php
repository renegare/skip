<?php
	
	namespace Skip\Test;

	use Silex\Application;
	use Skip\Config;


	class ConfigTest extends \PHPUnit_Framework_TestCase {

		public function testConfigureSettings(){
			$settings = array(
				'config.param1' => true,
				'config.param2' => array(2),
				'config.param3' => (object) array('var1' => 'value1')
			);

			$app = new Application();
			$config = new Config($app);

			$config->configureSettings($settings);

			foreach($settings as $key => $value) {
				$this->assertTrue(isset($app[$key]), "Assert application has the key '$key'");
				$this->assertEquals($value, $app[$key], "Assert \$app['$key'] === \$settings['$key']");
			}
		}

		public function getProviderSettingTestCases() {
			return array(
				array(
					array(
						'class' => 'Skip\\Test\\Helper\\TestServiceProvider'
					)
				),
				array(
					array(
						'class' => 'Skip\\Test\\Helper\\TestServiceProvider',
						'params' => array(
							'param.1' => 1,
							'param.2' => 2
						)
					)
				)
			);
		}

		/**
		 * 
		 * @dataProvider getProviderSettingTestCases
		 */
		public function testConfigureProvider($providerSetting) {

			$mockApplication = $this->getMockBuilder('Silex\Application')
				->disableOriginalConstructor()
				->getMock();

			$mockApplication->expects($this->once())
				->method('register')
				->will($this->returnCallback(function($provider, $params) use ($providerSetting) {
					$this->assertInstanceOf($providerSetting['class'], $provider);
					if(isset($providerSetting['params'])) {
						$originalParams = $providerSetting['params'];
						$this->assertEquals(gettype($params), gettype($originalParams));
						if(is_array($params)) {
							foreach($originalParams as $key => $value) {
								$this->assertTrue(isset($params[$key]), "Assert provider params has the key '$key'");
								$this->assertEquals($value, $params[$key], "Assert \$params['$key'] === \$originalParams['$key']");
							}
						}
					}
				}));

			$config = new Config($mockApplication);
			$config->configureProvider($providerSetting);

		}
		
		/**
		 * test silex app route is configured 
		 */
		public function testConfigureRoute() {
			$routeName = 'test';
			$routeSettings = array(
				'route' => '/{id}',
				'controller' => 'Skip\\Test\\Helper\\TestServiceProvider::testAction',
				
				'method' => 'get',

				'assert' => array('id' => '\d+'),
				'default' => array('id' => 1),
				'convert' => array('id' => 'Skip\\Test\\Helper\\TestServiceProvider::misc'),

				'before' => array('Skip\\Test\\Helper\\TestServiceProvider::misc'),
				'after' => array('Skip\\Test\\Helper\\TestServiceProvider::misc')
			);

			$mockController = $this->getMockBuilder('Silex\Controller')
				->disableOriginalConstructor()
				->setMethods(array('method', 'bind', 'value', 'assert', 'convert', 'before', 'after' ))
				->getMock();

			$mockController->expects($this->once())
				->method('bind')
				->will($this->returnCallback(function($name) use ($routeName) {
					$this->assertEquals($routeName, $name);
				}));

			if(isset($routeSettings['method'])) {
				$mockController->expects($this->once())
					->method('method')
					->will($this->returnCallback(function($method) use ($routeSettings, $mockController) {
						$this->assertInternalType('string', $method);
						$methods = explode('|', $method);
						$originalMethods = explode('|', $routeSettings['method']);
						foreach( $methods as $method ) {
							$this->assertContains($method, $originalMethods);
						}
						return $mockController;
					}));
			}

			if(isset($routeSettings['default'])) {
				$default = $routeSettings['default'];
				$mockController->expects($this->exactly(count($default)))
					->method('value')
					->will($this->returnCallback(function($paramName, $value) use ($default) {
						$this->assertTrue(isset($default[$paramName]), "Assert route defaults has the key '$paramName'");
						$this->assertEquals($value, $default[$paramName]);
					}));
			}

			if(isset($routeSettings['convert'])) {
				$convert = $routeSettings['convert'];
				$mockController->expects($this->exactly(count($convert)))
					->method('convert')
					->will($this->returnCallback(function($paramName, \Closure $callback) use ($convert) {
						$this->assertTrue(isset($convert[$paramName]), "Assert route convert has the key '$paramName'");
					}));
			}

			if(isset($routeSettings['assert'])) {
				$assert = $routeSettings['assert'];
				$mockController->expects($this->exactly(count($assert)))
					->method('assert')
					->will($this->returnCallback(function($paramName, $assertion) use ($assert) {
						$this->assertTrue(isset($assert[$paramName]), "Assert route assertions has the key '$paramName'");
						$this->assertEquals($assertion, $assert[$paramName]);
					}));
			}

			if(isset($routeSettings['before'])) {
				$before = $routeSettings['before'];
				$mockController->expects($this->exactly(count($before)))
					->method('before')
					->with($this->callback(function(\Closure $callback) use ($before) {
						return true;
					}));
			}

			if(isset($routeSettings['after'])) {
				$after = $routeSettings['after'];
				$mockController->expects($this->exactly(count($after)))
					->method('after')
					->with($this->callback(function(\Closure $callback) use ($after) {
						return true;
					}));
			}

			$mockApplication = $this->getMockBuilder('Silex\Application')
				->disableOriginalConstructor()
				->getMock();

			$mockApplication->expects($this->once())
				->method('match')
				->will($this->returnCallback(function($routePath, $controller) use ($routeSettings, $mockController) {
					$this->assertEquals($routePath, $routeSettings['route']);
					$this->assertEquals($controller, $routeSettings['controller']);
					return $mockController;
				}));

			$config = new Config($mockApplication);
			$config->configureRoute($routeSettings, $routeName);

		}
	}