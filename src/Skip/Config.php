<?php
    
    namespace Skip;

    use Silex\Application;
    use Skip\Util\Reflection;

    class Config {

        protected $app;

        public function __construct(Application $app) {
            $this->app = $app;
            $this->reflection = new Reflection();
        }

        public function configureSettings(array $settings) {
            foreach( $settings as $key => $value ) {
                $this->app[$key] = $value;
            }
        }

        public function configureProvider(array $settings) {
            $params = isset($settings['params'])? $settings['params'] : array();
            $class = $settings['class'];
            $this->app->register(new $class, $params );

        }

        public function configureRoute(array $settings, $name='') {
            $controller = $this->app->match($settings['route'], $settings['controller']);

            if(strlen($name) > 0) {
                $controller->bind($name);
            }

            if(isset($settings['method'])) {
                $controller->method($settings['method']);
            }

            if(isset($settings['default'])) {
                foreach($settings['default'] as $key => $value) {
                    $controller->value($key, $value);
                }
            }

            if(isset($settings['convert'])) {
                foreach($settings['convert'] as $key => $value) {
                    $controller->convert($key, $this->reflection->getClosure($value));
                }
            }

            if(isset($settings['assert'])) {
                foreach($settings['assert'] as $key => $value) {
                    $controller->assert($key, $value);
                }
            }

            if(isset($settings['before'])) {
                foreach($settings['before'] as $closurePath) {
                    $controller->before($this->reflection->getClosure($closurePath));
                }
            }

            if(isset($settings['after'])) {
                foreach($settings['after'] as $closurePath) {
                    $controller->after($this->reflection->getClosure($closurePath));
                }
            }
        }
    }