<?php

namespace Skip;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

class ConsoleApplication extends Application {
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
}