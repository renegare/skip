<?php

    namespace Skip\Test;

    use Silex\Application;
    use Skip\ConfigLoader;


    class ConfigLoaderTest extends \PHPUnit_Framework_TestCase {

        public function getMockFinder($mockConfigContent = array()) {

            $files = array();
            foreach( $mockConfigContent as $type => $config ) {
                $mockFile = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
                    ->disableOriginalConstructor()
                    ->setMethods(array('getContents', 'getExtension'))
                    ->getMock();
                $mockFile->expects($this->any())
                    ->method('getContents')
                    ->will($this->returnValue($config));
                $mockFile->expects($this->any())
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
            $mockFinder->expects($this->any())
                ->method('files')
                ->will($this->returnValue($mockFinder));
            $mockFinder->expects($this->any())
                ->method('in')
                ->will($this->returnValue($mockFinder));
            $mockFinder->expects($this->any())
                ->method('any')
                ->will($this->returnValue($mockFinder));
            $mockFinder->expects($this->any())
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

        /**
        * test loading a yaml constant file
        */
        public function testConstantLoading() {
            $randomValue = rand(0, 1000);
            $explicitConstant = 'explicit-' . $randomValue;
            $file1Constant = 'file1-' . $randomValue;
            $file2Constant = 'file2-' . $randomValue;

            $mockConfig = array(
                'json' => json_encode([
                    'EXPLICIT_CONSTANT' => '#EXPLICIT_CONSTANT#',
                    'FILE_CONSTANT_1' => '#FILE_CONSTANT_1#',
                    'FILE_CONSTANT_2' => '#FILE_CONSTANT_2#',
                ]));
            $mockFinder = $this->getMockFinder($mockConfig);

            $mockConstantLoader = $this->getMockBuilder('Skip\AbstractConstantConfigLoaderInterface')
                ->disableOriginalConstructor()
                ->getMock();
            $mockConstantLoader->expects($this->exactly(2))
                ->method('load')
                ->will($this->returnCallback(function($filePath) use ($file1Constant, $file2Constant){
                    switch($filePath) {
                        case 'app-constants.yml':
                            return ['FILE_CONSTANT_1' => $file1Constant];
                        case 'app-constants.yml.dist':
                            return [
                                'FILE_CONSTANT_1' => 'THIS_VALUE_SHOULD_BE_OVERWRITTEN',
                                'FILE_CONSTANT_2' => $file2Constant
                            ];
                    }
                    throw new \Exception('This file does not exist!');
                }));

            $loader = new ConfigLoader(array('dir1'), $mockFinder);
            $loader->setConstantLoader($mockConstantLoader);
            $loader->setConstants(
                ['EXPLICIT_CONSTANT' => $explicitConstant],
                'app-constants.yml',
                'app-constants.yml.dist');
            $config = $loader->load();

            $this->assertEquals(array(
                'EXPLICIT_CONSTANT' => $explicitConstant,
                'FILE_CONSTANT_1' => $file1Constant,
                'FILE_CONSTANT_2' => $file2Constant
            ), $config);
        }

        public function testYamlConstantLoaderIsUsedByDefault() {
            $mockFinder = $this->getMockFinder();
            $loader = new ConfigLoader(array('dir1'), $mockFinder);
            $yamlLoader = $loader->getConstantLoader();
            $this->assertInstanceOf('Skip\AbstractConstantConfigLoaderInterface', $yamlLoader);
            $this->assertInstanceOf('Skip\ConfigLoader\YamlLoader', $yamlLoader);
        }
    }
