<?php

	namespace Skip;

	use Skip\WebApplication;
	use Skip\ConfigLoader;
    use Symfony\Component\HttpKernel\Client;

	abstract class WebApplicationTestCase extends \Silex\WebTestCase {

        public function createClient(array $server = array())
        {
            return new Client( $this->createApplication(), $server);
        }

        abstract public function getConfigLoader();

		public function createApplication()
		{
			$app = new WebApplication();
			$app->setConfigLoader($this->getConfigLoader());
			$app->configure();

		    return $app;
		}

        public static function isInternetConnected() {
            if (!$sock = @fsockopen('www.google.com', 80, $num, $error, 5)) {
                return false;
            } else {
                return true;
            }
        }

	}