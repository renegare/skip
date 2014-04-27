<?php

namespace Skip\ConfigLoader;
use Symfony\Component\Yaml\Parser;

class YamlLoader implements \Skip\AbstractConstantConfigLoaderInterface {
    /**
     * {@inheritdoc}
     */
    public function load($filePath) {
        $yaml = new Parser();
        return $yaml->parse(file_get_contents($filePath));
    }
}
