<?php
	
	namespace Skip\Test;

	use Skip\WebApplication;
	use Skip\WebApplicationTestCase;

	class WebApplicationTest extends WebApplicationTestCase {

		public function getConfigLoader() {
			$mockConfig = array(
				'providers' => array(
					array(
						'class'=>'Skip\\Test\\Helper\\TestServiceProvider', 
						'params'=>array('provider.setting' => true))),
				'settings' => array('debug'=>true, 'test.setting' => array('test value'), 'provider.setting' => 'override_defaults'),
				'routes' => array(
					'test' => array('route'=>'/test', 'controller'=>'Skip\\Test\\Helper\\TestServiceProvider::testAction'))
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

		public function testCreateApplication() {
			$app = $this->createApplication();
			$this->assertEquals($app['test.setting'][0], 'test value');
			$this->assertEquals($app['provider'], 'override_defaults');
		}

		public function testCreateClient() {
			$client = $this->createClient();

            $crawler = $client->request('GET', '/test');
            $response = $client->getResponse();

			$this->assertTrue($response->isOK());
		}
	}