README
======

What is Skip?
-------------

Skip is a configuration wrapper around Silex (PHP microframework). The aim is to 'skip' the manual setup/configuration of silex and put all that stuff in a json file somewhere.


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

Usage
-----

Traditionally to start a Silex application you would do the following:

```
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

// definitions (that gets overly bloated as your app grows)

$app->run();

```

Now you can do this:

```
require_once __DIR__.'/../vendor/autoload.php';

$configPaths(__DIR__ . '/config', __DIR__ . '/dev/config', __DIR__ . '/live/config');
$loader = new Skip\ConfigLoader($configPaths);
$app = new Skip\WebApplication();
$app->setConfigLoader($this->getConfigLoader());
$app->configure();

$app->run();

```

The example above will find all the ```*.json``` files in the directories set within ```$configPaths``` and merge them into one huge configuration files and in that order.

Then ```$app->configure()``` will go through the configuration and configure your $app.

Browse the code to see what is possible as it really is dead simple ... no huge learning curve. Hopefully it will get you up and running quickly :).


Configuration Filetype Support
------------------------------

Currently skip only supports *.json files. I see no reason why it could not support .ini, yml or even xml!?


Supported Silex Configuration
-----------------------------

Silex Application has various 'features' you can configure to create the application you desire. Skip only currently supports and configures the following (in the respective order):

# Providers
# Settings
# Routes

I am keen to implement the following in the future (I tend to create Service Providers):

* Services
* Configuration placeholders (starts to get messy!)

After that I think it would be pretty comprehensive and need not get more complicated than that (as this is only a framework wrapper ... not a framework).

Contributions/Pull Requests/Forks are welcome. Enjoy!


Testing
-------

```[project root]$ composer update && phpunit```


TODOS (That I can think of)
---------------------------

* Support for other appropriate config file types
* Simple config level cache mechanism
* Documentation with examples
* Cover all the various ways silex can be configured


