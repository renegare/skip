<?php

namespace Skip;

use Skip\WebApplication;
use Skip\ConfigLoader;
use Symfony\Component\HttpKernel\Client;

class WebApplicationTestCase extends \Silex\WebTestCase {

    public function createClient(array $server = array(), WebApplication $app=null)
    {
        if(!$app) {
            $app = $this->createApplication();
        }
        return new Client($app, $server);
    }

    public function createApplication()
    {
        $app = new WebApplication();
        return $app;
    }

}
