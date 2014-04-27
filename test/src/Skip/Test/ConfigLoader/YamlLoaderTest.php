<?php

namespace Skip\Test\ConfigLoader;

use Silex\Application;
use Skip\ConfigLoader\YamlLoader;


class YamlLoaderTest extends \PHPUnit_Framework_TestCase {

    public function testLoadExistingFile() {
        $loader = new YamlLoader();
        $config = $loader->load('.travis.yml');
        $this->assertArrayHasKey('language', $config);
        $this->assertEquals('php', $config['language']);
    }
}
