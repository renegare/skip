<?php

    namespace Skip;

    use Symfony\Component\Finder\Finder;
    use Seld\JsonLint\JsonParser;

    /**
     * Config loader class that finds and merges the configuratio accross multiple files
     *
     * @todo support yaml and ini config loading
     */
    class ConfigLoader {

        /** @var $finder Finder */
        protected $finder;

        /** @var $finder array */
        protected $constants;

        /** @var $constantLoader AbstractConstantConfigLoaderInterface */
        protected $constantLoader;

        /**
         * Constructor!
         *
         * @param $incliudePaths array - directories to search in for config files
         * @param $finder Finder - optional!? I see it only has handy to not have to supply your own finder
         * @param $constants array - key pair values. Values must be a string else expect some issues!
         *
         * @todo support yaml, ini and other config friendly data structures
         * @todo loading errors need to throw concise exceptions
         * @todo validation but no need to be strict. Developers ought to be responsible for what they do
         * (e.g. use a json lib so we can identify errors in a specific file at a specific line)
         */
        public function __construct(array $includePaths, Finder $finder=null, array $constants = array()) {
            $this->constants = $constants;
            if(!$finder) {
                $finder = new Finder();
            }
            $finder->files()->name('*.json');
            $this->finder = $finder;


            foreach($includePaths as $path) {
                $finder->in($path);
            }
        }

        /**
         * find all config files, decode content to PHP and merge together in the correct order
         *
         * @return array
         *
         * @todo add a way to cache (e.g. using APC)
         * @todo support yaml and ini config loading
         * @todo throw exceptions ;)
         * @todo loading errors need to throw concise exceptions and not fail silently
         * (e.g. use a json lib so we can identify errors in a specific file at a specific line)
         */
        public function load() {
            $compiledConfig = array();

            $parser = new JsonParser();
            try {
                foreach($this->finder as $file) {
                    $config = null;
                    switch($file->getExtension()) {
                        case 'json':
                            $config = $this->doReplace($file->getContents());
                            $e = $parser->lint($config);
                            if($e) throw $e;
                            $config = json_decode($config, true);
                            break;
                    }
                    if($config) {
                        $compiledConfig = array_replace_recursive($compiledConfig, $config);
                    }
                }
            } catch (\Seld\JsonLint\ParsingException $e) {
                throw new InvalidConfigException((string) $e);
            }

            return $compiledConfig;
        }

        public function doReplace($config) {

            foreach($this->constants as $key => $constant) {
                $config = preg_replace('/#'.$key.'#/', $constant, $config);
            }
            return $config;
        }

        public function getFinder() {
            return $this->finder;
        }

        public function setConstantLoader(AbstractConstantConfigLoaderInterface $loader) {
            $this->constantLoader = $loader;
        }

        public function getConstantLoader() {
            if(!$this->constantLoader) {
                $this->constantLoader = new ConfigLoader\YamlLoader;
            }
            return $this->constantLoader;
        }

        public function setConstants() {
            $args = array_reverse(func_get_args());
            $constants = $this->constants;
            $loader = $this->getConstantLoader();
            foreach($args as $constantConfig) {

                if(is_string($constantConfig)) {
                    try {
                        $constantConfig = $loader->load($constantConfig);
                    } catch (\Exception $e) {
                        $constantConfig = null;
                    }
                }

                if(!is_array($constantConfig)) {
                    $constantConfig = array();
                }

                if(!$this->isValidConstants($constantConfig)) {
                    $constantConfig = array();
                }

                $constants = array_merge($constants, $constantConfig);
            }
            $this->constants = $constants;
        }

        public function isValidConstants(array $values) {
            $allowedTypes = array('string', 'int', 'integer', 'boolean');
            foreach($values as $key => $value) {
                $type = gettype($value);
                if(!in_array($type, $allowedTypes)) {
                    throw new \Exception(sprintf('Unsupported value %s', $type));
                }
            }
            return true;
        }
    }
