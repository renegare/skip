<?php

namespace Skip\ConfigLoader;
use Symfony\Component\Yaml\Parser;

class YamlLoader implements \Skip\AbstractConstantConfigLoaderInterface {
    /**
     * {@inheritdoc}
     */
    public function load($filePath) {
        $yaml = new Parser();
        $filePath = realPath($filePath);
        if(!$filePath) {
            throw new \InvalidArgumentException(sprintf('Constant File Does not Exist! %s', $filePath));
        }
        return $yaml->parse(file_get_contents($filePath));
    }
}
