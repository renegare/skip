<?php

namespace Skip\Test\Helper;

use Silex\Application;

class TestControllerServiceProvider implements \Silex\ControllerProviderInterface {

    public function connect(Application $app) {
        // register some controllers?
        $controllers = $app['controllers_factory'];

        $controllers->get('/test-controller-service-provider', function(){
            return 'All Good!';
        });

        return $controllers;
    }
}
