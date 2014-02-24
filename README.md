SKIP README
===========

[![Build Status](https://travis-ci.org/renegare/skip.png?branch=master)](https://travis-ci.org/renegare/skip) on Master

[![Build Status](https://travis-ci.org/renegare/skip.png?branch=development)](https://travis-ci.org/renegare/skip) on Development


What is Skip?
-------------

Skip is a configuration wrapper around [Silex (PHP microframework)][1] and [Symfony Console Component][2]. 

The idea behind wrapping these two libraries is provide a starting point for new projects that require a *simple* web app and a cli interface.

The aim/goal however is to 'skip' the manual setup/configuration of these libraries (amazing tools but annoying learning curve) and put all that stuff in a json file somewhere. I hope this will allow developers to focus better on their application development.


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

*NOTE:* The git flow I use, ensures that the ```master``` branch is always stable (or as I see fit). The ```development``` branch is bleeding edge, but could contain bugs! Commits merged to master are tagged so you can lock down to a particular version :).


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

Now you can do this (dont be fooled, it is less PHP code):

```
<?php
require_once __DIR__.'/../vendor/autoload.php';

$configPaths = array(__DIR__ . '/config', __DIR__ . '/dev/config', __DIR__ . '/live/config');
$loader = new Skip\ConfigLoader($configPaths);
$app = new Skip\WebApplication();
$app->setConfigLoader($this->getConfigLoader());
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
require_once __DIR__.'/../vendor/autoload.php';

$app = new Skip\ConsoleApplication();
$app->setConfigLoaderCallback(function(InputInterface $input, $env, $devUser) {
	// based on the params passed in, you decide what configuration is loaded e.g console specific stuff?
	$configPaths = array(__DIR__ . '/config', __DIR__ . '/dev/config', __DIR__ . '/live/config');
	$loader = new Skip\ConfigLoader($configPaths);
	return 
});
$app->configure();
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

I am keen to implement the following in the future:

* Configuration placeholders (starts to get messy!)

After that I think it would be pretty comprehensive and need not get more complicated than that (as this is only a framework wrapper ... not a framework).

Contributions/Pull Requests/Forks are welcome. Enjoy!


Supported Symfony Console Configuration
-----------------------------

Console Application has various 'features' you can configure to create the application you desire. Skip only currently supports and configures the following:

1. Commands

In addition to this, Skip provides the interface ```ContainerInterface```. Implementing this into your commands is recommended so you have access to the DI container (basically the web application) in your commands.

*WARNING:* Keep your commands as lite/thin as possible. #IMO they should be like controllers where the heavy lifting (business/core logic) is implemented as a service. This keeps the your application code portable/resuable/decoupled. Your commands will also be better understood in 6 months from now ;)

Contributions/Pull Requests/Forks are welcome. Enjoy!


Testing
-------

```[project root]$ composer update && /vendor/bin/phpunit --coverage-text```


TODOS (That I can think of)
---------------------------

- [ ] Support for other appropriate config file types
- [ ] Simple config level cache mechanism
- [ ] Documentation with examples (Please see ```test/src/Skip/Test``` for examples)
- [ ] Cover all the various ways silex can be configured (low priority)
- [ ] Cover all the various ways symfony console component can be configured (low priority)

[1]: http://silex.sensiolabs.org/doc/usage.html
[2]: http://symfony.com/doc/current/components/console/introduction.html