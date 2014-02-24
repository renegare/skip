<?php
	
	namespace Skip;

	use Silex\Application as Application;
	use Skip\Config;
	use Skip\ConfigLoader;

	class WebApplication extends Application{

	    /** @var ConsoleApplication */
	    protected $console;

	    public function __construct(array $values = array(), ConsoleApplication $console = null) {
			$this->console = $console;
			parent::__construct($values);
	    }

		public function setConfigLoader(ConfigLoader $loader) {
			$this->loader = $loader;
		}

		public function configure() {
			$configuration = $this->loader->load();

			$config = new Config($this, $this->console);

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

			if($this->console != null && isset($configuration['console'])) {
				$consoleSettings = $configuration['console'];
				if(isset($consoleSettings['commands']) && is_array($consoleSettings['commands'])) {
					foreach($consoleSettings['commands'] as $commandSetting) {
						$config->configureCommand($commandSetting);
					}
				}
			}
		}

	}