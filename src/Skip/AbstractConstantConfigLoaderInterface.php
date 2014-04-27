<?php

namespace Skip;

interface AbstractConstantConfigLoaderInterface {
    /**
     * load a config file
     *
     * @param mixed - returns null if the file cannot be found
     * @throws Exception if the file contents are not of the correct format
     */
    public function load($filePath);
}
