README
======

What is Skip?
-------------

Skip is a configuration wrapper around Silex (PHP microframework). The aim is to 'skip' the manual setup/configuration of silex and put all that stuff in a json file somewhere.


Configuration Filetype Support
------------------------------

Currently skip only supports *.json files. I see no reason why it could not support .ini, yml or event xml!?


Supported Silex Configuration
-----------------------------

Silex Application has various methods you can call to create the application you desire. Skip only currently supports the following (in the respective order):

* Providers
* Settings
* Routes

I am keen to implement the following:

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


