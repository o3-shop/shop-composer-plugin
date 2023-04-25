O3-Shop composer plugin
==========================

.. image:: https://app.travis-ci.com/o3-shop/shop_composer_plugin.svg?branch=master
  :target: https://app.travis-ci.com/o3-shop/shop_composer_plugin

.. image:: https://img.shields.io/packagist/v/o3-shop/shop-composer-plugin.svg?maxAge=3600
  :target: https://packagist.org/packages/o3-shop/shop-composer-plugin

This plugin is used to install O3-Shop and O3-Shop third party integrations (modules, themes).

More information how to install O3-Shop using this plugin can be found `here <https://docs.o3-shop.com/developer/en/6.1/getting_started/installation/index.html>`__.

Supported types
---------------

Packages are recognised by their type, specified in composer.json file.
Available types are:

- oxideshop - Main shop package is installed into source directory.
- oxideshop-module - Modules, which are installed into source directory. Modules depends on main shop package.
- oxideshop-theme - Themes, which are installed into source directory. Themes depends on main shop package.

More information how to create module installable via composer: https://docs.o3-shop.com/developer/en/6.1/modules/skeleton/composerjson/module_via_composer.html

More information how to create themes installable via composer: https://docs.o3-shop.com/developer/en/6.1/themes/theme_via_composer.html

Requirements
------------

* master branch is compatible with O3-Shop compilation master
* b-1.x branch is compatible with O3-Shop compilation 1.x (>=1.0.x)

Bugs and Issues
---------------

If you experience any bugs or issues, please report them in the section **O3-Shop (all versions)** of https://bugs.o3-shop.com.
