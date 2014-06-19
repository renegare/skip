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
            'settings' => array(
                'debug'=>true,
                'test.setting' => array('test value'),
                'provider.setting' => 'override_defaults',
                'another.service' => '%another.service%'),
            'routes' => array(
                'test' => array('route'=>'/test', 'controller'=>'Skip\\Test\\Helper\\TestServiceProvider::testAction')),
            'services' => array(
                'test.service' => array(
                    'class' => 'Skip\Test\Helper\GenericTestClass',
                    'deps' => array('dep_value', '%another.service%'),
                    'set' => array(
                        'param_a' => 'value_a',
                        'param_b' => '%another.service%'
                    )
                ))
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
        $this->assertInstanceOf('Silex\Application', $this->createApplication());
    }

    public function testCreateClient() {
		$app = $this->createApplication();
		$app->setConfigLoader($this->getConfigLoader());
		$app->configure();

        $client = $this->createClient([], $app);

        $crawler = $client->request('GET', '/test');
        $response = $client->getResponse();

        $this->assertTrue($response->isOK());


		$client = $this->createClient();
		$crawler = $client->request('GET', '/test');
		$response = $client->getResponse();
		$this->assertFalse($response->isOK());
    }
}
