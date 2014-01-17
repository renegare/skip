<?php

	namespace Skip\Test\Helper;

	use Silex\Application;
	
	class TestServiceProvider implements \Silex\ServiceProviderInterface {

		public function register(Application $app) {
	        $app['provider'] = $app->share(function () use ($app) {
	            return $app['provider.setting'] ? $app['provider.setting'] : false;
	        });
		}

		public function boot(Application $app) {}

		public function testAction(Application $app) {
			return 'route working!';
		}

		public function misc() {}

		public function getTime() {
			return time();
		}

	}