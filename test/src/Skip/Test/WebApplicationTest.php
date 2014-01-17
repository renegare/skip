<?php
	
	namespace Skip\Test;

	use Skip\WebApplication;
	use Skip\WebApplicationTestCase;

	class WebApplicationTest extends WebApplicationTestCase {

		public function getConfigLoader() {
			$mockConfig = array(
				'settings' => array('test.setting' => array('test value'))
			);
			
			$mockConfigLoader = $this->getMockBuilder('Skip\ConfigLoader')
				->disableOriginalConstructor()
				->setMethods(array('load'))
				->getMock();
			$mockConfigLoader->expects($this->once())
				->method('load')
				->will($this->returnValue($mockConfig));

			return $mockConfigLoader;
		}

		public function testConfigure() {
			$app = $this->createApplication();
			$this->assertEquals($app['test.setting'][0], 'test value');
		}
	}