<?php

namespace Skip\Test\Helper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestConsoleCommand extends Command implements \Skip\ContainerInterface {

    protected $app;

    public function setContainer(\Pimple $app)
    {
        $this->app = $app;
    }

    protected function configure()
    {
        $this->setName("test:command");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('runtime env is ' . $this->app['env']);
        $output->writeln('runtime dev.user is ' . $this->app['dev.user']);
    }

}
