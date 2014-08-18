<?php

    namespace Skip;

    use Skip\Util\Reflection;

    class Config {

        protected $app;

        protected $console;

        protected $reflection;

        public function __construct(WebApplication $app, ConsoleApplication $console=null) {
            $this->app = $app;
            $this->console = $console;
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

            if(isset($settings['debug'])
                && $settings['debug'] == true
                && $this->app['debug'] != true ) return;

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

        public function configureService($serviceName, array $settings) {
            $this->app[$serviceName] = $this->app->share(function(WebApplication $app) use ($settings){
                $class = new \ReflectionClass( $settings['class'] );

                $arguments = array();
                $deps = isset($settings['deps'])? $settings['deps'] : array();
                foreach($deps as $key ) {
                    if(is_string($key) && preg_match('/^%(.+)%$/', $key, $matches)) {
                        $key = $matches[1];
                        $value = $this->app[$key];
                    } else {
                        $value = $key;
                    }

                    $arguments[] = $value;
                }

                $instance = $class->newInstanceArgs( $arguments );

                if(isset($settings['set'])) {
                    foreach($settings['set'] as $field => $value ) {
                        if(is_string($value) && preg_match('/^%(.+)%$/', $value, $matches)) {
                            $value = $matches[1];
                            $value = $this->app[$value];
                        }

                        $method = 'set'.ucwords(preg_replace('/_+/', ' ', strtolower($field)));
                        $method = preg_replace('/\s+/', '', $method);

                        $instance->$method($value);
                    }
                }
                return $instance;
            });
        }

        public function configureCommand($commandClassName){
            $command = new $commandClassName;
            if($command instanceof ContainerInterface) {
                $command->setContainer($this->app);
            }
            $this->console->add($command);
        }

        public function configureControllerProvider($mount, $class) {
            $this->app->mount($mount, new $class);
        }
    }
