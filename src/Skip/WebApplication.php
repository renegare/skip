<?php

namespace Skip;

use Silex\Application as Application;
use Skip\Config;
use Skip\ConfigLoader;

class WebApplication extends Application{

    /** @var ConsoleApplication */
    protected $console;

    /** @var ConfigLoader */
    protected $loader;

    public function __construct(array $values = array()) {
        parent::__construct($values);
    }

    public function setConsoleApplication(ConsoleApplication $console) {
        $this->console = $console;
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

        if(isset($configuration['controllers'])) {
            foreach($configuration['controllers'] as $provider) {
                $config->configureControllerProvider($provider['mount'], $provider['class']);
            }
        }

        if(isset($configuration['settings'])) {
            $config->configureSettings($configuration['settings']);
        }

        if(isset($configuration['routes'])) {
            foreach($configuration['routes'] as $routeName => $route) {
                $config->configureRoute($route, $routeName);
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
