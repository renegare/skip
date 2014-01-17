<?php
	
	namespace Skip\Util;

	class Reflection {

		protected $objectCache=array();
		protected $methodCache=array();

		/**
		 * get a closure instance of a given class::method. One caveat, the constructor needs to accept no arguments
		 * @param $method string e.g 'Full\\Namespace\\ClassName::methodName'
		 * @param $initialiseCallback \Closure
		 * @param $cache boolean
		 *
		 * @return \Closure
		 *
		 * @throws \RuntimeException
		 *
		 * @todo implement cache functionality
		 */
		public function getClosure($method, \Closure $intialiseCallback = null, $cache=false) {
			if( preg_match('/^([A-Za-z0-9_\\\\]+)::([A-Za-z0-9_\\\\]+)$/', $method, $matches)) {

				$object = new $matches[1];
				if( $intialiseCallback )
				{
					$intialiseCallback( $object );
				}
				$method = new \ReflectionMethod( $object, $matches[2]);
				$method = $method->getClosure( $object );

			}

			if( !is_object($method) ) {
				throw new \RuntimeException( sprintf("Cannot find class or method '%s", $method ) );
			}

			return $method;
		} 
	}