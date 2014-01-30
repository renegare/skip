<?php
	
	namespace Skip\Test;

	use Silex\Application;
	use Skip\ConfigLoader;


	class ConfigLoaderTest extends \PHPUnit_Framework_TestCase {

		public function getMockFinder($mockConfigContent) {

			$files = array();
			foreach( $mockConfigContent as $type => $config ) {
				$mockFile = $this->getMockBuilder('Symfony\Component\Finder\Finder\SplFileInfo')
					->disableOriginalConstructor()
					->setMethods(array('getContents', 'getExtension'))
					->getMock();
				$mockFile->expects($this->once())
					->method('getContents')
					->will($this->returnValue($config));
				$mockFile->expects($this->once())
					->method('getExtension')
					->will($this->returnValue($type));

				$files[] = $mockFile;
			}

			$filesIterator = new \ArrayIterator($files);
			$mockIterator = new \AppendIterator();
			$mockIterator->append($filesIterator);

			$mockFinder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
				->disableOriginalConstructor()
				->setMethods(array('files', 'getIterator', 'in', 'name'))
				->getMock();
			$mockFinder->expects($this->once())
				->method('files')
				->will($this->returnValue($mockFinder));
			$mockFinder->expects($this->any())
				->method('in')
				->will($this->returnValue($mockFinder));
			$mockFinder->expects($this->any())
				->method('any')
				->will($this->returnValue($mockFinder));
			$mockFinder->expects($this->once())
				->method('getIterator')
				->will($this->returnValue($mockIterator));

			return $mockFinder;
		}

		public function testLoad() {
			$mockConfigContent = $mockConfigContent = array(
				'json' => json_encode(array( "param1" => "value3")),
				'json' => json_encode(array( "param1" => "value1")),
				'json' => json_encode(array( "param1" => "value2"))
			);

			$mockFinder = $this->getMockFinder($mockConfigContent);

			$loader = new ConfigLoader(array('dir1', 'dir2'), $mockFinder);
			$loadedConfig = $loader->load();

			$this->assertArrayHasKey('param1', $loadedConfig);
			$this->assertEquals('value2', $loadedConfig['param1']);
		}

		public function testGetFinder() {
			$loader = new ConfigLoader(array());
			$this->assertInstanceOf('Symfony\Component\Finder\Finder', $loader->getFinder());
		}

		public function testConstantPlaceHolder() {
			$mockConfigContent = $mockConfigContent = array(
				'json' => json_encode(array(
					"param1" => "#CONSTANT#"
					)));

			$mockFinder = $this->getMockFinder($mockConfigContent);

			$loader = new ConfigLoader(array('dir1', 'dir2'), $mockFinder, array('CONSTANT' => 'value2'));
			$loadedConfig = $loader->load();

			$this->assertArrayHasKey('param1', $loadedConfig);
			$this->assertEquals('value2', $loadedConfig['param1']);
		}
		
		/**
		 * @expectedException Skip\InvalidConfigException
		 */
		public function testInvalidConfigException() {
			$mockConfigContent = $mockConfigContent = array(
				'json' => '{invalid:"config"}'
			);

			$mockFinder = $this->getMockFinder($mockConfigContent);

			$loader = new ConfigLoader(array('dir1'), $mockFinder);
			$loadedConfig = $loader->load();
		}
	}