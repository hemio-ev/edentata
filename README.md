Edentata
========

A toothless frontend for the hosting-service management backend [carnivora](https://git.hemio.de/hemio/edentata).

### Available on Packagist

[![Latest Stable
Version](https://poser.pugx.org/hemio/edentata/version)](https://packagist.org/packages/hemio/edentata)
[![Total
Downloads](https://poser.pugx.org/hemio/edentata/downloads)](https://packagist.org/packages/hemio/edentata)
[![Reference
Status](https://www.versioneye.com/php/hemio:edentata/reference_badge.svg?style=flat)](https://www.versioneye.com/php/hemio:edentata/references)

### Tests and Code Quality

[![Build
Status](https://travis-ci.org/qua-bla/edentata.svg?branch=master)](https://travis-ci.org/qua-bla/edentata)
[![Dependency
Status](https://www.versioneye.com/php/hemio:edentata/badge.svg)](https://www.versioneye.com/php/hemio:edentata/)

### Requiremenets

Required Debian packages

-   cracklib-runtime
-   gettext
-   make
-   php (>= 5.6)
-   php-intl
-   php-mbstring
-   php-pgsql

Further dependencies are managed via
[composer](https://getcomposer.org/) (only packaged starting with
Debian stretch) and are configure in *composer.yaml*.

### Installation

-   Run `composer install` to install dependencies
-   Run `make` to build localizations
-   Based on the *examples/config.yaml* create a config at
    */etc/edentata/config.yaml*
-   Configure your webserver to serve *src/htdocs/index.php* as default
    (see *examples/apache2.conf*)

