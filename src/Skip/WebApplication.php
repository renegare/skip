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
				foreach($configuration['routes'] as $provider) {
					$config->configureRoute($provider);
				}
			}
		}


	}