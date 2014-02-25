<?php

namespace Skip;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleApplication extends Application {

    /** @var Closure */
    protected $callback;

    /** @var ConfigLoader */
    protected $configLoader;

    /** @var WebApplication */
    protected $app;

    /** @var array */
    protected $values;

    /** @var boolean */
    protected $configLoaded = false;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN') {
        parent::__construct($name, $version);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOptions(array(
            new InputOption('devuser', null, InputOption::VALUE_REQUIRED, "Set app user variable to run under." ),
            new InputOption('env', null, InputOption::VALUE_REQUIRED, "Set app environment variable to run under." )
        ));
        return $definition;
    }

    /**
     * set a callback that will return a ConfigLoader class. Call back will be
     * passed the following variables at runtime:
     * * @param $input InputInterface command line input instance
     * * @param $env string name of evironment (default false)
     * * @param $devUser string name of developer (default false)
     *
     * @param $callback Closure callback that will be executed after the
     * application has been started but before any command is executed
     */
    public function setConfigLoaderCallback(\Closure $callback) {
        $this->callback = $callback;
    }

    /**
     * creates and configures a new application. should only need to call this when testing
     * as it is automatically called when the application is run (see configureIO).
     * this method is idempotent ... runs once and thats it!
     * @param $input InputInterface required by configuration callback (called internally)
     * @return void
     */
    public function loadConfig(InputInterface $input) {
        if($this->configLoaded || !$this->callback) return;
        $this->configLoaded = true;
        $callback = $this->callback;
        $configLoader = $callback($input, $input->getParameterOption('--env'), $input->getParameterOption('--devuser'));
        if($configLoader) {
            if(!$this->app) {
                $this->app = new WebApplication();
            }
            $this->app->setConfigLoader($configLoader);
            $this->app->setConsoleApplication($this);
            $this->app->configure();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureIO(InputInterface $input, OutputInterface $output) {
        $this->loadConfig($input);
        return parent::configureIO($input, $output);
    }

    public function setServiceContainer(WebApplication $app) {
        $this->app = $app;
    }

    public function getServiceContainer() {
        return $this->app;
    }
}