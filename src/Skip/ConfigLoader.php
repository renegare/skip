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

	}