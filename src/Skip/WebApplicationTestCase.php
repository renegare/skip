<?php

	namespace Skip;

	use Skip\WebApplication;
	use Skip\ConfigLoader;
    use Symfony\Component\HttpKernel\Client;

	abstract class WebApplicationTestCase extends \Silex\WebTestCase {

        public function createClient(array $server = array(), WebApplication $app=null)
        {
        	if(!$app) {
        		$app = $this->createApplication();
        	}
            return new Client($app, $server);
        }

        abstract public function getConfigLoader();

		public function createApplication()
		{
			$app = new WebApplication();
			$app->setConfigLoader($this->getConfigLoader());
			$app->configure();
		    return $app;
		}

	}