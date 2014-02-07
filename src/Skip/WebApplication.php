<?php
	
	namespace Skip;

	use Silex\Application as Application;
	use Skip\Config;
	use Skip\ConfigLoader;

	class WebApplication extends Application{

		public function setConfigLoader(ConfigLoader $loader) {
			$this->loader = $loader;
		}

		public function configure() {
			$configuration = $this->loader->load();

			$config = new Config($this);

			if(isset($configuration['providers'])) {
				foreach($configuration['providers'] as $provider) {
					$config->configureProvider($provider);
				}
			}
			
			if(isset($configuration['settings'])) {
				$config->configureSettings($configuration['settings']);
			}

			if(isset($configuration['routes'])) {
				foreach($configuration['routes'] as $route) {
					$config->configureRoute($route);
				}
			}

			if(isset($configuration['services'])) {
				foreach($configuration['services'] as $serviceName => $service) {
					$config->configureService($serviceName, $service);
				}
			}
		}


	}