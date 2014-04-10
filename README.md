Skip README
===========

[![Build Status](https://travis-ci.org/renegare/skip.png?branch=master)](https://travis-ci.org/renegare/skip) on Master

[![Build Status](https://travis-ci.org/renegare/skip.png?branch=development)](https://travis-ci.org/renegare/skip) on Development


What is Skip?
-------------

Skip is a configuration wrapper around [Silex (PHP microframework)][1] and [Symfony Console Component][2].

The idea behind wrapping these two libraries is provide a starting point for new projects that require a *simple* web app and a cli interface.

The aim/goal however is to 'skip' the manual setup/configuration of these libraries (amazing tools but annoying learning curve) and put all that stuff in a json file somewhere. I hope this will allow developers to focus better on their application development.


Requirements
------------

* PHP 5.4
* composer (preferably latest)

Installation
------------

The recommened way is to use composer to install skip in your project:
```
"require": {
    ...
    "renegare/skip": "master-dev"
    ...
}
```

Note: you can require your own version of silex, as long as it is greater than the minimalist defined version in composer.json.

Test
----

Check out the repo and from the top level directory run the following command:
```
$ composer update && vendor/bin/phpunit
```

*NOTE:* You need composer installed on your machine


Web Application Usage
---------------------

Traditionally to start a Silex application you would do the following:

```
<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

// definitions (that gets overly bloated as your app grows)

$app->run();

```

Now you can do this:

```
<?php
require_once __DIR__.'/../vendor/autoload.php';

$configPaths = array(__DIR__ . '/config', __DIR__ . '/dev/config', __DIR__ . '/live/config');

// used to replace 'placeholders' in you configuration (e.g "param1": "#RANDOM_ENV_VAR_1#/goes/here")
$placeholders = array('RANDOM_ENV_VAR_1' => 'something', 'RANDOM_ENV_VAR_2' => '2');
$loader = new Skip\ConfigLoader($configPaths, null, $placeholders);

$app = new Skip\WebApplication();
$app->setConfigLoader($loader);
$app->configure();
$app->run();
```

The example above will find all the ```*.json``` files in the directories set within ```$configPaths``` and merge them into one huge configuration files and in that order.

Then ```$app->configure()``` will go through the configuration and configure your $app.

Browse the code to see what is possible as it really is dead simple ... no huge learning curve. Hopefully it will get you up and running quickly :).

*NOTE:* Please look through at the test ```WebApplicationTest``` for configuration specifics.


Console Application Usage
-------------------------

Create a file with the following code in-place:
```
#!/usr/bin/env php
<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new Skip\ConsoleApplication();
$app->setConfigLoaderCallback(function(Symfony\Component\Console\Input\InputInterface $input, $env, $devUser) {
    $configPaths = array(__DIR__ . '/config', __DIR__ . '/dev/config', __DIR__ . '/live/config');

    $placeholders = array('RANDOM_ENV_VAR_1' => 'something', 'RANDOM_ENV_VAR_2' => '2');
    $loader = new Skip\ConfigLoader($configPaths, null, $placeholders);

    return $loader;
});
$app->run();
```

Ensure your file is executable and all should work as expected.

*Note:* Please look through at the test ```ConsoleApplicationTest``` for configuration specifics.


Configuration Filetype Support
------------------------------

Currently skip only supports *.json files. I see no reason why it could not support .ini, yml or even xml!?


Supported Silex Configuration
-----------------------------

Silex Application has various 'features' you can configure to create the application you desire. Skip only currently supports and configures the following (in the respective order):

1. Providers
2. Settings
3. Routes
4. Services
5. Configuration placeholders

*@TODO:* Need to add some additional support for traits with skip (currently possible but not elegantly)

Contributions/Pull Requests/Forks are welcome. Enjoy!


Supported Symfony Console Configuration
-----------------------------

Console Application has various 'features' you can configure to create the application you desire. Skip only currently supports and configures the following:

1. Commands

In addition to this, Skip provides the interface ```ContainerInterface```. Implementing this into your commands is recommended so you have access to the DI container (basically the web application) in your commands.

*WARNING:* Keep your commands as lite/thin as possible. #IMO they should be like controllers where all heavy lifting (business/core logic) are implemented as a services. This keeps the your application code portable/resuable/decoupled/testable. Your commands will also be better understood in 6 months from now ;)

Contributions/Pull Requests/Forks are welcome. Enjoy!


TODOS (That I can think of)
---------------------------

- [ ] Support for other appropriate config file types
- [ ] Simple config level cache mechanism (e.g APC)
- [ ] Documentation with examples (Please see ```test/src/Skip/Test``` for examples)

[1]: http://silex.sensiolabs.org/doc/usage.html
[2]: http://symfony.com/doc/current/components/console/introduction.html
