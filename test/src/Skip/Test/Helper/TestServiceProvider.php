<?php

	namespace Skip\Test\Helper;

	use Silex\Application;
	
	class TestServiceProvider implements \Silex\ServiceProviderInterface {

		public function register(Application $app) {}

		public function boot(Application $app) {}

		public function testAction() {}

		public function misc() {}

	}