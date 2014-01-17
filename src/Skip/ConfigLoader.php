<?php
	
	namespace Skip;

	use Symfony\Component\Finder\Finder;

	/**
	 * Config loader class that finds and merges the configuratio accross multiple files
	 *
	 * @todo support yaml and ini config loading
	 */
	class ConfigLoader {

		/** @var $finder Finder */
		protected $finder;


		/**
		 * Constructor!
		 *
		 * @param $incliudePaths array - directories to search in for config files
		 * @param $finder Finder - optional!?
		 *
		 * @todo support yaml and ini config loading
		 * @todo loading errors need to throw concise exceptions 
		 * (e.g. use a json lib so we can identify errors in a specific file at a specific line)
		 */

		public function __construct(array $includePaths, Finder $finder=null) {
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

			foreach($this->finder as $file) {
				$config = null;
				switch($file->getExtension()) {
					case 'json':
						$config = json_decode($file->getContents(), true);
						break;
				}

				if($config) {
					$compiledConfig = array_replace_recursive($compiledConfig, $config);
				}
			}

			return $compiledConfig;
		}

		public function getFinder() {
			return $this->finder;
		}

	}